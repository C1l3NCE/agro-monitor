<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\ActivityLog;

class UserController extends Controller
{
    /**
     * Список пользователей
     */
    public function index()
    {
        $users = User::with('fields')->get(); // убрали скрытие админов

        return view('admin.users.index', compact('users'));
    }

    /**
     * Форма создания
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Создание пользователя
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,manager,agronom',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'create',
            'model_type' => 'User',
            'model_id' => $user->id,
            'description' => 'Создан пользователь: ' . $user->name,
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Пользователь создан');
    }

    /**
     * Форма редактирования
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Обновление пользователя
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,manager,agronom',
            'password' => 'nullable|min:6|confirmed',
        ]);
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'update',
            'model_type' => 'User',
            'model_id' => $user->id,
            'description' => 'Обновлён пользователь: ' . $user->name,
        ]);

        // запрет админу менять самого себя в агронома/менеджера
        if ($user->id === auth()->id() && $request->role !== 'admin') {
            return back()->withErrors([
                'role' => 'Нельзя изменить свою роль'
            ]);
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        if ($request->filled('password')) {
            $user->update([
                'password' => $request->password
            ]);
        }

        return redirect()
            ->route('admin.users.index') // ← исправлено
            ->with('success', 'Пользователь обновлён');
    }

    /**
     * Удаление пользователя
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors([
                'error' => 'Нельзя удалить самого себя'
            ]);
        }

        $user->delete();
        

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Пользователь удалён');
    }
}
