# Creative Platform

Локальное веб-приложение с Laravel (SSR) + FastAPI (REST API), MySQL, Redis и Nginx в Docker Compose.

## Технологии

- Laravel 11 (PHP 8.4, Blade SSR)
- FastAPI (Python 3.11, REST API)
- MySQL 8.0
- Redis 7
- Nginx
- Docker Compose

## Запуск

```bash
cd ~/creative-platform
./install.sh
```

По желанию в laravel/.env добавить GITHUB_CLIENT_ID и GITHUB_CLIENT_SECRET для авторизации через гитхаб

## Хосты

Laravel - https://creative.localhost
FastAPI - https://ap.creative.localhost

## Роли

- **Читатель**: просмотр, комментарии, реакции, подписки, жалобы
- **Публикатор**: всё как читатель + создание постов
- **Администратор**: всё + модерация, блокировка

## Сущности

- user, role, post, comment, reaction, subscription, category, report

## Laravel роуты

### Публичные страницы

- `GET /` — главная страница, список опубликованных постов.
- `GET /categories/{slug}` — посты по выбранной категории.
- `GET /posts/{id}` — страница одного поста.

### Гостевые

- `GET /login` — форма входа.
- `POST /login` — отправка данных для входа.
- `GET /register` — форма регистрации.
- `POST /register` — создание нового пользователя.
- `GET /auth/github/redirect` — редирект на GitHub OAuth.
- `GET /auth/github/callback` — callback после авторизации через GitHub.

### Для авторизованных

- `POST /logout` — выход из аккаунта.
- `GET /feed/subscriptions` — лента постов от авторов, на которых подписан пользователь.

## Посты, комментарии, реакции

### Посты

- `GET /posts/create` — форма создания поста, доступна авторизованным пользователям.
- `POST /posts` — сохранение нового поста.
- `GET /posts/{id}/edit` — форма редактирования поста.
- `PUT /posts/{id}` — обновление поста.
- `DELETE /posts/{id}` — удаление поста автором или админом, фактически помечается как `deleted`.

### Комментарии

- `POST /posts/{id}/comments` — добавить комментарий к посту.
- `POST /comments/{id}/replies` — ответить на комментарий.
- `GET /comments/{id}/edit` — форма редактирования комментария.
- `PUT /comments/{id}` — сохранить изменения комментария.
- `DELETE /comments/{id}` — удалить комментарий, обычно soft delete через `is_deleted`.

### Реакции

- `POST /posts/{id}/reactions` — поставить или обновить реакцию `like/dislike` на пост.
- `DELETE /posts/{id}/reactions` — убрать реакцию с поста.

## Подписки, жалобы, профиль

### Подписки

- `POST /publishers/{id}/subscribe` — подписаться на автора.
- `DELETE /publishers/{id}/subscribe` — отписаться от автора.
- `GET /publishers/{id}` — страница конкретного автора с его постами.

### Жалобы

- `GET /reports/create` — форма отправки жалобы.
- `POST /reports` — отправка жалобы на пост, комментарий или пользователя.

### Пользовательские страницы

- `GET /my/posts` — список собственных постов пользователя.
- `GET /favorites` — избранные посты, собранные по лайкам пользователя.
- `GET /choose-role` — форма выбора роли после регистрации через GitHub или в специальном сценарии.
- `POST /choose-role` — сохранение выбранной роли `reader/publisher`.

## Админ

- `GET /admin/reports` — список всех жалоб.
- `GET /admin/reports/{id}` — просмотр одной жалобы.
- `PATCH /admin/reports/{id}` — обновить статус жалобы, например `pending`, `reviewed`, `rejected`, `accepted`.
- `DELETE /admin/posts/{id}` — удалить пост как администратор.
- `DELETE /admin/comments/{id}` — удалить комментарий как администратор.
- `PATCH /admin/users/{id}/block` — заблокировать пользователя.
- `PATCH /admin/users/{id}/unblock` — разблокировать пользователя.

## FastAPI

### Проверка сервиса

- `GET /api/health` — healthcheck, возвращает статус работы API.

### Посты

- `GET /api/posts` — получить список постов с пагинацией и фильтрами `page`, `size`, `category`, `publisher_id`, `content_type`.
- `GET /api/posts/{post_id}` — получить один опубликованный пост по ID.
- `GET /api/categories/{slug}/posts` — получить посты по `slug` категории.
- `GET /api/publishers/{publisher_id}/posts` — получить посты конкретного автора.
- `GET /api/feed/subscriptions/{user_id}` — получить ленту постов по подпискам пользователя.
- `GET /api/users/{user_id}/favorites` — получить избранные посты пользователя по его лайкам.

### Категории и комментарии

- `GET /api/categories` — список активных категорий.
- `GET /api/posts/{post_id}/comments` — комментарии поста с
пагинацией.

### Поиск

- `GET /api/search/publishers` — поиск авторов по имени или email через query‑параметр `q`.

## WebSocket

- `WS /ws?user_id=...` — WebSocket‑подключение для пользователя по его `user_id`; перед подключением FastAPI проверяет, существует ли пользователь, а затем регистрирует соединение в `manager.connect(...)`.