FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    sqlite3 \
    libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN composer install

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]