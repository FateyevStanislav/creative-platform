@extends('layouts.app')

@section('title', 'Отправить жалобу')

@section('content')
<h1 style="margin-bottom: 1.5rem;">Отправить жалобу</h1>

<form method="POST" action="/reports">
    @csrf

    <input type="hidden" name="target_type" value="{{ request('target_type') }}">
    <input type="hidden" name="target_id" value="{{ request('target_id') }}">

    <div class="form-group">
        <label class="form-label">Тип объекта</label>
        <input type="text" class="form-control" value="{{ match(request('target_type')) { 'post' => 'Пост', 'comment' => 'Комментарий', 'user' => 'Пользователь', default => '—' } }}" disabled>
    </div>

    <div class="form-group">
        <label class="form-label">Причина</label>
        <select name="reason" class="form-control @error('reason') is-invalid @enderror">
            <option value="">— выберите причину —</option>
            <option value="spam" {{ old('reason') === 'spam' ? 'selected' : '' }}>Спам</option>
            <option value="abuse" {{ old('reason') === 'abuse' ? 'selected' : '' }}>Оскорбление</option>
            <option value="plagiarism" {{ old('reason') === 'plagiarism' ? 'selected' : '' }}>Плагиат</option>
            <option value="other" {{ old('reason') === 'other' ? 'selected' : '' }}>Другое</option>
        </select>
        @error('reason')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label">Комментарий (необязательно)</label>
        <textarea name="message" class="form-control @error('message') is-invalid @enderror" rows="4">{{ old('message') }}</textarea>
        @error('message')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div style="display: flex; gap: 1rem; margin-top: 1rem;">
        <button type="submit" class="btn btn-primary">Отправить</button>
        <a href="javascript:history.back()" class="btn btn-secondary">Отмена</a>
    </div>
</form>
@endsection