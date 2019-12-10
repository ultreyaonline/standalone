#!/bin/bash

# DEPLOY SCRIPT FOR LARAVEL APPLICATION

# This file is intended to be run from a command shell.
# It may also be run via a webhook call from /public/deploy-webhook.php by passing a ?key=insert_token_word_here parameter to match a .env key named DEPLOY_SECRET_KEY

# You may optionally add more commands here, such as deploy notices to Sentry or Honeybadger

# NOTE: any deploy from git requires that github have a deploy key (read-only) matching this server, in order to be able to read the repo

#optionally change directory to where the application lives, if it's not in the same directory as this script
#cd /home/INSERT_SPECIFIC_DIRECTORY_PATH_HERE


if [ ! -f artisan ]; then
    pwd
    echo "Could not find artisan file. Please relocate this bash file or set the proper path."
    exit 1
fi

php artisan -V
php artisan down --message="Quick maintenance in progress..." --retry=20
# optionally shut down horizon or queues
# php artisan horizon:pause

# clear caches etc, and disable config-caching so env() calls will work during deploy scripts, until deploy is done
php artisan optimize:clear

if [ -d .git ]; then
    git pull origin master --force
else
    php artisan deploy:cloudways_git
fi

composer install --no-interaction --no-dev --prefer-dist --optimize-autoloader --no-suggest
php artisan migrate --force

#reset opcache by reloading php (usually requires sudo enabled for this user); update this command to suit the server's software
#sudo -S service php7.3-fpm reload

php artisan config:cache
php artisan event:cache
#  php artisan route:cache
php artisan up

# optionally restart horizon or queue workers
php artisan -v queue:restart
# php artisan horizon:terminate

