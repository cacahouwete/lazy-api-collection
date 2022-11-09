#######################################################
####################### BASE ##########################
#######################################################
FROM php:8.0 AS base

ENV APPIZE_DEPS \
    libzip-dev

RUN apt-get update && apt-get install -y $APPIZE_DEPS $PHPIZE_DEPS unzip libzip4 && \
    mkdir -p /usr/src/php/ext/pcov && curl -fsSL https://pecl.php.net/get/pcov | tar xvz -C "/usr/src/php/ext/pcov" --strip 1 && \
    docker-php-ext-install -j$(nproc) pcov zip && \
    rm $PHP_INI_DIR/conf.d/docker-php-ext-pcov.ini

COPY --from=composer/composer:latest-bin /composer /usr/local/bin/composer

WORKDIR /srv

#######################
######### DEV #########
#######################

FROM base AS dev

RUN mkdir -p /usr/src/php/ext/xdebug && curl -fsSL https://pecl.php.net/get/xdebug | tar xvz -C "/usr/src/php/ext/xdebug" --strip 1 && \
    docker-php-ext-install -j$(nproc) xdebug && \
    rm $PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini && \
    apt-get purge -y --auto-remove $APPIZE_DEPS $PHPIZE_DEPS && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* && \
    docker-php-source delete

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

########################
######### TEST #########
########################
FROM base AS test

COPY ./ /srv

RUN composer install --no-scripts --classmap-authoritative

RUN rm $PHP_INI_DIR/php.ini && \
    mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" && \
    apt-get purge -y --auto-remove $APPIZE_DEPS $PHPIZE_DEPS && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* && \
    docker-php-source delete
