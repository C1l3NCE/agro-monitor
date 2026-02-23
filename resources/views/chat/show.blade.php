@extends('layouts.admin')

@section('header', 'Сообщения')

@section('content')

    @php
        $authId = auth()->id();
        $other = $conversation->otherUser($authId);
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

        <!-- ЛЕВАЯ ПАНЕЛЬ -->
        <div class="bg-white rounded-3xl shadow-lg border border-gray-200 p-6">

            <h2 class="text-lg font-semibold text-gray-800 mb-6">
                Диалоги
            </h2>

            <div class="space-y-3">

                @foreach($conversations as $conv)

                        @php
                            $otherUser = $conv->otherUser($authId);
                            $isActive = $conv->id === $conversation->id;
                            $lastMessage = $conv->messages->first();
                        @endphp

                        <a href="{{ route('chat.show', $conv) }}" class="block p-4 rounded-xl border transition
                               {{ $isActive
                    ? 'border-green-400 bg-green-50'
                    : 'border-gray-100 hover:bg-gray-50' }}">

                            <div class="flex justify-between items-start">

                                <div class="flex-1">

                                    <div class="font-medium text-gray-800">
                                        {{ $otherUser->name }}
                                    </div>

                                    @if($lastMessage)
                                        <div class="text-xs text-gray-500 truncate mt-1">
                                            {{ $lastMessage->user_id === $authId ? 'Вы: ' : '' }}
                                            {{ $lastMessage->message }}
                                        </div>
                                    @endif

                                </div>

                                @if($lastMessage)
                                    <div class="text-xs text-gray-400 ml-3">
                                        {{ $lastMessage->created_at->format('H:i') }}
                                    </div>
                                @endif

                            </div>

                            @if($conv->unread_count > 0)
                                <div class="mt-2 text-right">
                                    <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                                        {{ $conv->unread_count }}
                                    </span>
                                </div>
                            @endif

                        </a>

                @endforeach

            </div>
        </div>


        <!-- ПРАВАЯ ЧАСТЬ -->
        <div class="md:col-span-2">

            <div class="bg-white rounded-3xl shadow-lg border border-gray-200 flex flex-col h-[600px]">

                <!-- HEADER -->
                <div class="p-6 border-b border-gray-100">
                    <div class="font-semibold text-gray-800">
                        {{ $other->name }}
                    </div>
                    <div class="text-xs text-gray-500">
                        {{ ucfirst($other->role) }}
                    </div>
                </div>

                <!-- MESSAGES -->
                <div id="messages" class="flex-1 overflow-y-auto p-6 space-y-4">

                    @foreach($conversation->messages as $message)

                                <div class="flex {{ $message->user_id == $authId ? 'justify-end' : 'justify-start' }}">

                                    <div class="max-w-xs px-4 py-2 rounded-2xl
                                            {{ $message->user_id == $authId
                        ? 'bg-green-500 text-white'
                        : 'bg-gray-100 text-gray-800' }}">

                                        <div class="text-sm">
                                            {{ $message->message }}
                                        </div>

                                        <div class="text-xs mt-1 opacity-70">
                                            {{ $message->created_at->format('H:i') }}
                                        </div>

                                    </div>

                                </div>

                    @endforeach

                </div>

                <!-- INPUT -->
                <form method="POST" action="{{ route('chat.send', $conversation) }}"
                    class="p-4 border-t border-gray-100 flex gap-3">
                    @csrf

                    <input name="message" class="flex-1 px-4 py-2 rounded-xl border border-gray-200
                                  focus:ring-2 focus:ring-green-400" placeholder="Введите сообщение..." required>

                    <button class="px-6 py-2 rounded-xl
                                   bg-gradient-to-r from-green-500 to-green-600
                                   text-white font-semibold
                                   hover:from-green-600 hover:to-green-700
                                   transition">
                        Отправить
                    </button>
                </form>

            </div>

        </div>
    </div>


    <script>
        const container = document.getElementById('messages');
        container.scrollTop = container.scrollHeight;

        let lastMessageId = {{ $conversation->messages->last()?->id ?? 0 }};

        function fetchMessages() {
            fetch("{{ route('chat.fetch', $conversation) }}?last_id=" + lastMessageId)
                .then(response => response.json())
                .then(messages => {

                    if (messages.length === 0) return;

                    messages.forEach(message => {

                        const isMine = message.user_id === {{ $authId }};

                        const wrapper = document.createElement('div');
                        wrapper.className = 'flex ' + (isMine ? 'justify-end' : 'justify-start');

                        const bubble = document.createElement('div');
                        bubble.className = 'max-w-xs px-4 py-2 rounded-2xl ' +
                            (isMine
                                ? 'bg-green-500 text-white'
                                : 'bg-gray-100 text-gray-800');

                        bubble.innerHTML = `
                            <div class="text-sm">${message.message}</div>
                            <div class="text-xs mt-1 opacity-70">
                                ${new Date(message.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                            </div>
                        `;

                        if (!isMine) {
                            showNotification("Новое сообщение");
                        }

                        wrapper.appendChild(bubble);
                        container.appendChild(wrapper);

                        lastMessageId = message.id;
                    });

                    container.scrollTop = container.scrollHeight;
                });
        }

        setInterval(fetchMessages, 3000);

        function showNotification(text) {
            const notification = document.createElement('div');

            notification.className =
                "fixed bottom-6 right-6 bg-gray-900 text-white px-6 py-3 rounded-xl shadow-lg animate-bounce";

            notification.innerText = text;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    </script>

@endsection