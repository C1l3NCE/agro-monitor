<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    // Список диалогов
    public function index()
    {
        $user = auth()->user();

        $conversations = Conversation::where('user_one', $user->id)
            ->orWhere('user_two', $user->id)
            ->with([
                'userOne',
                'userTwo',
                'messages' => function ($q) {
                    $q->latest()->limit(1);
                }
            ])
            ->withCount([
                'messages as unread_count' => function ($query) use ($user) {
                    $query->where('user_id', '!=', $user->id)
                        ->whereNull('read_at');
                }
            ])
            ->latest()
            ->get();

        // Пользователи, с кем можно начать чат
        $availableUsers = User::where('id', '!=', $user->id)
            ->when($user->role === 'agronom', function ($query) {
                $query->whereIn('role', ['admin', 'manager']);
            })
            ->get();

        return view('chat.index', compact('conversations', 'availableUsers'));
    }


    // Открыть диалог
    public function show(Conversation $conversation)
    {
        $this->authorizeAccess($conversation);

        $user = auth()->user();

        $conversations = Conversation::where('user_one', $user->id)
            ->orWhere('user_two', $user->id)
            ->with([
                'userOne',
                'userTwo',
                'messages' => function ($q) {
                    $q->latest()->limit(1);
                }
            ])
            ->withCount([
                'messages as unread_count' => function ($query) use ($user) {
                    $query->where('user_id', '!=', $user->id)
                        ->whereNull('read_at');
                }
            ])
            ->latest()
            ->get();

        $conversation->load(['messages.user', 'userOne', 'userTwo']);

        $conversation->messages()
            ->where('user_id', '!=', auth()->id())
            ->whereNull('read_at')
            ->update([
                'read_at' => now()
            ]);

        return view('chat.show', compact('conversation', 'conversations'));
    }


    // Начать чат
    public function start(User $user)
    {
        $authUser = auth()->user();

        // Ограничение для агронома
        if ($authUser->role === 'agronom') {
            if (!in_array($user->role, ['admin', 'manager'])) {
                abort(403);
            }
        }

        // Проверяем существует ли уже диалог
        $conversation = Conversation::where(function ($query) use ($authUser, $user) {
            $query->where('user_one', $authUser->id)
                ->where('user_two', $user->id);
        })
            ->orWhere(function ($query) use ($authUser, $user) {
                $query->where('user_one', $user->id)
                    ->where('user_two', $authUser->id);
            })
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'user_one' => $authUser->id,
                'user_two' => $user->id,
            ]);
        }

        return redirect()->route('chat.show', $conversation);
    }


    // Отправить сообщение
    public function send(Request $request, Conversation $conversation)
    {
        $this->authorizeAccess($conversation);

        $request->validate([
            'message' => 'required|string'
        ]);

        Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => auth()->id(),
            'message' => $request->message,
        ]);

        return back();
    }

    public function fetch(Conversation $conversation, Request $request)
    {
        $this->authorizeAccess($conversation);

        $lastId = $request->last_id ?? 0;

        $messages = $conversation->messages()
            ->where('id', '>', $lastId)
            ->with('user')
            ->orderBy('id')
            ->get();

        return response()->json($messages);
    }

    public function globalUnread()
    {
        $user = auth()->user();

        $count = Message::whereHas('conversation', function ($q) use ($user) {
            $q->where('user_one', $user->id)
                ->orWhere('user_two', $user->id);
        })
            ->where('user_id', '!=', $user->id)
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'count' => $count
        ]);
    }

    // Проверка доступа
    private function authorizeAccess($conversation)
    {
        if (!in_array(auth()->id(), [$conversation->user_one, $conversation->user_two])) {
            abort(403);
        }
    }
}