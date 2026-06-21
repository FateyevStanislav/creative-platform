<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Reaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReactionController extends Controller
{
    public function store(Request $request, int $id)
    {
        $post = Post::findOrFail($id);

        $data = $request->validate([
            'type' => 'required|in:like,dislike',
        ]);

        Reaction::updateOrCreate(
            ['user_id' => Auth::id(), 'post_id' => $post->id],
            ['type' => $data['type']]
        );

        return redirect()->route('posts.show', $post->id);
    }

    public function destroy(int $id)
    {
        $post = Post::findOrFail($id);

        Reaction::where('user_id', Auth::id())
            ->where('post_id', $post->id)
            ->delete();

        return redirect()->route('posts.show', $post->id);
    }
}