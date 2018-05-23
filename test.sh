#!/bin/bash
set -e

# Cleanup
mkdir -p var/logs
echo "" > var/logs/test.log

COMPOSE="docker-compose -f ./docker-compose.yml -f ./docker-compose.tests.yml"

$COMPOSE run enveloper infrastructure/scripts/install-composer.sh
$COMPOSE run enveloper ./composer.phar install --no-scripts --no-interaction --optimize-autoloader
$COMPOSE run enveloper ./vendor/bin/simple-phpunit --filter=Unit
$COMPOSE run enveloper \
    sh -c "./bin/console --env=test cache:warmup && ./vendor/bin/simple-phpunit --filter=Functional"

