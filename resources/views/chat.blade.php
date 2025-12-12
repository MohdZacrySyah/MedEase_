<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat MedEase</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        .chat-container { max-width: 600px; margin: 20px auto; border: 1px solid #ddd; padding: 20px; border-radius: 10px; }
        .chat-box { height: 400px; overflow-y: scroll; border-bottom: 1px solid #eee; margin-bottom: 20px; padding: 10px; }
        .message { padding: 10px; margin: 5px; border-radius: 5px; max-width: 70%; }
        .my-message { background-color: #d1e7dd; margin-left: auto; text-align: right; }
        .other-message { background-color: #f8f9fa; margin-right: auto; text-align: left; }
    </style>
</head>
<body>

<div class="chat-container">
    <h3>Chat dengan {{ $receiver->name }}</h3>
    
    <div id="chat-box" class="chat-box">
        @foreach($messages as $msg)
            <div class="message {{ $msg->sender_id == auth()->id() ? 'my-message' : 'other-message' }}">
                <small>{{ $msg->sender_id == auth()->id() ? 'Saya' : $receiver->name }}</small><br>
                {{ $msg->message }}
            </div>
        @endforeach
    </div>

    <div style="display: flex;">
        <input type="text" id="messageInput" placeholder="Ketik pesan..." style="flex: 1; padding: 10px;">
        <button onclick="sendMessage()" style="padding: 10px; background: blue; color: white; border: none;">Kirim</button>
    </div>
</div>

<script type="module">
    // ID User yang sedang login (Saya)
    const myId = "{{ auth()->id() }}";
    const receiverId = "{{ $receiver->id }}";

    // 1. MENDENGARKAN PESAN MASUK (REALTIME)
    window.Echo.private('chat.' + myId)
        .listen('MessageSent', (e) => {
            console.log("Pesan diterima:", e.message);
            
            // Cek apakah pesan ini dari orang yang sedang kita chat?
            if(e.message.sender_id == receiverId) {
                const chatBox = document.getElementById('chat-box');
                chatBox.innerHTML += `
                    <div class="message other-message">
                        <small>{{ $receiver->name }}</small><br>
                        ${e.message.message}
                    </div>
                `;
                chatBox.scrollTop = chatBox.scrollHeight; // Auto scroll ke bawah
            }
        });

    // 2. FUNGSI KIRIM PESAN
    window.sendMessage = function() {
        const input = document.getElementById('messageInput');
        const message = input.value;
        
        if(message.trim() === "") return;

        // Tampilkan pesan sendiri langsung (biar terasa cepat)
        const chatBox = document.getElementById('chat-box');
        chatBox.innerHTML += `
            <div class="message my-message">
                <small>Saya</small><br>
                ${message}
            </div>
        `;
        chatBox.scrollTop = chatBox.scrollHeight;
        input.value = '';

        // Kirim ke database via API
        axios.post('/chat/send', {
            receiver_id: receiverId,
            message: message
        });
    }
</script>
</body>
</html>