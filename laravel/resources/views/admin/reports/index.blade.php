@extends('layouts.app')

@section('title', 'Жалобы — Администратор')

@section('content')
<h1 style="margin-bottom: 1.5rem;">Жалобы</h1>

@if($reports->isEmpty())
    <p style="color: var(--color-muted);">Жалоб пока нет.</p>
@else
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="border-bottom: 2px solid var(--color-border); text-align: left;">
                <th style="padding: 0.75rem;">ID</th>
                <th style="padding: 0.75rem;">От кого</th>
                <th style="padding: 0.75rem;">Тип</th>
                <th style="padding: 0.75rem;">Причина</th>
                <th style="padding: 0.75rem;">Статус</th>
                <th style="padding: 0.75rem;">Дата</th>
                <th style="padding: 0.75rem;"></th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports as $report)
            <tr style="border-bottom: 1px solid var(--color-border);">
                <td style="padding: 0.75rem;">{{ $report->id }}</td>
                <td style="padding: 0.75rem;">{{ $report->user->name }}</td>
                <td style="padding: 0.75rem;">{{ $report->target_type }}</td>
                <td style="padding: 0.75rem;">{{ $report->reason }}</td>
                <td style="padding: 0.75rem;">
                    <span class="badge {{ $report->status === 'pending' ? 'badge-warning' : '' }}">
                        {{ $report->status }}
                    </span>
                </td>
                <td style="padding: 0.75rem;">{{ $report->created_at->format('d.m.Y') }}</td>
                <td style="padding: 0.75rem;">
                    <a href="/admin/reports/{{ $report->id }}" class="btn btn-sm btn-secondary">Просмотр</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div style="margin-top: 2rem;">
        {{ $reports->links() }}
    </div>
@endif
@endsection