FROM php:8.2-fpm-alpine

# Installer les dépendances système, y compris les librairies pour Redis
RUN apk update && apk add --no-cache \
    build-base autoconf automake libtool \
    bash ca-certificates curl git libzip-dev libpng-dev libjpeg-turbo-dev \
    freetype-dev supervisor icu-dev postgresql-dev linux-headers \
    redis # <-- AJOUT DE LA DÉPENDANCE REDIS

# Installer les extensions PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo pdo_pgsql pgsql zip exif intl bcmath sockets opcache

# Installer et activer l'extension Redis
RUN pecl install redis \
    && docker-php-ext-enable redis

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Créer l'utilisateur
ARG USER_ID
ARG GROUP_ID
RUN addgroup -g ${GROUP_ID:-1000} zenfleet_user && \
    adduser -u ${USER_ID:-1000} -G zenfleet_user -s /bin/sh -D zenfleet_user

# Copier la configuration Supervisor
COPY ./docker/php/supervisord.conf /etc/supervisor/supervisord.conf

# Préparer les répertoires de l'application AVANT de copier le code
USER root
RUN mkdir -p /var/www/html/storage/framework/sessions \
             /var/www/html/storage/framework/views \
             /var/www/html/storage/framework/cache \
             /var/www/html/storage/logs \
    && chown -R zenfleet_user:zenfleet_user /var/www/html

# Passer à l'utilisateur de l'application
USER zenfleet_user
WORKDIR /var/www/html

# Copier le code et installer les dépendances
COPY --chown=zenfleet_user:zenfleet_user . .
RUN composer install --no-dev --optimize-autoloader

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]