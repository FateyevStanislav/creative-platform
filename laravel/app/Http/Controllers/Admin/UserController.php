<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function block(int $id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => false]);

        return redirect()->back()->with('success', 'Пользователь заблокирован.');
    }

    public function unblock(int $id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => true]);

        return redirect()->back()->with('success', 'Пользователь разблокирован.');
    }
}