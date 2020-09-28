#!/bin/bash
set -e

# Cleanup
mkdir -p var/logs
echo "" > var/logs/test.log

COMPOSE="docker-compose -f ./docker-compose.yml -f ./docker-compose.tests.yml"

$COMPOSE run --rm enveloper sh -c '\
    infrastructure/scripts/install-composer.sh && \
    ./composer.phar install && \
    ./vendor/bin/simple-phpunit --filter=Unit && \
    ./bin/console --env=test cache:warmup && \
    ./vendor/bin/simple-phpunit --filter=Functional'

