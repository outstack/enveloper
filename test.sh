#!/bin/bash

./bin/console cache:clear --env=test && \
./vendor/bin/simple-phpunit --filter=Unit && \
./vendor/bin/simple-phpunit --filter=Functional