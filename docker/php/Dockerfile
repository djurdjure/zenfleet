# ~/projects/zenfleet/docker/php/Dockerfile

FROM php:8.2-fpm-alpine

ARG USER_ID
ARG GROUP_ID

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_HOME=/composer
ENV PATH=$PATH:/composer/vendor/bin

# Installer bash
RUN apk add --no-cache bash

RUN apk update && apk add --no-cache \
    # Dépendances de build
    build-base \
    autoconf \
    automake \
    libtool \
    # Dépendances pour extensions PHP
    icu-dev \
    libzip-dev \
    libxml2-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    postgresql-dev \
    postgresql-libs \
    linux-headers \
    # Supervisor
    supervisor \
    # Utilitaires
    git \
    curl \
    unzip \
    zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_pgsql \
    pdo_mysql \
    gd \
    intl \
    bcmath \
    opcache \
    exif \
    pcntl \
    sockets \
    zip \
    mbstring \
    xml \
    fileinfo \
    # Nettoyer les paquets temporaires de build
    && apk del build-base autoconf automake libtool linux-headers postgresql-dev \
    && rm -rf /var/cache/apk/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN addgroup -g ${GROUP_ID:-1000} zenfleet_user && \
    adduser -u ${USER_ID:-1000} -G zenfleet_user -s /bin/sh -D zenfleet_user

RUN mkdir -p /var/www/html && chown -R zenfleet_user:zenfleet_user /var/www/html

# S'assurer que le nom du fichier local est bien supervisord.conf
COPY ./supervisord.conf /etc/supervisor/supervisord.conf

WORKDIR /var/www/html

EXPOSE 9000

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]

USER zenfleet_user
