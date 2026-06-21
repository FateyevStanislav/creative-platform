@extends('layouts.app')
@section('title', $post->title ?? 'Публикация')

@section('content')

<div class="card">
    <div class="post-meta">
        <a href="/publishers/{{ $post->user->id }}">{{ $post->user->name }}</a>
        <span class="badge">{{ $post->category->name }}</span>
        <span>{{ $post->published_at?->diffForHumans() }}</span>
    </div>

    <h1 style="font-size:1.5rem; margin-bottom:1rem;">{{ $post->title ?? 'Без заголовка' }}</h1>

    @if($post->content)
        <p style="white-space:pre-line; margin-bottom:1.5rem;">{{ $post->content }}</p>
    @endif

    @auth
        <div class="reactions">
            <form method="POST" action="/posts/{{ $post->id }}/reactions">
                @csrf
                <input type="hidden" name="type" value="like">
                <button type="submit" class="btn btn-sm {{ $userReaction?->type === 'like' ? 'btn-primary' : 'btn-secondary' }}">
                    👍 {{ $post->reactions->where('type','like')->count() }}
                </button>
            </form>
            <form method="POST" action="/posts/{{ $post->id }}/reactions">
                @csrf
                <input type="hidden" name="type" value="dislike">
                <button type="submit" class="btn btn-sm {{ $userReaction?->type === 'dislike' ? 'btn-danger' : 'btn-secondary' }}">
                    👎 {{ $post->reactions->where('type','dislike')->count() }}
                </button>
            </form>
            @if($userReaction)
                <form method="POST" action="/posts/{{ $post->id }}/reactions">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-secondary">Убрать реакцию</button>
                </form>
            @endif
        </div>

        <div style="display:flex; gap:0.5rem; margin-bottom:1.5rem;">
            <a href="/reports/create?target_type=post&target_id={{ $post->id }}" class="btn btn-sm btn-secondary">Пожаловаться</a>
            @if(Auth::id() === $post->user_id || Auth::user()->isAdmin())
                <a href="/posts/{{ $post->id }}/edit" class="btn btn-sm btn-secondary">Редактировать</a>
                <form method="POST" action="/posts/{{ $post->id }}">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger"
                        onclick="return confirm('Удалить пост?')">Удалить</button>
                </form>
            @endif
        </div>
    @endauth
</div>

<div class="card">
    <h2 style="font-size:1.1rem; margin-bottom:1rem;">
        Комментарии ({{ $post->comments->where('is_deleted', false)->count() }})
    </h2>

    @auth
        <form method="POST" action="/posts/{{ $post->id }}/comments" style="margin-bottom:1.5rem;">
            @csrf
            <div class="form-group">
                <textarea name="content" class="form-control" placeholder="Написать комментарий..." rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Отправить</button>
        </form>
    @endauth

    @forelse($post->comments->whereNull('parent_id') as $comment)
        <div class="comment">
            @if($comment->is_deleted)
                <p style="color:#94a3b8; font-style:italic;">— комментарий удалён —</p>
            @else
                <div class="comment-meta">
                    <strong>{{ $comment->user->name }}</strong> · {{ $comment->created_at->diffForHumans() }}
                    @auth
                        · <a href="/reports/create?target_type=comment&target_id={{ $comment->id }}">Пожаловаться</a>
                        @if(Auth::id() === $comment->user_id || Auth::user()->isAdmin())
                            · <a href="/comments/{{ $comment->id }}/edit">Редактировать</a>
                            · <form method="POST" action="/comments/{{ $comment->id }}" style="display:inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                              </form>
                        @endif
                    @endauth
                </div>
                <p>{{ $comment->content }}</p>

                @auth
                    <form method="POST" action="/comments/{{ $comment->id }}/replies" style="margin-top:0.5rem;">
                        @csrf
                        <div style="display:flex; gap:0.5rem;">
                            <input type="text" name="content" class="form-control" placeholder="Ответить...">
                            <button type="submit" class="btn btn-sm btn-primary">↩</button>
                        </div>
                    </form>
                @endauth
            @endif

            @foreach($comment->replies as $reply)
                <div class="comment reply">
                    @if($reply->is_deleted)
                        <p style="color:#94a3b8; font-style:italic;">— комментарий удалён —</p>
                    @else
                        <div class="comment-meta">
                            <strong>{{ $reply->user->name }}</strong> · {{ $reply->created_at->diffForHumans() }}
                            @auth
                                · <a href="/reports/create?target_type=comment&target_id={{ $reply->id }}">Пожаловаться</a>
                                @if(Auth::id() === $reply->user_id || Auth::user()->isAdmin())
                                    · <a href="/comments/{{ $reply->id }}/edit">Редактировать</a>
                                    · <form method="POST" action="/comments/{{ $reply->id }}" style="display:inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                                    </form>
                                @endif
                            @endauth
                        </div>
                        <p>{{ $reply->content }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    @empty
        <p style="color:#64748b">Комментариев пока нет.</p>
    @endforelse
</div>

@endsection