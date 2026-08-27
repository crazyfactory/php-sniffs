FROM php:8.2.31-cli-alpine3.23 AS base

RUN apk add --no-cache \
    bash \
    curl \
    git \
    libzip-dev \
    oniguruma-dev \
    unzip

RUN docker-php-ext-install mbstring zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/project

FROM base AS composer-install

COPY --chown=www-data:www-data ./composer.json composer.json
COPY --chown=www-data:www-data ./composer.lock composer.lock

RUN mkdir -p vendor && chown www-data:www-data vendor

USER www-data
RUN COMPOSER_MEMORY_LIMIT=-1 composer install --no-interaction
USER root

FROM base AS copy-volume

COPY --chown=www-data:www-data ./ /var/www/project

# ---------------- CI ----------------- #

FROM base AS image-ci-testing

COPY ./docker/php/php-ini.ini /usr/local/etc/php/conf.d/999-overrides.ini

COPY --from=composer-install /var/www/project .
COPY --from=copy-volume /var/www/project .

VOLUME ["/var/www/project"]
