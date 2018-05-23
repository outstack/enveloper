#!/bin/bash
set -ev

# Cleanup
mkdir -p var/logs
echo "" > var/logs/test.log

COMPOSE="docker-compose -f ./docker-compose.yml -f ./docker-compose.travis.yml"

$COMPOSE run enveloper infrastructure/scripts/install-composer.sh
$COMPOSE run enveloper ./vendor/bin/simple-phpunit --filter=Unit
$COMPOSE run enveloper \
    sh -c "./bin/console --env=test cache:warmup && ./vendor/bin/simple-phpunit --filter=Functional"

