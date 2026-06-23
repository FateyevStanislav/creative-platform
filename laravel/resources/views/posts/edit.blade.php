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
    <form method="POST" action="/posts/{{ $post->id }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
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
            <select name="content_type" id="content_type" class="form-control" onchange="toggleMediaField()">
                <option value="text" {{ $post->content_type === 'text' ? 'selected' : '' }}>Текст</option>
                <option value="image" {{ $post->content_type === 'image' ? 'selected' : '' }}>Изображение</option>
                <option value="audio" {{ $post->content_type === 'audio' ? 'selected' : '' }}>Аудио</option>
                <option value="mixed" {{ $post->content_type === 'mixed' ? 'selected' : '' }}>Смешанный</option>
            </select>
        </div>
        <div class="form-group" id="media_field" style="{{ in_array($post->content_type, ['image', 'audio', 'video', 'mixed']) ? '' : 'display:none;' }}">
            <label>Медиа файл</label>
            @if($post->media_path)
                <div style="margin-bottom: 0.5rem; padding: 0.5rem; background: var(--color-muted, #f1f5f9); border-radius: 4px;">
                    Текущий файл: <strong>{{ basename($post->media_path) }}</strong>
                </div>
            @endif
            <input type="file" name="media" id="media_input" class="form-control" accept="image/*,audio/*">
            <small style="color: var(--color-muted);">Загрузите новый файл, чтобы заменить текущий. Максимум 10 МБ.</small>
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

<script>
function toggleMediaField() {
    const contentType = document.getElementById('content_type').value;
    const mediaField = document.getElementById('media_field');
    const fileInput = document.getElementById('media_input');
    
    const showMedia = ['image', 'audio', 'mixed'].includes(contentType);
    mediaField.style.display = showMedia ? '' : 'none';
    
    if (contentType === 'image') {
        fileInput.accept = 'image/*';
    } else if (contentType === 'audio') {
        fileInput.accept = 'audio/*';
    } else {
        fileInput.accept = 'image/*,audio/*';
    }
}

document.addEventListener('DOMContentLoaded', toggleMediaField);
</script>

@endsection