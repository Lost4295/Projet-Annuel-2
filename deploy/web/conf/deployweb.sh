#!/bin/bash
php bin/console doctrine:schema:update --force
apachectl -D FOREGROUND