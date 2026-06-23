@extends('layouts.app')

@section('title', 'Избранное')

@section('content')
<h1 style="margin-bottom: 1.5rem;">Избранное</h1>

@if($posts->isEmpty())
    <p style="color: var(--color-muted);">У вас пока нет избранных постов.</p>
@else
    <div class="posts-grid">
        @foreach($posts as $post)
            <article class="post-card">
                <div class="post-card-meta">
                    <a href="/publishers/{{ $post->user->id }}">{{ $post->user->name }}</a>
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