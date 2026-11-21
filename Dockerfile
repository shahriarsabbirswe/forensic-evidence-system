FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev libpq-dev \
    && docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && npm ci \
    && npm run build \
    && rm -rf node_modules

RUN chmod +x /var/www/html/docker-entrypoint.sh

EXPOSE 8000
CMD ["/var/www/html/docker-entrypoint.sh"]
