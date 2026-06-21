<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Support\Facades\Redis;

class PostController extends Controller
{
    public function destroy(int $id)
    {
        $post = Post::findOrFail($id);
        $post->update(['status' => 'deleted']);

        Redis::publish('post.deleted', json_encode(['post_id' => $post->id]));

        return redirect()->route('home')->with('success', 'Пост удалён.');
    }

    public function destroyComment(int $id)
    {
        $comment = Comment::findOrFail($id);
        $comment->update(['is_deleted' => true]);

        return redirect()->back()->with('success', 'Комментарий удалён.');
    }
}