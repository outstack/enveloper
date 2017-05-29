FROM php:7-fpm-alpine
RUN apk update \
    && apk add openssl \
    && apk add bash \
#    && apk add curl \
    && apk add caddy \
    && wget https://raw.githubusercontent.com/chrismytton/shoreman/380e745d1c2cd7bc163a1485ee57b20c76395198/shoreman.sh && chmod +x shoreman.sh && mv shoreman.sh /usr/local/bin/shoreman

COPY . /app
WORKDIR /app
RUN cp /app/infrastructure/php-fpm/php-fpm.conf /usr/local/etc/php-fpm.conf && \
    cp /app/infrastructure/php-fpm/www.conf     /usr/local/etc/php-fpm.d/www.conf

ENV SYMFONY_ENV prod

RUN infrastructure/scripts/install-composer.sh && \
    ./composer.phar install --optimize-autoloader --no-interaction --no-scripts && \
    chown -R www-data composer.phar var vendor
EXPOSE 80
CMD ["/usr/local/bin/shoreman"]