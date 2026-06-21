@extends('layouts.app')

@section('title', 'Жалоба #{{ $report->id }}')

@section('content')
<h1 style="margin-bottom: 1.5rem;">Жалоба #{{ $report->id }}</h1>

<div style="display: grid; gap: 0.75rem; margin-bottom: 2rem;">
    <div><strong>От кого:</strong> {{ $report->user->name }}</div>
    <div><strong>Тип объекта:</strong> {{ $report->target_type }} #{{ $report->target_id }}</div>
    <div><strong>Причина:</strong> {{ $report->reason }}</div>
    <div><strong>Статус:</strong> {{ $report->status }}</div>
    <div><strong>Дата:</strong> {{ $report->created_at->format('d.m.Y H:i') }}</div>
    @if($report->message)
        <div><strong>Комментарий:</strong> {{ $report->message }}</div>
    @endif
    @if($report->reviewedBy)
        <div><strong>Обработал:</strong> {{ $report->reviewedBy->name }} в {{ $report->reviewed_at->format('d.m.Y H:i') }}</div>
    @endif
</div>

@if($report->status === 'pending')
<form method="POST" action="/admin/reports/{{ $report->id }}">
    @csrf
    @method('PATCH')

    <div class="form-group">
        <label class="form-label">Изменить статус</label>
        <select name="status" class="form-control">
            <option value="reviewed">Рассмотрено</option>
            <option value="accepted">Принято</option>
            <option value="rejected">Отклонено</option>
        </select>
    </div>

    <div style="display: flex; gap: 1rem; margin-top: 1rem;">
        <button type="submit" class="btn btn-primary">Сохранить</button>
        <a href="/admin/reports" class="btn btn-secondary">Назад</a>
    </div>
</form>
@else
    <a href="/admin/reports" class="btn btn-secondary">Назад к списку</a>
@endif

@if($report->target_type === 'post')
    <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--color-border);">
        <h3>Действия</h3>
        <form method="POST" action="/admin/posts/{{ $report->target_id }}" style="display:inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger" onclick="return confirm('Удалить пост?')">Удалить пост</button>
        </form>
    </div>
@elseif($report->target_type === 'comment')
    <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--color-border);">
        <h3>Действия</h3>
        <form method="POST" action="/admin/comments/{{ $report->target_id }}" style="display:inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger" onclick="return confirm('Удалить комментарий?')">Удалить комментарий</button>
        </form>
    </div>
@elseif($report->target_type === 'user')
    <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--color-border);">
        <h3>Действия</h3>
        <form method="POST" action="/admin/users/{{ $report->target_id }}/block" style="display:inline">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-danger" onclick="return confirm('Заблокировать пользователя?')">Заблокировать</button>
        </form>
    </div>
@endif
@endsection