<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use App\Models\Reaction;      
use App\Models\Subscription;  
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with(['user', 'category'])
            ->where('status', 'published')
            ->latest('published_at')
            ->paginate(10);

        $categories = Category::where('is_active', true)->get();

        return view('posts.index', compact('posts', 'categories'));
    }

    public function byCategory(string $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $posts = Post::with(['user', 'category'])
            ->where('category_id', $category->id)
            ->where('status', 'published')
            ->latest('published_at')
            ->paginate(10);

        $categories = Category::where('is_active', true)->get();

        return view('posts.index', compact('posts', 'categories', 'category'));
    }

    public function show(int $id)
    {
        $post = Post::with(['user', 'category', 'comments.user', 'reactions'])
            ->where('status', 'published')
            ->findOrFail($id);

        $userReaction = null;
        if (Auth::check()) {
            $userReaction = $post->reactions->where('user_id', Auth::id())->first();
        }

        return view('posts.show', compact('post', 'userReaction'));
    }

    public function create()
    {
        if (!Auth::user()->isPublisher()) {
            abort(403);
        }

        $categories = Category::where('is_active', true)->get();
        return view('posts.create', compact('categories'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->isPublisher()) {
            abort(403);
        }

        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'content_type' => 'required|in:text,image,audio,mixed',
            'excerpt' => 'nullable|string|max:500',
        ]);

        if ($request->hasFile('media')) {
            $path = $request->file('media')->store('media', 'public');
            $data['media_path'] = $path;
        }

        $post = Post::create([
            'user_id' => Auth::id(),
            'category_id' => $data['category_id'],
            'title' => $data['title'] ?? null,
            'content' => $data['content'] ?? null,
            'content_type' => $data['content_type'],
            'excerpt' => $data['excerpt'] ?? null,
            'status' => 'published',
            'published_at' => now(),
        ]);

        $subscriber_ids = \App\Models\Subscription::where('publisher_id', Auth::id())
            ->pluck('subscriber_id')
            ->toArray();

        Redis::publish('post.created', json_encode([
            'event' => 'post.created',
            'post_id' => $post->id,
            'author_id' => $post->user_id,
            'category_id' => $post->category_id,
            'title' => $post->title,
            'content_type' => $post->content_type,
            'published_at' => $post->published_at->toISOString(),
            'subscriber_ids' => $subscriber_ids,
        ]));

        return redirect()->route('posts.show', $post->id);
    }

    public function edit(int $id)
    {
        $post = Post::findOrFail($id);

        if ($post->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $categories = Category::where('is_active', true)->get();
        return view('posts.edit', compact('post', 'categories'));
    }

    public function update(Request $request, int $id)
    {
        $post = Post::findOrFail($id);

        if ($post->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'content_type' => 'required|in:text,image,audio,mixed',
            'excerpt' => 'nullable|string|max:500',
        ]);

        if ($request->hasFile('media')) {
            $path = $request->file('media')->store('media', 'public');
            $data['media_path'] = $path;
        }

        $post->update($data);

        Redis::publish('post.updated', json_encode([
            'post_id' => $post->id,
            'author_id' => $post->user_id,
            'title' => $post->title,
        ]));

        return redirect()->route('posts.show', $post->id);
    }

    public function destroy(int $id)
    {
        $post = Post::findOrFail($id);

        if ($post->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $post->update(['status' => 'deleted']);

        Redis::publish('post.deleted', json_encode([
            'post_id' => $post->id,
        ]));

        return redirect()->route('home');
    }

    public function subscriptionFeed()
    {
        $publisherIds = Auth::user()->subscriptions()->pluck('publisher_id');

        $posts = Post::with(['user', 'category'])
            ->whereIn('user_id', $publisherIds)
            ->where('status', 'published')
            ->latest('published_at')
            ->paginate(10);

        $categories = Category::where('is_active', true)->get();

        return view('posts.index', compact('posts', 'categories'));
    }

    public function publisherPage(int $id)
    {
        $publisher = User::findOrFail($id);

        $posts = Post::with(['user', 'category'])
            ->where('user_id', $id)
            ->where('status', 'published')
            ->latest('published_at')
            ->paginate(10);

        $isSubscribed = false;
        if (Auth::check()) {
            $isSubscribed = Auth::user()->subscriptions()
                ->where('publisher_id', $id)->exists();
        }

        $subscriberCount = \App\Models\Subscription::where('publisher_id', $id)->count();

        $categories = Category::where('is_active', true)->get();

        return view('publishers.show', compact('publisher', 'posts', 'isSubscribed', 'categories', 'subscriberCount'));
    }

    public function myPosts()
    {
        $posts = Post::with(['category'])
            ->where('user_id', Auth::id())
            ->whereIn('status', ['published', 'draft'])
            ->latest('published_at')
            ->paginate(10);

        return view('posts.my', compact('posts'));
    }

    public function favorites()
    {
        $likedPostIds = Reaction::where('user_id', Auth::id())
            ->where('type', 'like')
            ->pluck('post_id');

        $posts = Post::with(['user', 'category'])
            ->whereIn('id', $likedPostIds)
            ->where('status', 'published')
            ->latest('published_at')
            ->paginate(10);

        return view('posts.favorites', compact('posts'));
    }
}