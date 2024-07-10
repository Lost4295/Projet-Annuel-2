#!/bin/bash
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:schema:update --force
composer install
apachectl -D FOREGROUND