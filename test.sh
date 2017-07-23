#!/bin/bash
./composer.phar install --no-scripts --no-interaction --no-autoloader
./bin/console cache:clear --env=test && \
./vendor/bin/simple-phpunit --filter=Unit && \
./vendor/bin/simple-phpunit --filter=Functional