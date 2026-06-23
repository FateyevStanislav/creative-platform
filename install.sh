#!/bin/bash

set -e

echo "[1/13] Проверка зависимостей..."
command -v docker >/dev/null 2>&1 || { echo "Docker не установлен"; exit 1; }
command -v docker compose >/dev/null 2>&1 || { echo "Docker Compose не установлен"; exit 1; }

echo "[2/13] Создание laravel/.env..."
if [ ! -f laravel/.env ]; then
    if [ -f laravel/.env.example ]; then
        cp laravel/.env.example laravel/.env
        echo "laravel/.env создан из laravel/.env.example"
    else
        echo "Нет laravel/.env и laravel/.env.example"
        exit 1
    fi
else
    echo "laravel/.env уже существует"
fi

echo "[3/13] Генерация SSL сертификатов"
mkdir -p nginx/certs

if [ ! -f nginx/certs/creative.crt ]; then
    cat > nginx/certs/san.cnf << EOF
[req]
default_bits = 2048
prompt = no
default_md = sha256
distinguished_name = dn
x509_extensions = v3_req

[dn]
CN = creative.localhost

[v3_req]
subjectAltName = @alt_names

[alt_names]
DNS.1 = creative.localhost
DNS.2 = api.creative.localhost
DNS.3 = localhost
EOF

    openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
      -keyout nginx/certs/creative.key \
      -out nginx/certs/creative.crt \
      -config nginx/certs/san.cnf \
      2>/dev/null

    chmod 644 nginx/certs/creative.crt
    chmod 600 nginx/certs/creative.key
    echo "SSL сертификаты созданы"
else
    echo "SSL сертификаты уже существуют"
fi

echo "[4/13] Настройка /etc/hosts"
if ! grep -q "creative.localhost" /etc/hosts; then
    echo "127.0.0.1 creative.localhost api.creative.localhost" | sudo tee -a /etc/hosts > /dev/null
    echo "Домены добавлены в /etc/hosts"
else
    echo "Домены уже в /etc/hosts"
fi

echo "[5/13] Запуск Docker контейнеров"
docker compose up -d --build

echo "[6/13] Ожидание запуска MySQL"
sleep 10

echo "[7/13] Установка зависимостей Laravel"
docker compose exec -T laravel composer install --no-interaction --prefer-dist
echo "Зависимости установлены"

echo "[8/13] Генерация APP_KEY"
docker compose exec -T laravel php artisan key:generate --force
echo "APP_KEY сгенерирован"

echo "[9/13] Очистка кэша конфигурации Laravel"
docker compose exec -T laravel php artisan optimize:clear
echo "Кэш очищен"

echo "[10/13] Выполнение миграций и сидеров (чистой базой)"
docker compose exec -T laravel php artisan migrate:fresh --seed --force

echo "[11/13] Настройка прав доступа..."
docker compose exec -T laravel bash -c '
    mkdir -p /var/www/html/storage/framework/views
    mkdir -p /var/www/html/storage/framework/cache/data
    mkdir -p /var/www/html/storage/framework/sessions
    mkdir -p /var/www/html/storage/framework/testing
    mkdir -p /var/www/html/storage/logs
    mkdir -p /var/www/html/storage/app/public
    mkdir -p /var/www/html/bootstrap/cache
    chown -R www-data:www-data /var/www/html/storage
    chown -R www-data:www-data /var/www/html/bootstrap/cache
    chmod -R 775 /var/www/html/storage
    chmod -R 775 /var/www/html/bootstrap/cache
'

echo "[12/13] Очистка кэша маршрутов и представлений"
docker compose exec -T laravel php artisan config:clear
docker compose exec -T laravel php artisan route:clear
docker compose exec -T laravel php artisan view:clear

echo "[13/13] Установка завершена"

echo ""
echo "Откройте в браузере:"
echo "  https://creative.localhost"
echo ""
echo "Данные для входа:"
echo "  Email: admin@creative.local"
echo "  Password: password"
echo ""
echo "API:"
echo "  https://api.creative.localhost/api/health"
echo ""
echo "Логи:"
echo "  docker compose logs -f"
echo ""