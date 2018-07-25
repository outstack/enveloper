FROM php:7.2-fpm-alpine as deps
COPY --from=composer:1.6 /usr/bin/composer /usr/bin/composer
WORKDIR /app
COPY app/AppKernel.php /app/app/
COPY app/AppCache.php /app/app/
COPY composer.json /app/
COPY composer.lock /app/
RUN composer install --optimize-autoloader --no-interaction --ignore-platform-reqs --no-scripts

FROM php:7.2-fpm-alpine
MAINTAINER Adam Quaile <adamquaile@gmail.com>
RUN apk update --no-cache \
    && apk add openssl \
    && apk add ca-certificates \
    && apk add zlib-dev \
    && apk add bash \
    && apk add nginx \
    && apk add zip \
    && apk add unzip \
    && docker-php-source extract \
    && docker-php-ext-install zip \
    && docker-php-ext-install bcmath \
    && docker-php-source delete \
    && wget https://raw.githubusercontent.com/chrismytton/shoreman/380e745d1c2cd7bc163a1485ee57b20c76395198/shoreman.sh && chmod +x shoreman.sh && mv shoreman.sh /usr/local/bin/shoreman

WORKDIR /app
COPY --from=deps /app/vendor /app/vendor
COPY . /app
RUN cp /app/infrastructure/php-fpm/php-fpm.conf /usr/local/etc/php-fpm.conf && \
    cp /app/infrastructure/php-fpm/www.conf     /usr/local/etc/php-fpm.d/www.conf && \
    cp /app/infrastructure/nginx/nginx.conf     /etc/nginx/nginx.conf && \
    cp /app/infrastructure/nginx/vhost.conf     /etc/nginx/conf.d/default.conf

RUN ln -sf /dev/stdout /var/log/nginx/access.log && ln -sf /dev/stderr /var/log/nginx/error.log

ENV SYMFONY_ENV prod
EXPOSE 8080
RUN addgroup enveloper && adduser -D -G enveloper enveloper && \
    chown -R enveloper:enveloper \
        /app \
        /var/lib/nginx/ \
        /etc/nginx \
        /var/tmp/nginx

USER enveloper
CMD ["/usr/local/bin/shoreman"]