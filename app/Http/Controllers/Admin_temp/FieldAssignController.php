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

    public function update(Request $request, User $user)
    {
        Field::whereIn('id', $request->fields ?? [])
            ->update(['user_id' => $user->id]);

        return redirect()->route('admin.users.index');
    }
}
