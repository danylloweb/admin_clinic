FROM php:8.3-cli
ENV DOCUMENT_ROOT=/var/www/html
# ==== Sistema ====
# - Inclui libs necessárias para compilar extensões (incl. OpenSwoole), GD, intl etc.
RUN apt-get update && apt-get install -y --no-install-recommends \
    nginx supervisor unzip git curl nano ca-certificates tzdata cron \
    default-mysql-client \
    pkg-config $PHPIZE_DEPS \
    libxml2-dev libzip-dev libxslt-dev \
    libssl-dev libcurl4-openssl-dev zlib1g-dev \
    libicu-dev \
    libzstd-dev libmemcached-dev \
    libjpeg62-turbo-dev libfreetype6-dev libpng-dev \
  && rm -rf /var/lib/apt/lists/*

# ==== PHP core/extensões ====
# GD separado (PHP 8.3 usa --with-freetype --with-jpeg)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j"$(nproc)" gd

RUN docker-php-ext-install -j"$(nproc)" \
    pcntl opcache bcmath mysqli pdo_mysql intl zip xsl soap

# ==== Extensões PECL ====
# MongoDB fixado na série ^1.15 para compatibilidade com mongodb/laravel-mongodb 4.8.x
RUN set -eux; \
    MONGODB_VERSION=1.21.2; \
    curl -fsSL --retry 5 --retry-all-errors --connect-timeout 20 \
      "https://pecl.php.net/get/mongodb-${MONGODB_VERSION}.tgz" -o /tmp/mongodb.tgz; \
    tar -tzf /tmp/mongodb.tgz >/dev/null; \
    pecl install /tmp/mongodb.tgz; \
    docker-php-ext-enable mongodb; \
    rm -f /tmp/mongodb.tgz

RUN set -eux; \
    REDIS_VERSION=6.2.0; \
    curl -fsSL --retry 5 --retry-all-errors --connect-timeout 20 \
      "https://pecl.php.net/get/redis-${REDIS_VERSION}.tgz" -o /tmp/redis.tgz; \
    tar -tzf /tmp/redis.tgz >/dev/null; \
    pecl install /tmp/redis.tgz; \
    docker-php-ext-enable redis; \
    rm -f /tmp/redis.tgz

RUN set -eux; \
    APCU_VERSION=5.1.28; \
    curl -fsSL --retry 5 --retry-all-errors --connect-timeout 20 \
      "https://pecl.php.net/get/apcu-${APCU_VERSION}.tgz" -o /tmp/apcu.tgz; \
    tar -tzf /tmp/apcu.tgz >/dev/null; \
    pecl install /tmp/apcu.tgz; \
    docker-php-ext-enable apcu; \
    rm -f /tmp/apcu.tgz

# OpenSwoole (Octane) — conforme documentação oficial
RUN set -eux; \
    OPENSWOOLE_VERSION=25.2.0; \
    curl -fsSL --retry 5 --retry-all-errors --connect-timeout 20 \
      "https://pecl.php.net/get/openswoole-${OPENSWOOLE_VERSION}.tgz" -o /tmp/openswoole.tgz; \
    tar -tzf /tmp/openswoole.tgz >/dev/null; \
    pecl install /tmp/openswoole.tgz; \
    docker-php-ext-enable openswoole; \
    rm -f /tmp/openswoole.tgz

# ==== Memcached (manual) ====
RUN curl -fsSL https://github.com/php-memcached-dev/php-memcached/archive/refs/heads/master.zip -o /tmp/memcached.zip \
 && unzip /tmp/memcached.zip -d /tmp/ \
 && cd /tmp/php-memcached-master \
 && phpize \
 && ./configure --with-php-config=/usr/local/bin/php-config --disable-memcached-sasl \
 && make -j"$(nproc)" && make install \
 && docker-php-ext-enable memcached \
 && rm -rf /tmp/php-memcached-master /tmp/memcached.zip

# ==== Fuso horário ====
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# ==== Diretórios / permissões base ====
RUN mkdir -p /run/nginx /var/log/nginx /var/log/supervisor /var/log/laravel \
    /etc/nginx/conf.d /etc/supervisor/conf.d

WORKDIR $DOCUMENT_ROOT

# ==== Copia app e configs ====
COPY . $DOCUMENT_ROOT
COPY swoole/php.ini /usr/local/etc/php/conf.d/app.ini
COPY swoole/nginx.conf /etc/nginx/nginx.conf
COPY swoole/site-nginx.conf /etc/nginx/conf.d/app.conf
COPY swoole/supervisor.conf /etc/supervisor/conf.d/supervisord.conf
COPY swoole/supervisor.conf /etc/supervisor/supervisord.conf

# ==== Cron ====
COPY laravel-cron /etc/cron.d/laravel-cron
RUN chmod 0644 /etc/cron.d/laravel-cron \
    && crontab /etc/cron.d/laravel-cron

# ==== Composer ====
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
RUN composer install --no-dev --no-interaction --optimize-autoloader --ignore-platform-reqs
RUN composer require laravel/octane --ignore-platform-reqs \
    && php artisan octane:install --server=swoole

# ==== Permissões ====
WORKDIR $DOCUMENT_ROOT
COPY . .

RUN chown -R www-data:www-data $DOCUMENT_ROOT \
    && chown -R www-data:www-data /run/nginx \
    && chown -R www-data:www-data /var/log/nginx \
    && chown -R www-data:www-data /var/log/supervisor \
    && chown -R www-data:www-data /var/log/laravel
RUN chmod -R 777 $DOCUMENT_ROOT

# Nginx logs para STDOUT/STDERR (útil em containers)
RUN ln -sf /dev/stdout /var/log/nginx/access.log \
 && ln -sf /dev/stderr /var/log/nginx/error.log

EXPOSE 80

# ==== Start (Nginx + Octane/OpenSwoole via Supervisor) ====
CMD ["supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]
