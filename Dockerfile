FROM php:8.3-fpm

# Instalacja zależności systemowych
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libxslt1-dev \
    zip \
    unzip \
    curl \
    git \
    nano

# Instalacja rozszerzeń PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd xml dom

# Instalacja Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Ustawienia pracy
WORKDIR /var/www/html

# Kopiowanie plików aplikacji
COPY . .

# Instalacja zależności aplikacji
RUN composer install

# Ustawienia praw dostępu
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Uruchomienie aplikacji
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
