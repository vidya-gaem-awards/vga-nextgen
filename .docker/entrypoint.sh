#!/bin/bash
set -ex

if [[ "${APP_ENV}" == "local" ]]; then
    php artisan optimize:clear
else
    php artisan optimize
    php artisan event:cache
    php artisan view:cache
    php artisan migrate --isolated --force
fi

exec /entrypoint "$@"
