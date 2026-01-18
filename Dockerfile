# Dockerfile
FROM php:8.5-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libjpeg-dev \
    libicu-dev \
    libzip-dev \
    libpq-dev \
    default-mysql-client \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy Apache configuration FIRST (better caching)
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Disable default site and enable our site
RUN a2dissite 000-default.conf && a2ensite 000-default.conf

# Set working directory
WORKDIR /var/www/html

# Copy composer files first for better caching
COPY composer.json composer.lock symfony.lock ./
RUN composer install --no-interaction --optimize-autoloader

# Copy the rest of the application
COPY . .

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html/var \
    && chmod -R 755 /var/www/html/var \
    && chmod -R 755 /var/www/html/public

# Create .env file if it doesn't exist (optional)
RUN if [ ! -f .env ]; then cp .env.dist .env; fi

# Healthcheck
HEALTHCHECK --interval=30s --timeout=3s --start-period=30s --retries=3 \
    CMD curl -f http://localhost/ || exit 1
