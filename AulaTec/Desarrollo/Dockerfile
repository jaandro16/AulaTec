FROM php:8.2-apache
WORKDIR /var/www/html


RUN a2enmod rewrite

RUN apt-get update && apt-get install -y \
    libicu-dev \
    libmariadb-dev \
    unzip zip \
    zlib1g-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY apache.conf /etc/apache2/sites-available/000-default.conf

RUN docker-php-ext-install gettext intl pdo_mysql gd

RUN docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd