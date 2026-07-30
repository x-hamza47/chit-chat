FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    nginx \
    gettext-base \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip pcntl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN composer install --no-dev --optimize-autoloader

COPY nginx.conf.template /etc/nginx/nginx.conf.template
COPY start.sh /start.sh

RUN chmod +x /start.sh

CMD ["/start.sh"]