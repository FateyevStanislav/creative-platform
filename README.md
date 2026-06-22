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
docker compose up -d --build
docker compose exec laravel php artisan migrate --seed
```

## Хосты

Добавить в `/etc/hosts`:
```
127.0.0.1 creative.localhost api.creative.localhost
```

## Функции

### Laravel (https://creative.localhost)

- Авторизация (email/пароль)
- Создание, редактирование, удаление постов
- Комментарии (с мягким удалением)
- Реакции (like/dislike)
- Подписки на авторов
- Жалобы на посты/комментарии
- Администраторская панель (модерация, блокировка юзеров)

### FastAPI (https://api.creative.localhost)

- REST API: `/api/posts`, `/api/categories`, `/api/publishers/{id}/posts`, `/api/feed/subscriptions/{user_id}`
- Swagger: `/docs`

## Роли

- **Читатель**: просмотр, комментарии, реакции, подписки, жалобы
- **Публикатор**: всё как читатель + создание постов
- **Администратор**: всё + модерация, блокировка

## Сущности

- user, role, post, comment, reaction, subscription, category, report

## Команды

```bash
# Логи
docker compose logs -f laravel
docker compose logs -f fastapi

# Миграции
docker compose exec laravel php artisan migrate --seed

# Очистка
docker compose down -v
```
