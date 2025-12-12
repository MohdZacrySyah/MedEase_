<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;           // Model Pasien
use App\Models\TenagaMedis;    // Model Dokter/Medis
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index($partnerId = null)
    {
        $user = Auth::user();

        // 1. DETEKSI SIAPA YANG LOGIN
        // Jika instance User, berarti Pasien. Jika bukan, berarti Tenaga Medis.
        if ($user instanceof \App\Models\User) {
            $myRole = 'pasien';
            $myType = 'user';       // Disimpan ke DB sebagai 'user'
            $targetModel = \App\Models\TenagaMedis::class;
            $targetType = 'medis';  // Lawan bicara adalah 'medis'
        } else {
            $myRole = 'medis';
            $myType = 'medis';
            $targetModel = \App\Models\User::class;
            $targetType = 'user';
        }

        // 2. AMBIL DAFTAR KONTAK (SIDEBAR)
        // Jika saya pasien, ambil semua tenaga medis. Jika saya medis, ambil semua pasien.
        $contacts = $targetModel::all()->map(function($contact) use ($user, $myType, $targetType) {
            
            // Ambil pesan terakhir untuk preview
            $lastMsg = Message::where(function($q) use ($user, $contact, $myType, $targetType) {
                $q->where('sender_id', $user->id)->where('sender_type', $myType)
                  ->where('receiver_id', $contact->id)->where('receiver_type', $targetType);
            })->orWhere(function($q) use ($user, $contact, $myType, $targetType) {
                $q->where('sender_id', $contact->id)->where('sender_type', $targetType)
                  ->where('receiver_id', $user->id)->where('receiver_type', $myType);
            })->latest()->first();

            // Mapping properti untuk View
            $contact->display_name = $contact->nama ?? $contact->name; // Sesuaikan dengan kolom DB kamu
            $contact->last_message = $lastMsg ? $lastMsg->message : '';
            $contact->last_time = $lastMsg ? $lastMsg->created_at->format('H:i') : '';
            
            return $contact;
        });

        // 3. AMBIL ISI CHAT (AREA KANAN)
        $messages = [];
        $partner = null;

        if ($partnerId) {
            $partner = $targetModel::find($partnerId);
            
            if ($partner) {
                $partner->display_name = $partner->nama ?? $partner->name;

                // Query Chat: Cocokkan ID DAN Tipe Pengirim/Penerima
                $messages = Message::where(function($q) use ($user, $partnerId, $myType, $targetType) {
                    $q->where('sender_id', $user->id)->where('sender_type', $myType)
                      ->where('receiver_id', $partnerId)->where('receiver_type', $targetType);
                })->orWhere(function($q) use ($user, $partnerId, $myType, $targetType) {
                    $q->where('sender_id', $partnerId)->where('sender_type', $targetType)
                      ->where('receiver_id', $user->id)->where('receiver_type', $myType);
                })->orderBy('created_at', 'asc')->get();
            }
        }

        return view('chat.index', [
            'contacts' => $contacts,
            'messages' => $messages,
            'partner' => $partner,
            'partnerId' => $partnerId,
            'myRole' => $myRole, // 'pasien' atau 'medis'
            'search' => request('search')
        ]);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'receiver_id' => 'required|integer'
        ]);

        $user = Auth::user();

        // Tentukan ulang tipe pengirim & penerima
        if ($user instanceof \App\Models\User) {
            $senderType = 'user';
            $receiverType = 'medis';
        } else {
            $senderType = 'medis';
            $receiverType = 'user';
        }

        Message::create([
            'sender_id' => $user->id,
            'sender_type' => $senderType,
            'receiver_id' => $request->receiver_id,
            'receiver_type' => $receiverType,
            'message' => $request->message,
            'is_read' => false
        ]);

        return response()->json(['status' => 'success']);
    }
}