FROM php:8.3-apache

# تثبيت الأدوات المطلوبة وبناء امتدادات PHP
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libonig-dev \
    libicu-dev \
    libxml2-dev \
    libssl-dev \
    pkg-config \
    zlib1g-dev \
    libpq-dev \
    && docker-php-ext-install pdo pdo_mysql mysqli intl zip opcache

# تثبيت pecl/redis (phpredis) ثم تفعيله
RUN pecl channel-update pecl.php.net \
    && pecl install redis-6.0.2 \
    && docker-php-ext-enable redis

# تمكين mod_rewrite
RUN a2enmod rewrite

# نسخ إعدادات php
COPY php.ini /usr/local/etc/php/php.ini

# إعداد Document Root
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# نسخ التطبيق
WORKDIR /var/www/html
COPY . /var/www/html

# تثبيت Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && rm composer-setup.php

# تثبيت الحزم (مثل Monolog) إن وُجدت في composer.json
RUN composer install --no-interaction --no-progress --prefer-dist

# إعداد أذونات
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80
