@extends('layouts.app')
@section('title', 'Вход')

@section('content')
<div class="auth-wrapper">
    <div class="auth-card">
        <h1>Вход</h1>

        @if($errors->any())
            <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="/login">
            @csrf
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            </div>
            <div class="form-group">
                <label>Пароль</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%">Войти</button>
        </form>

        <div class="auth-divider">или</div>

        <a href="/auth/github/redirect" class="btn btn-github">Войти через GitHub</a>

        <p style="text-align:center; margin-top:1rem; font-size:0.875rem;">
            Нет аккаунта? <a href="/register">Зарегистрироваться</a>
        </p>
    </div>
</div>
@endsection