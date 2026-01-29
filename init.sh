#!/bin/bash

echo "🚀 Initializing Symfony project with Docker…"

echo "📦 Building Docker images…"
docker compose build

echo "🔄 Starting containers…"
docker compose --env-file .env.prod up -d

echo "⏳ Waiting for services…"
sleep 10

if [ ! -f "composer.json" ]; then
echo "🎵 Installing Symfony…"
docker compose exec php composer create-project symfony/skeleton . --no-interaction
docker compose exec php composer require webapp --no-interaction
fi

echo "🗄️ Setting up database…"
docker exec php php bin/console doctrine:database:create --if-not-exists
docker exec php php bin/console doctrine:migrations:migrate --no-interaction

echo "✅ Project initialized successfully!"
echo "🌐 App available at: http://localhost:8080"
echo "🗄️ phpMyAdmin available at: http://localhost:8081"