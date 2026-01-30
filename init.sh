#!/bin/bash

echo "🚀 Initializing Symfony project with Docker…"

echo "📦 Building Docker images…"
docker compose down  # Clean up any existing containers
docker compose build --no-cache  # Force fresh build

echo "🔄 Starting containers…"
docker compose --env-file .env.prod up -d

echo "⏳ Waiting for services to be ready…"
sleep 15

echo "✅ Containers are running!"
echo "📊 Checking services:"
docker compose ps

# Note: Composer install is already done in Dockerfile
# No need to run it again in init.sh

docker exec symfony_php chown -R www-data:www-data /var/www/html/var
docker exec symfony_php chmod -R 775 /var/www/html/var
docker exec symfony_php chmod -R 777 /tmp
docker exec symfony_php php bin/console cache:warmup --env=prod


echo "🗄️ Setting up database…"
docker exec symfony_php php bin/console doctrine:database:create --if-not-exists --no-interaction
docker exec symfony_php php bin/console doctrine:migrations:migrate --no-interaction

echo "🎉 Setup complete!"
echo "🌐 Your application should be available at http://localhost:8000"
