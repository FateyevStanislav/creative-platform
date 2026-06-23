@extends('layouts.app')
@section('title', 'Выбор роли')

@section('content')
<div class="auth-wrapper">
    <div class="auth-card">
        <h1>Добро пожаловать!</h1>
        <p style="color: var(--color-muted); margin-bottom: 1.5rem;">
            Выберите, кем вы хотите быть на платформе. Это можно сделать только один раз.
        </p>

        @if($errors->any())
            <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="/choose-role">
            @csrf
            
            <div class="form-group">
                <label style="display: flex; align-items: flex-start; gap: 0.75rem; padding: 1rem; border: 2px solid var(--color-border); border-radius: 8px; cursor: pointer; margin-bottom: 1rem; transition: all 0.2s;">
                    <input type="radio" name="role" value="reader" required style="margin-top: 0.25rem;">
                    <div>
                        <strong style="font-size: 1.1rem;">👁 Читатель</strong>
                        <p style="margin: 0.25rem 0 0; color: var(--color-muted); font-size: 0.9rem;">
                            Просматривайте публикации, комментируйте, ставьте лайки и подписывайтесь на авторов
                        </p>
                    </div>
                </label>

                <label style="display: flex; align-items: flex-start; gap: 0.75rem; padding: 1rem; border: 2px solid var(--color-border); border-radius: 8px; cursor: pointer; transition: all 0.2s;">
                    <input type="radio" name="role" value="publisher" required style="margin-top: 0.25rem;">
                    <div>
                        <strong style="font-size: 1.1rem;">✍️ Публикатор</strong>
                        <p style="margin: 0.25rem 0 0; color: var(--color-muted); font-size: 0.9rem;">
                            Всё что умеет читатель + возможность создавать и публиковать свои посты
                        </p>
                    </div>
                </label>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%; margin-top: 1rem;">
                Продолжить
            </button>
        </form>
    </div>
</div>

<style>
    .auth-wrapper label:has(input:checked) {
        border-color: var(--color-primary, #3b82f6);
        background: rgba(59, 130, 246, 0.05);
    }
    .auth-wrapper label:hover {
        border-color: var(--color-primary, #3b82f6);
    }
</style>
@endsection