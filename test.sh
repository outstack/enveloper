#!/bin/bash
./composer.phar install --no-scripts --no-interaction --no-autoloader
echo "" > var/logs/test.log
./bin/console cache:clear --env=test && \
./vendor/bin/simple-phpunit --filter=Unit && \
./vendor/bin/simple-phpunit --filter=Functional || cat var/logs/test.log