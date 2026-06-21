<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, int $id)
    {
        $post = Post::findOrFail($id);

        $data = $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        Comment::create([
            'post_id' => $post->id,
            'user_id' => Auth::id(),
            'content' => $data['content'],
        ]);

        return redirect()->route('posts.show', $post->id);
    }

    public function reply(Request $request, int $id)
    {
        $parent = Comment::findOrFail($id);

        $data = $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        Comment::create([
            'post_id' => $parent->post_id,
            'user_id' => Auth::id(),
            'parent_id' => $parent->id,
            'content' => $data['content'],
        ]);

        return redirect()->route('posts.show', $parent->post_id);
    }

    public function edit(int $id)
    {
        $comment = Comment::findOrFail($id);

        if ($comment->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        return view('comments.edit', compact('comment'));
    }

    public function update(Request $request, int $id)
    {
        $comment = Comment::findOrFail($id);

        if ($comment->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $data = $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $comment->update($data);

        return redirect()->route('posts.show', $comment->post_id);
    }

    public function destroy(int $id)
    {
        $comment = Comment::findOrFail($id);

        if ($comment->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $comment->update(['is_deleted' => true]);

        return redirect()->route('posts.show', $comment->post_id);
    }
}