#!/bin/bash

if [ ! -f $PWD/vendor/autoload.php ]; then
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

$PWD/bin/phpspec run --format=pretty
