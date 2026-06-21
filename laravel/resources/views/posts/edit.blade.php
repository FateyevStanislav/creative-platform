@extends('layouts.app')
@section('title', 'Редактировать пост')

@section('content')

<div class="page-header">
    <h1>Редактировать пост</h1>
</div>

@if($errors->any())
    <div class="alert alert-error">{{ $errors->first() }}</div>
@endif

<div class="card">
    <form method="POST" action="/posts/{{ $post->id }}">
        @csrf @method('PUT')
        <div class="form-group">
            <label>Заголовок</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $post->title) }}">
        </div>
        <div class="form-group">
            <label>Текст</label>
            <textarea name="content" class="form-control" rows="6">{{ old('content', $post->content) }}</textarea>
        </div>
        <div class="form-group">
            <label>Категория</label>
            <select name="category_id" class="form-control" required>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ $post->category_id == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Тип контента</label>
            <select name="content_type" class="form-control">
                @foreach(['text','image','audio','mixed'] as $type)
                    <option value="{{ $type }}" {{ $post->content_type === $type ? 'selected' : '' }}>
                        {{ ucfirst($type) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Краткое описание</label>
            <textarea name="excerpt" class="form-control" rows="2">{{ old('excerpt', $post->excerpt) }}</textarea>
        </div>
        <div style="display:flex; gap:0.5rem;">
            <button type="submit" class="btn btn-primary">Сохранить</button>
            <a href="/posts/{{ $post->id }}" class="btn btn-secondary">Отмена</a>
        </div>
    </form>
</div>

@endsection