@extends('layouts.app')

@section('title', $publisher->name . ' — Creative Platform')

@section('content')
<div class="publisher-header" style="margin-bottom: 2rem;">
    <h1>{{ $publisher->name }}</h1>
    <p style="color: var(--color-muted);">Публикатор · {{ $posts->total() }} постов · {{ $subscriberCount }} подписчиков</p>

    @auth
        @if(Auth::id() !== $publisher->id)
            @if($isSubscribed)
                <form method="POST" action="/publishers/{{ $publisher->id }}/subscribe" style="display:inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-secondary">Отписаться</button>
                </form>
            @else
                <form method="POST" action="/publishers/{{ $publisher->id }}/subscribe" style="display:inline">
                    @csrf
                    <button type="submit" class="btn btn-primary">Подписаться</button>
                </form>
            @endif
        @endif
    @endauth
</div>

@if($posts->isEmpty())
    <p style="color: var(--color-muted);">У этого публикатора пока нет постов.</p>
@else
    <div class="posts-grid">
        @foreach($posts as $post)
            <article class="post-card">
                <div class="post-card-meta">
                    <span class="badge">{{ $post->category->name ?? '—' }}</span>
                    <span style="color: var(--color-muted); font-size: 0.85rem;">{{ $post->published_at?->diffForHumans() }}</span>
                </div>
                <h2 class="post-card-title">
                    <a href="/posts/{{ $post->id }}">{{ $post->title ?? 'Без заголовка' }}</a>
                </h2>
                @if($post->excerpt)
                    <p class="post-card-excerpt">{{ $post->excerpt }}</p>
                @endif
                <div class="post-card-footer">
                    <span>👍 {{ $post->reactions->where('type','like')->count() }}</span>
                    <span>💬 {{ $post->comments->where('is_deleted', false)->count() }}</span>
                </div>
            </article>
        @endforeach
    </div>
    <div style="margin-top: 2rem;">
        {{ $posts->links() }}
    </div>
@endif
@endsection