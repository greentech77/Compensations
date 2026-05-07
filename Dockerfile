FROM php:8.3-fpm-alpine

# Install system dependencies and PHP extensions
# - mbstring + gd are required by mpdf/mpdf
# - zip + libzip-dev are required by mpdf (optional features) and laravel composer cache
# - libxml2-dev backs the xml/dom extensions used for OpPIS XML exports
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    oniguruma-dev \
    mysql-client \
    nodejs \
    npm \
    && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    opcache \
    xml \
    zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html

# Install PHP dependencies. We rely on the lock file produced on the host
# (Laravel 13 / Inertia 3 / mpdf 8.3) and do NOT use --ignore-platform-reqs,
# so the build fails fast if an extension is missing in the image.
RUN composer install --optimize-autoloader --no-interaction --no-dev

# Install Node dependencies and build front-end assets via Vite
RUN npm ci && npm run build

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Create non-root user
RUN addgroup -g 1000 www && \
    adduser -u 1000 -G www -s /bin/sh -D www && \
    chown -R www:www /var/www/html

USER www

# Expose port
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]
