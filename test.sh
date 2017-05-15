#!/bin/bash

set -e
if [ ! -f phpunit.phar ]; then
    wget https://phar.phpunit.de/phpunit-5.7.phar -O ./phpunit.phar && chmod +x phpunit.phar
fi
./phpunit.phar --filter=Unit && \
./phpunit.phar --filter=Functional