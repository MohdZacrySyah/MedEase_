<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;           
use App\Models\TenagaMedis;    
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    /**
     * Helper untuk mendapatkan user yang sedang login dari guard manapun.
     */
    private function getAuthenticatedUser()
    {
        if (Auth::guard('web')->check()) {
            return Auth::guard('web')->user();
        }
        if (Auth::guard('tenaga_medis')->check()) {
            return Auth::guard('tenaga_medis')->user();
        }
        return null;
    }

    public function index($partnerId = null)
    {
        $user = $this->getAuthenticatedUser();

        if (!$user) {
            // Redirect sesuai guard jika belum login
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // 1. DETEKSI SIAPA YANG LOGIN & TENTUKAN LAYOUT
        $layout = 'layouts.main'; // Default (Pasien)

        if ($user instanceof \App\Models\User) {
            $myRole = 'pasien';
            $myType = 'user';       
            $targetModel = \App\Models\TenagaMedis::class;
            $targetType = 'medis'; 
            $myId = $user->id; 
            $layout = 'layouts.main'; 
        } else {
            // Tenaga Medis
            $myRole = 'medis';
            $myType = 'medis';
            $targetModel = \App\Models\User::class;
            $targetType = 'user';
            $myId = $user->id; 
            $layout = 'layouts.tenaga_medis'; // Layout untuk Tenaga Medis
        }

        // 2. AMBIL DAFTAR KONTAK (SIDEBAR)
        $contacts = $targetModel::all()->map(function($contact) use ($myId, $myType, $targetType) {
            // Ambil pesan terakhir
            $lastMsg = Message::where(function($q) use ($myId, $contact, $myType, $targetType) {
                $q->where('sender_id', $myId)->where('sender_type', $myType)
                  ->where('receiver_id', $contact->id)->where('receiver_type', $targetType);
            })->orWhere(function($q) use ($myId, $contact, $myType, $targetType) {
                $q->where('sender_id', $contact->id)->where('sender_type', $targetType)
                  ->where('receiver_id', $myId)->where('receiver_type', $myType);
            })->latest()->first();

            $contact->display_name = $contact->name ?? $contact->nama; 
            
            if ($lastMsg) {
                if ($lastMsg->message) {
                    $contact->last_message = $lastMsg->message;
                } elseif ($lastMsg->media_path) {
                    $contact->last_message = '📎 [Melampirkan File]';
                } else {
                    $contact->last_message = '';
                }
                $contact->last_time = $lastMsg->created_at->format('H:i');
            } else {
                $contact->last_message = '';
                $contact->last_time = '';
            }
            
            return $contact;
        });

        // 3. AMBIL ISI CHAT
        $messages = [];
        $partner = null;

        if ($partnerId) {
            $partner = $targetModel::find($partnerId);
            
            if ($partner) {
                $partner->display_name = $partner->name ?? $partner->nama;

                // Query Chat
                $messages = Message::where(function($q) use ($myId, $partnerId, $myType, $targetType) {
                    $q->where('sender_id', $myId)->where('sender_type', $myType)
                      ->where('receiver_id', $partnerId)->where('receiver_type', $targetType);
                })->orWhere(function($q) use ($myId, $partnerId, $myType, $targetType) {
                    $q->where('sender_id', $partnerId)->where('sender_type', $targetType)
                      ->where('receiver_id', $myId)->where('receiver_type', $myType);
                })->orderBy('created_at', 'asc')->get();
            }
        }

        return view('chat.index', [
            'contacts' => $contacts,
            'messages' => $messages,
            'partner' => $partner,
            'partnerId' => $partnerId,
            'myRole' => $myRole, 
            'myType' => $myType, 
            'myId' => $myId, // 🔥 PENTING: Kirim ID user yang login agar view tidak bingung
            'search' => request('search'),
            'layout' => $layout 
        ]);
    }

    // 🔥 FUNGSI KIRIM PESAN (+ MEDIA) 🔥
    public function sendMessage(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'message' => 'nullable|string',
            'receiver_id' => 'required|integer',
            'media' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx,mp4,mov|max:10240', 
        ]);

        if (!$request->message && !$request->hasFile('media')) {
             return response()->json(['status' => 'error', 'message' => 'Pesan atau media harus diisi.'], 400);
        }

        $user = $this->getAuthenticatedUser();
        
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
        }
        
        // Tentukan tipe pengirim & penerima
        if ($user instanceof \App\Models\User) {
            $senderType = 'user';
            $receiverType = 'medis';
        } else {
            $senderType = 'medis';
            $receiverType = 'user';
        }

        $mediaPath = null;
        $mediaType = null;
        
        // 2. Handle File Upload
        if ($request->hasFile('media')) {
            try {
                $file = $request->file('media');
                $mediaPath = $file->store('chat-media', 'public'); 
                $mediaType = $file->getClientMimeType();
            } catch (\Exception $e) {
                return response()->json(['status' => 'error', 'message' => 'Gagal upload file: ' . $e->getMessage()], 500);
            }
        }

        try {
            // 3. Simpan ke Database
            $message = Message::create([
                'sender_id' => $user->id,
                'sender_type' => $senderType,
                'receiver_id' => $request->receiver_id,
                'receiver_type' => $receiverType,
                'message' => $request->message ?? '',
                'is_read' => false,
                'media_path' => $mediaPath, 
                'media_type' => $mediaType, 
            ]);

            return response()->json([
                'status' => 'success', 
                'message_id' => $message->id,
                'media_path' => $mediaPath, 
                'media_type' => $mediaType
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Database Error: ' . $e->getMessage()], 500);
        }
    }
    
    // 🔥 FUNGSI TANDAI DIBACA 🔥
    public function markRead($partnerId)
    {
        $user = $this->getAuthenticatedUser();
        
        if (!$user) return response()->json(['status' => 'error'], 401);

        if ($user instanceof \App\Models\User) {
            $myType = 'user';
            $partnerType = 'medis';
        } else {
            $myType = 'medis';
            $partnerType = 'user';
        }

        // Update semua pesan dari partner ini yang belum dibaca
        Message::where('sender_id', $partnerId)
               ->where('sender_type', $partnerType)
               ->where('receiver_id', $user->id)
               ->where('receiver_type', $myType)
               ->where('is_read', false)
               ->update(['is_read' => true]);
               
        return response()->json(['status' => 'success']);
    }
    

public function getMessages(Request $request, $partnerId)
    {
        $user = $this->getAuthenticatedUser();
        if (!$user) return response()->json([], 401);

        // Tentukan role
        if ($user instanceof \App\Models\User) {
            $myType = 'user';
            $targetType = 'medis';
        } else {
            $myType = 'medis';
            $targetType = 'user';
        }
        $myId = $user->id;

        // Ambil ID pesan terakhir dari request client
        $lastId = $request->query('last_id', 0);

        // Ambil pesan BARU saja (id > last_id)
        $messages = Message::where('id', '>', $lastId)
            ->where(function($q) use ($myId, $partnerId, $myType, $targetType) {
                $q->where(function($sub) use ($myId, $partnerId, $myType, $targetType) {
                    $sub->where('sender_id', $myId)->where('sender_type', $myType)
                        ->where('receiver_id', $partnerId)->where('receiver_type', $targetType);
                })->orWhere(function($sub) use ($myId, $partnerId, $myType, $targetType) {
                    $sub->where('sender_id', $partnerId)->where('sender_type', $targetType)
                        ->where('receiver_id', $myId)->where('receiver_type', $myType);
                });
            })
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function($msg) {
                return [
                    'id' => $msg->id,
                    'message' => $msg->message,
                    'media_path' => $msg->media_path,
                    'media_type' => $msg->media_type,
                    'sender_id' => $msg->sender_id,
                    'sender_type' => $msg->sender_type,
                    'created_at_formatted' => $msg->created_at->format('H:i'),
                    'is_read' => $msg->is_read
                ];
            });

        return response()->json(['messages' => $messages]);
    }}