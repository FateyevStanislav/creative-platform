@extends('layouts.app')
@section('title', 'Новый пост')

@section('content')

<div class="page-header">
    <h1>Новый пост</h1>
</div>

@if($errors->any())
    <div class="alert alert-error">{{ $errors->first() }}</div>
@endif

<div class="card">
    <form method="POST" action="/posts">
        @csrf
        <div class="form-group">
            <label>Заголовок</label>
            <input type="text" name="title" class="form-control" value="{{ old('title') }}">
        </div>
        <div class="form-group">
            <label>Текст</label>
            <textarea name="content" class="form-control" rows="6">{{ old('content') }}</textarea>
        </div>
        <div class="form-group">
            <label>Категория</label>
            <select name="category_id" class="form-control" required>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Тип контента</label>
            <select name="content_type" class="form-control">
                <option value="text">Текст</option>
                <option value="image">Изображение</option>
                <option value="audio">Аудио</option>
                <option value="mixed">Смешанный</option>
            </select>
        </div>
        <div class="form-group">
            <label>Краткое описание</label>
            <textarea name="excerpt" class="form-control" rows="2">{{ old('excerpt') }}</textarea>
        </div>
        <div style="display:flex; gap:0.5rem;">
            <button type="submit" class="btn btn-primary">Опубликовать</button>
            <a href="/" class="btn btn-secondary">Отмена</a>
        </div>
    </form>
</div>

@endsection