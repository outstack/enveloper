FROM php:7-fpm-alpine
RUN apk update && apk add caddy && apk add supervisor
COPY . /app
WORKDIR /app
RUN php bin/console cache:clear --env=prod && php bin/console cache:clear --env=dev
RUN chown -R www-data var
EXPOSE 80
CMD ["/usr/bin/supervisord", "-c", "/app/infrastructure/supervisord/supervisord.conf"]