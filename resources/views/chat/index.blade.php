@extends('layouts.admin')

@section('header', 'Сообщения')

@section('content')

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

        <!-- ЛЕВАЯ КОЛОНКА -->
        <div class="bg-white rounded-3xl shadow-lg border border-gray-200 p-6">

            <h2 class="text-lg font-semibold text-gray-800 mb-6">
                Диалоги
            </h2>

            @forelse($conversations as $conversation)

                @php
                    $other = $conversation->otherUser(auth()->id());
                @endphp

                <a href="{{ route('chat.show', $conversation) }}" class="block p-4 rounded-xl border border-gray-100
                  hover:bg-gray-50 transition mb-3">

                    @php
                        $lastMessage = $conversation->messages->first();
                    @endphp

                    <div class="flex justify-between items-start">

                        <div class="flex-1">

                            <div class="font-medium text-gray-800">
                                {{ $other->name }}
                            </div>

                            @if($lastMessage)
                                <div class="text-xs text-gray-500 truncate mt-1">
                                    {{ $lastMessage->user_id === auth()->id() ? 'Вы: ' : '' }}
                                    {{ $lastMessage->message }}
                                </div>
                            @endif

                        </div>

                        <div class="text-xs text-gray-400 ml-3">
                            @if($lastMessage)
                                {{ $lastMessage->created_at->format('H:i') }}
                            @endif
                        </div>

                    </div>

                    @if($conversation->unread_count > 0)
                        <div class="mt-2 text-right">
                            <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                                {{ $conversation->unread_count }}
                            </span>
                        </div>
                    @endif

                </a>


            @empty

                <p class="text-sm text-gray-400">
                    Пока нет диалогов
                </p>

            @endforelse

        </div>


        <!-- ПРАВАЯ КОЛОНКА -->
        <div class="md:col-span-2 bg-white rounded-3xl shadow-lg border border-gray-200 p-6">

            <h2 class="text-lg font-semibold text-gray-800 mb-6">
                Начать новый чат
            </h2>

            <div class="space-y-3">

                @foreach($availableUsers as $user)

                    <form method="POST" action="{{ route('chat.start', $user) }}">
                        @csrf

                        <button class="w-full text-left p-4 rounded-xl
                                                   border border-gray-100
                                                   hover:bg-green-50 hover:border-green-300
                                                   transition">

                            <div class="font-medium text-gray-800">
                                {{ $user->name }}
                            </div>

                            <div class="text-xs text-gray-500">
                                {{ ucfirst($user->role) }}
                            </div>

                        </button>

                    </form>

                @endforeach

            </div>

        </div>

    </div>

@endsection