FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    curl \
    unzip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json ./

RUN composer install --prefer-dist --no-progress --no-scripts

COPY . .

CMD ["./vendor/bin/phpunit"]