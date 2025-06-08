FROM php:8.3-fpm

ENV DOCUMENT_ROOT /var/www/html
ENV TZ=America/Recife
# Instala dependências necessárias
RUN apt-get update && apt-get install -y \
        nginx \
        unzip \
        git \
        curl \
        nano \
        libxml2-dev \
        libzip-dev \
        libxslt-dev \
        libpng-dev \
        libssl-dev \
        libzstd-dev \
        libmemcached-dev \
        librdkafka-dev \
        supervisor \
        cron \
    && docker-php-ext-install opcache \
    && pecl install redis mongodb apcu rdkafka xdebug \
    && echo "extension=mongodb.so" > $PHP_INI_DIR/conf.d/mongo.ini \
    && echo "extension=rdkafka.so" > $PHP_INI_DIR/conf.d/rdkafka.ini \
    && docker-php-ext-enable redis mongodb apcu rdkafka xdebug \
    && docker-php-ext-configure gd \
    && docker-php-ext-configure zip \
    && docker-php-ext-install bcmath \
    && docker-php-ext-install -j "$(nproc)" mysqli pdo_mysql intl zip xsl soap gd \
    && docker-php-source delete

# Instala manualmente o memcached com suporte atualizado
RUN curl -fsSL https://github.com/php-memcached-dev/php-memcached/archive/refs/heads/master.zip -o /tmp/memcached.zip \
    && unzip /tmp/memcached.zip -d /tmp/ \
    && cd /tmp/php-memcached-master \
    && phpize \
    && ./configure --with-php-config=/usr/local/bin/php-config --disable-memcached-sasl \
    && make -j "$(nproc)" && make install \
    && docker-php-ext-enable memcached \
    && rm -rf /tmp/php-memcached-master /tmp/memcached.zip

# Configura o fuso horário
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Configura o diretório de trabalho
WORKDIR $DOCUMENT_ROOT

# Remove configurações antigas do Nginx
RUN rm -f /etc/nginx/conf.d/*

RUN mkdir -p /run/nginx \
    && mkdir -p /var/log/supervisor \
    && mkdir -p /etc/nginx/conf.d \
    && mkdir -p /etc/supervisor.d \
    && mkdir -p /var/log/laravel

ENV PATH="/usr/bin/supervisor:${PATH}"
# Copia o código da aplicação e as configurações
COPY . $DOCUMENT_ROOT
COPY nginx.conf /etc/nginx/nginx.conf
COPY php.ini /usr/local/etc/php/conf.d/app.ini
COPY php-fpm-www.conf /usr/local/etc/php-fpm.d/www.conf
COPY supervisor.conf /etc/supervisor.d/supervisord.conf
COPY laravel-cron /etc/cron.d/laravel-cron
RUN chmod 0644 /etc/cron.d/laravel-cron
# Instala o Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
# Instala as dependências do Laravel
#RUN composer install --no-dev --no-interaction --no-progress --no-suggest --optimize-autoloader
# Configuração do Xdebug para cobertura de testes
RUN echo "zend_extension=/usr/local/lib/php/extensions/no-debug-non-zts-20230831/xdebug.so" > $PHP_INI_DIR/conf.d/xdebug.ini \
    && echo "xdebug.mode=coverage" >> $PHP_INI_DIR/conf.d/xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> $PHP_INI_DIR/conf.d/xdebug.ini
# Set permissions
RUN chown -R www-data:www-data $DOCUMENT_ROOT \
    && chown -R www-data:www-data /run/nginx \
    && chown -R www-data:www-data /var/log/nginx \
    && chown -R www-data:www-data /var/log/supervisor \
    && chown -R www-data:www-data /var/log/laravel
RUN chmod 777 /var/www/html

COPY . $DOCUMENT_ROOT

# Garante que os diretórios existem
RUN mkdir -p $DOCUMENT_ROOT/storage/logs \
    && mkdir -p /run/nginx \
    && mkdir -p /var/log/supervisor \
    && mkdir -p /etc/nginx/conf.d \
    && mkdir -p /etc/supervisor.d \
    && mkdir -p /var/log/laravel

RUN chown -R www-data:www-data $DOCUMENT_ROOT \
    && chmod -R 775 $DOCUMENT_ROOT/storage
# Exposição da porta 80
EXPOSE 80

# Define o comando de inicialização
RUN ln -sf /dev/stdout /var/log/nginx/access.log && ln -sf /dev/stderr /var/log/nginx/error.log
CMD ["supervisord", "-n", "-c", "/etc/supervisor.d/supervisord.conf"]
