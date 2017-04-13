#!/bin/sh

echo 'script starts'
git pull
php bin/console assets:install --symlink
php bin/console cache:clear --env=prod
php bin/console assetic:dump --env=prod --no-debug
php bin/console cache:warmup --env=prod
echo 'script finished'