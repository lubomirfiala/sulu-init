# STAGE 1: build the base apache image
FROM php:8.2.4-apache AS apache_base

RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        libicu-dev \
        libpng-dev \
        libpq-dev \
        libzip-dev \
        libmagickwand-dev \
        openssl \
        git \
        unzip \
        inkscape \
        curl && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN docker-php-ext-configure \
    intl

RUN docker-php-ext-install \
        intl \
        gd \
        pdo \
        exif \
        xml \
		pdo_mysql \
        opcache \
		zip

RUN pecl install \
    imagick

RUN docker-php-ext-enable \
    imagick

RUN echo "short_open_tag = off" >> /usr/local/etc/php/php.ini && \
    echo "upload_max_filesize = 32M" >> /usr/local/etc/php/php.ini && \
    echo "post_max_size = 32M" >> /usr/local/etc/php/php.ini && \
    echo "date.timezone = Europe/Prague" >> /usr/local/etc/php/conf.d/symfony.ini && \
    echo "opcache.max_accelerated_files = 20000" >> /usr/local/etc/php/conf.d/symfony.ini && \
    echo "realpath_cache_size=4096K" >> /usr/local/etc/php/conf.d/symfony.ini && \
    echo "realpath_cache_ttl=600" >> /usr/local/etc/php/conf.d/symfony.ini && \
    echo "memory_limit=256M" >> /usr/local/etc/php/conf.d/symfony.ini && \
    echo "max_execution_time = 60" >> /usr/local/etc/php/conf.d/symfony.ini && \
    a2enmod rewrite && \
    a2enmod expires

COPY --from=composer:2.4 /usr/bin/composer /usr/bin/composer



# STAGE 2: install node dependencies and build assets
FROM node:18.15.0-alpine as app_node
WORKDIR /srv

COPY ./package*.json ./
COPY ./yarn.lock ./
COPY ./webpack.config.js ./

RUN yarn install

COPY ./assets assets/

RUN mkdir public && \
    yarn build

# Prepare entrypoint for building assets in real time
# This should be used only in dev environment
COPY ./docker/node_entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint
ENTRYPOINT ["entrypoint"]
CMD ["yarn", "watch"]



# STAGE 2: install composer dependencies and copy files
FROM apache_base AS app
WORKDIR /srv

# Install composer dependencies
# Performa the installation before most files are copied over to prevent running this step every time a source code file changes
ENV COMPOSER_ALLOW_SUPERUSER=1
COPY ./composer.json composer.lock symfony.lock ./
RUN set -eux; \
	composer install --prefer-dist --no-dev --no-scripts --no-progress; \
	composer clear-cache

# Copy only specifically what we need
COPY ./.env ./
COPY ./bin bin/
COPY ./config config/
COPY ./public public/
COPY ./src src/
COPY ./templates templates/
COPY ./translations translations/

# Copy assets built in app_node stage
COPY --from=app_node /srv/public/build/website /srv/public/build/website/

RUN set -eux; \
	mkdir -p /srv/var/cache; \
	mkdir -p /srv/var/log; \
	mkdir -p /srv/var/apache2/log; \
	composer dump-autoload --classmap-authoritative --no-dev; \
	composer dump-env prod; \
	composer run-script --no-dev post-install-cmd; \
	chmod +x bin/console; sync
VOLUME /srv/var

RUN rm /srv/.env.local.php

# Copy apache config
COPY ./docker/apache-prod.conf /etc/apache2/sites-enabled/000-default.conf

# Add entrypoint
COPY ./docker/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint
ENTRYPOINT ["/usr/local/bin/entrypoint"]
