<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function store(int $id)
    {
        $publisher = User::findOrFail($id);

        if ($publisher->id === Auth::id()) {
            return back()->withErrors(['error' => 'Нельзя подписаться на себя.']);
        }

        Subscription::firstOrCreate([
            'subscriber_id' => Auth::id(),
            'publisher_id' => $publisher->id,
        ]);

        return redirect()->route('publishers.show', $publisher->id);
    }

    public function destroy(int $id)
    {
        Subscription::where('subscriber_id', Auth::id())
            ->where('publisher_id', $id)
            ->delete();

        return redirect()->route('publishers.show', $id);
    }
}