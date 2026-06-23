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
        console.log('WebSocket disconnected, reconnecting...');
        reconnectTimeout = setTimeout(connectWebSocket, 5000);
    };
    
    ws.onerror = (error) => {
        console.error('WebSocket error:', error);
        ws.close();
    };
    
    ws.onmessage = (event) => {
        try {
            const data = JSON.parse(event.data);
            handleWebSocketEvent(data);
        } catch (error) {
            console.error('Error parsing message:', error);
        }
    };
}

function handleWebSocketEvent(data) {
    switch(data.event) {
        case 'post.created':
            showNotification(`Новая публикация: ${data.title || 'без заголовка'}`);
            addPostToList(data);
            break;
        case 'post.updated':
            showNotification(`Публикация обновлена: ${data.title || 'без заголовка'}`);
            updatePostInList(data);
            break;
        case 'post.deleted':
            showNotification('Публикация удалена');
            removePostFromList(data.post_id);
            break;
        case 'comment.created':
            showNotification('Новый комментарий');
            addCommentToList(data);
            break;
    }
}

function showNotification(text) {
    const container = document.getElementById('ws-notifications');
    const el = document.createElement('div');
    el.className = 'ws-notification';
    el.textContent = text;
    container.appendChild(el);
    setTimeout(() => el.remove(), 5000);
}

function addPostToList(data) {
    const postsContainer = document.querySelector('.posts-list, .post-card')?.closest('.container, main');
    if (!postsContainer) return;
    
    const newPost = document.createElement('div');
    newPost.className = 'post-card';
    newPost.style.animation = 'slideIn 0.5s ease-out';
    newPost.innerHTML = `
        <div class="post-meta">
            <span>${data.author_name || 'Автор'}</span>
            <span class="badge">${data.category_name || 'Категория'}</span>
            <span>только что</span>
        </div>
        <h2><a href="/posts/${data.post_id}">${data.title || 'Без заголовка'}</a></h2>
        ${data.excerpt ? `<p class="post-excerpt">${data.excerpt}</p>` : ''}
    `;
    
    const firstPost = postsContainer.querySelector('.post-card');
    if (firstPost) {
        postsContainer.insertBefore(newPost, firstPost);
    } else {
        postsContainer.appendChild(newPost);
    }
}

function updatePostInList(data) {
    const postElement = document.querySelector(`a[href="/posts/${data.post_id}"]`)?.closest('.post-card');
    if (!postElement) return;
    
    const titleElement = postElement.querySelector('h2 a');
    if (titleElement && data.title) {
        titleElement.textContent = data.title;
    }
    
    postElement.style.animation = 'highlight 2s ease-out';
    setTimeout(() => postElement.style.animation = '', 2000);
}

function removePostFromList(postId) {
    const postElement = document.querySelector(`a[href="/posts/${postId}"]`)?.closest('.post-card');
    if (!postElement) return;
    
    postElement.style.transition = 'all 0.5s ease-out';
    postElement.style.opacity = '0';
    postElement.style.transform = 'scale(0.9)';
    setTimeout(() => postElement.remove(), 500);
}

function addCommentToList(data) {
    const commentsContainer = document.querySelector('.comments-list, .comment')?.closest('.card, .container');
    if (!commentsContainer) return;
    
    const newComment = document.createElement('div');
    newComment.className = 'comment';
    newComment.style.animation = 'slideIn 0.3s ease-out';
    newComment.innerHTML = `
        <div class="comment-meta">
            <strong>${data.author_name || 'Автор'}</strong> · только что
        </div>
        <p>${data.content}</p>
    `;
    
    commentsContainer.appendChild(newComment);
}

connectWebSocket();
</script>

<style>
.ws-notification {
    position: fixed;
    top: 80px;
    right: 20px;
    background: #3b82f6;
    color: white;
    padding: 1rem 1.5rem;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    z-index: 1000;
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from { transform: translateX(400px); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

@keyframes highlight {
    0% { background-color: rgba(59, 130, 246, 0.2); }
    100% { background-color: transparent; }
}
</style>
@endauth

</body>
</html>