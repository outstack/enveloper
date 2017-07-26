FROM php:7-fpm-alpine
MAINTAINER Adam Quaile <adamquaile@gmail.com>

RUN apk update --no-cache \
    && apk add openssl \
    && apk add ca-certificates \
    && apk add zlib-dev \
    && apk add bash \
    && apk add caddy \
    && apk add zip \
    && apk add unzip \
    && docker-php-source extract \
    && docker-php-ext-install zip \
    && docker-php-source delete \
    && wget https://raw.githubusercontent.com/chrismytton/shoreman/380e745d1c2cd7bc163a1485ee57b20c76395198/shoreman.sh && chmod +x shoreman.sh && mv shoreman.sh /usr/local/bin/shoreman

WORKDIR /app
COPY composer.json /app/
COPY composer.lock /app/
COPY app/AppKernel.php /app/app/
COPY app/AppCache.php /app/app/
COPY infrastructure/scripts/install-composer.sh /app/infrastructure/scripts/
RUN infrastructure/scripts/install-composer.sh && \
    ./composer.phar install --optimize-autoloader --no-interaction --no-scripts

COPY . /app
RUN cp /app/infrastructure/php-fpm/php-fpm.conf /usr/local/etc/php-fpm.conf && \
    cp /app/infrastructure/php-fpm/www.conf     /usr/local/etc/php-fpm.d/www.conf
RUN chown -R www-data composer.phar var vendor

ENV SYMFONY_ENV prod
EXPOSE 80
CMD ["/usr/local/bin/shoreman"]