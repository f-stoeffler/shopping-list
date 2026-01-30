FROM php:8.5-fpm

RUN groupadd -g 1001 appgroup && \
    useradd -u 1001 -g appgroup -m appuser

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    curl \
    wget \
    git \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libicu-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy PHP config
COPY docker/php/php.ini /usr/local/etc/php/php.ini

# Copy application files (everything)
COPY . .

# Set directory to be writable by both appuser and root (group)
RUN chown -R appuser:appgroup /var/www/html && \
    chmod -R 775 /var/www/html && \
    chmod -R g+s /var/www/html  

# Switch to appuser for composer operations

# Set environment
ENV APP_ENV=prod
ENV APP_DEBUG=0

# Install dependencies as appuser
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# Expose port
EXPOSE 9000

# Default command
CMD ["php-fpm"]
