#!/bin/bash
set -ev

# Cleanup
echo "" > var/logs/test.log

COMPOSE="docker-compose -f ./docker-compose.yml -f ./docker-compose.test.yml"

$COMPOSE run enveloper composer install --no-scripts --no-interaction --optimize-autoloader
$COMPOSE run enveloper ./vendor/bin/simple-phpunit --filter=Unit
$COMPOSE run enveloper \
    sh -c "./bin/console --env=test cache:warmup && ./vendor/bin/simple-phpunit --filter=Functional || cat var/logs/test.log"

