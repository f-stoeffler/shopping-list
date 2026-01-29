FROM php:8.5-fpm

RUN groupadd -g 1001 appgroup && \
    useradd -u 1001 -g appgroup -m appuser

# Set working directory
WORKDIR /var/www/html

COPY . .


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

ENV APP_ENV=prod
ENV APP_DEBUG=0

# Install Composer
RUN wget https://getcomposer.org/download/2.9.4/composer.phar \
    && mv composer.phar /usr/bin/composer && chmod +x /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader --no-interaction
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


# Copy PHP config
COPY docker/php/php.ini /usr/local/etc/php/php.ini

# Set permissions
RUN chown -R appuser:appgroup /var/www/html

# Use non-root user
USER appuser

# Expose port
EXPOSE 9000
EXPOSE 6767
EXPOSE 6768

# Default command
CMD ["php-fpm"]