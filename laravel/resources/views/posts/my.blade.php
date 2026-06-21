@extends('layouts.app')

@section('title', 'Мои посты')

@section('content')
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
    <h1>Мои посты</h1>
    @if(Auth::user()->isPublisher())
        <a href="/posts/create" class="btn btn-primary">+ Новый пост</a>
    @endif
</div>

@if($posts->isEmpty())
    <p style="color:var(--color-muted);">У вас пока нет постов.</p>
@else
    <div class="posts-grid">
        @foreach($posts as $post)
            <article class="post-card">
                <div class="post-card-meta">
                    <span class="badge">{{ $post->category->name ?? '—' }}</span>
                    <span style="color:var(--color-muted); font-size:0.85rem;">{{ $post->published_at?->diffForHumans() ?? 'черновик' }}</span>
                </div>
                <h2 class="post-card-title">
                    <a href="/posts/{{ $post->id }}">{{ $post->title ?? 'Без заголовка' }}</a>
                </h2>
                @if($post->excerpt)
                    <p class="post-card-excerpt">{{ $post->excerpt }}</p>
                @endif
                <div class="post-card-footer">
                    <a href="/posts/{{ $post->id }}/edit" class="btn btn-sm btn-secondary">Редактировать</a>
                    <form method="POST" action="/posts/{{ $post->id }}" style="display:inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger"
                            onclick="return confirm('Удалить пост?')">Удалить</button>
                    </form>
                </div>
            </article>
        @endforeach
    </div>
    <div style="margin-top:2rem;">
        {{ $posts->links() }}
    </div>
@endif
@endsection