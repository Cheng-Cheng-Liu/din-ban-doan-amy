FROM php:8.1-fpm-alpine

RUN docker-php-ext-install pdo pdo_mysql
RUN curl -sS https://getcomposer.org/installer | php -- \
        --install-dir=/usr/local/bin --filename=composer
# 安裝系統依賴
RUN apk add --no-cache \
    bash \
    git \
    zip \
    unzip

# 安裝並啟用 PCNTL 擴展
RUN docker-php-ext-install pcntl

# 安裝 Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


WORKDIR /app
COPY . .
RUN composer install

CMD php artisan serve --host=0.0.0.0