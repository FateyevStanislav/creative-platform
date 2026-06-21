@extends('layouts.app')

@section('title', 'Редактировать комментарий')

@section('content')
<h1 style="margin-bottom: 1.5rem;">Редактировать комментарий</h1>

<form method="POST" action="/comments/{{ $comment->id }}">
    @csrf
    @method('PUT')

    <div class="form-group">
        <label class="form-label">Комментарий</label>
        <textarea name="content" class="form-control @error('content') is-invalid @enderror" rows="5">{{ old('content', $comment->content) }}</textarea>
        @error('content')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div style="display: flex; gap: 1rem; margin-top: 1rem;">
        <button type="submit" class="btn btn-primary">Сохранить</button>
        <a href="/posts/{{ $comment->post_id }}" class="btn btn-secondary">Отмена</a>
    </div>
</form>
@endsection