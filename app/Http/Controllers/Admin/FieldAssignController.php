<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Field;
use Illuminate\Http\Request;

class FieldAssignController extends Controller
{
    public function edit(User $user)
    {
        $fields = Field::all();
        return view('admin.users.assign', compact('user', 'fields'));
    }

    public function update(User $user, Request $request)
{
    // ❌ Админ не может иметь поля
    if ($user->role === 'admin') {
        return back()->with('error', 'Администратору нельзя назначать поля');
    }

    $fieldIds = $request->input('fields', []);

    // 🔄 Убираем все поля у пользователя (делаем их ничьими)
    Field::where('user_id', $user->id)->update([
        'user_id' => null
    ]);

    // ✅ Назначаем только свободные поля
    Field::whereIn('id', $fieldIds)
        ->whereNull('user_id')
        ->update([
            'user_id' => $user->id
        ]);

    return back()->with('success', 'Поля обновлены');
}
}
