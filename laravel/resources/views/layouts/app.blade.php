<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Creative Platform')</title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>

<nav class="navbar">
    <div class="container">
        <a href="/" class="navbar-brand">Creative Platform</a>
        <ul class="navbar-links">
            @auth
                <li><a href="/feed/subscriptions">Подписки</a></li>
                @if(Auth::user()->isPublisher())
                    <li><a href="/posts/create">Новый пост</a></li>
                @endif
                @if(Auth::user()->isPublisher())
                    <li><a href="/my/posts">Мои посты</a></li>
                @endif
                @if(Auth::user()->isAdmin())
                    <li><a href="/admin/reports">Жалобы</a></li>
                @endif
                <li><a href="/favorites">Избранное</a></li>
                <li>
                    <form method="POST" action="/logout" style="display:inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-secondary">Выйти</button>
                    </form>
                </li>
            @else
                <li><a href="/login">Войти</a></li>
                <li><a href="/register" class="btn btn-sm btn-primary">Регистрация</a></li>
            @endauth
        </ul>
    </div>
</nav>

<div class="container" style="padding-top: 2rem; padding-bottom: 3rem;">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @yield('content')
</div>

@auth
<div id="ws-notifications"></div>
<script>
    const userId = {{ Auth::id() }};
    let ws;
    let reconnectTimeout;

    function connectWebSocket() {
        ws = new WebSocket(`wss://api.creative.localhost/ws?user_id=${userId}`);
        
        ws.onopen = () => {
            console.log('WebSocket connected');
            clearTimeout(reconnectTimeout);
        };
        
        ws.onclose = () => {
            console.log('WebSocket disconnected, reconnecting in 5 seconds...');
            reconnectTimeout = setTimeout(connectWebSocket, 5000);
        };
        
        ws.onerror = (error) => {
            console.error('WebSocket error:', error);
            ws.close();
        };
        
        ws.onmessage = (event) => {
            try {
                const data = JSON.parse(event.data);
                
                switch(data.event) {
                    case 'post.created':
                        showNotification(`Новая публикация: ${data.title ?? 'без заголовка'}`);
                        break;
                    case 'post.updated':
                        showNotification(`Публикация обновлена: ${data.title ?? 'без заголовка'}`);
                        break;
                    case 'post.deleted':
                        showNotification(`Публикация удалена`);
                        break;
                    case 'comment.created':
                        showNotification(`Новый комментарий`);
                        break;
                }
            } catch (error) {
                console.error('Error parsing WebSocket message:', error);
            }
        };
    }

    function showNotification(text) {
        const container = document.getElementById('ws-notifications');
        const el = document.createElement('div');
        el.className = 'ws-notification';
        el.textContent = text;
        container.appendChild(el);
        setTimeout(() => el.remove(), 5000);
    }

    connectWebSocket();
</script>
@endauth

</body>
</html>