@extends('layouts.main')

@section('content')
<div class="container-fluid" style="height: 85vh;"> <div class="row h-100">
        <div class="col-md-4 border-end h-100 overflow-auto">
            <div class="p-3">
                <h5>{{ $myRole == 'pasien' ? 'Daftar Tenaga Medis' : 'Daftar Pasien' }}</h5>
                
                <form method="GET" action="{{ route('chat.index') }}" class="mb-3">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Cari..." value="{{ $search ?? '' }}">
                </form>

                <div class="list-group">
                    @forelse($contacts as $contact)
                        <a href="{{ route('chat.index', ['partnerId' => $contact->id]) }}" 
                           class="list-group-item list-group-item-action {{ $partnerId == $contact->id ? 'active' : '' }}">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">{{ $contact->display_name }}</h6>
                                <small class="{{ $partnerId == $contact->id ? 'text-white' : 'text-muted' }}">
                                    {{ $contact->last_time }}
                                </small>
                            </div>
                            <p class="mb-1 small {{ $partnerId == $contact->id ? 'text-white' : 'text-muted' }}">
                                {{ Str::limit($contact->last_message, 30) }}
                            </p>
                        </a>
                    @empty
                        <div class="text-center text-muted py-3">
                            Belum ada kontak tersedia.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-md-8 h-100 d-flex flex-column">
            @if($partner)
                <div class="border-bottom p-3 bg-light d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">{{ $partner->display_name }}</h5>
                    <span class="badge bg-success">Online</span> </div>

                <div id="chat-messages" class="p-3 flex-grow-1 overflow-auto bg-white">
                    @foreach($messages as $message)
                        {{-- LOGIKA: Cek apakah pesan ini dari SAYA --}}
                        @php
                            $isMe = false;
                            if ($myRole == 'pasien' && $message->sender_type == 'user' && $message->sender_id == Auth::id()) {
                                $isMe = true;
                            } elseif ($myRole == 'medis' && $message->sender_type == 'medis' && $message->sender_id == Auth::id()) {
                                $isMe = true;
                            }
                        @endphp
                        @php $isMe = $message->sender_id == Auth::id() && $message->sender_type == ($myRole == 'pasien' ? 'App\\Models\\User' : 'App\\Models\\TenagaMedis'); @endphp

                        <div class="mb-3 {{ $isMe ? 'text-end' : 'text-start' }}">
                            <div class="d-inline-block p-2 rounded {{ $isMe ? 'bg-primary text-white' : 'bg-light border' }}" 
                                 style="max-width: 70%; text-align: left;">
                                {{ $message->message }}
                                <div class="small mt-1 {{ $isMe ? 'text-white-50' : 'text-muted' }}" 
                                     style="font-size: 0.7rem; text-align: right;">
                                    {{ $message->created_at->format('H:i') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="border-top p-3 bg-light">
                    <form id="chat-form" class="d-flex gap-2">
                        @csrf
                        <input type="hidden" id="receiver-id" value="{{ $partnerId }}">
                        <input type="text" id="message-input" class="form-control" 
                               placeholder="Ketik pesan..." required autocomplete="off">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send"></i> Kirim
                        </button>
                    </form>
                </div>
            @else
                <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                    <div class="text-center">
                        <h4 class="text-muted">Selamat Datang di MedEase Chat</h4>
                        <p class="text-muted">Pilih tenaga medis atau pasien untuk memulai konsultasi.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Scroll otomatis ke bawah saat halaman dimuat
    const chatContainer = document.getElementById('chat-messages');
    if (chatContainer) {
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }

    // Handle Kirim Pesan
    document.getElementById('chat-form')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const messageInput = document.getElementById('message-input');
        const message = messageInput.value;
        const receiverId = document.getElementById('receiver-id').value;
        
        if(!message.trim()) return; // Jangan kirim pesan kosong

        fetch('{{ route("chat.send") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                message: message,
                receiver_id: receiverId
            })
        })
        .then(response => response.json())
        .then(data => {
            messageInput.value = '';
            location.reload(); // Reload sementara (nanti diganti WebSocket/Pusher)
        })
        .catch(error => console.error('Error:', error));
    });
</script>
@endpush
@endsection