@extends('layouts.app')
@section('title', isset($category) ? $category->name : 'Лента')

@section('content')

<div class="page-header">
    <h1>{{ isset($category) ? $category->name : 'Все публикации' }}</h1>
    @auth
        @if(Auth::user()->isPublisher())
            <a href="/posts/create" class="btn btn-primary">+ Новый пост</a>
        @endif
    @endauth
</div>

<div class="filters">
    <a href="/" class="btn btn-sm {{ !isset($category) ? 'btn-primary' : 'btn-secondary' }}">Все</a>
    @foreach($categories as $cat)
        <a href="/categories/{{ $cat->slug }}"
           class="btn btn-sm {{ isset($category) && $category->id === $cat->id ? 'btn-primary' : 'btn-secondary' }}">
            {{ $cat->name }}
        </a>
    @endforeach
    @auth
        <a href="/feed/subscriptions" class="btn btn-sm btn-secondary">Подписки</a>
    @endauth
</div>

@forelse($posts as $post)
    <div class="post-card">
        <div class="post-meta">
            <span>{{ $post->user->name }}</span>
            <span class="badge">{{ $post->category->name }}</span>
            <span>{{ $post->published_at?->diffForHumans() }}</span>
        </div>
        <h2><a href="/posts/{{ $post->id }}">{{ $post->title ?? 'Без заголовка' }}</a></h2>
        @if($post->excerpt)
            <p class="post-excerpt">{{ $post->excerpt }}</p>
        @endif
        <div class="post-card-footer">
            <span>👍 {{ $post->reactions->where('type','like')->count() }}</span>
            <span>💬 {{ $post->comments->where('is_deleted', false)->count() }}</span>
        </div>
    </div>
@empty
    <div class="card">
        <p style="color:#64748b">Публикаций пока нет.</p>
    </div>
@endforelse

{{ $posts->links() }}

@endsection