FROM php:8.2.19-apache

ENV APACHE_RUN_USER=www-data
ENV APACHE_RUN_GROUP=www-data

WORKDIR /usr/src/api
# PHP
RUN apt-get update && apt-get upgrade -y
RUN apt-get install -y zlib1g-dev libwebp-dev libpng-dev && docker-php-ext-install gd && apt-get clean
RUN apt-get install libzip-dev -y && docker-php-ext-install zip && apt-get clean

# Composer
RUN apt-get install curl -y
RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer


# Apache
COPY conf/apache2.conf /etc/apache2/apache2.conf
COPY conf/api.conf /etc/apache2/sites-available/api.conf

COPY ./conf/deployapi.sh /usr/src/api/deployapi.sh
CMD [ "sh", "./deployapi.sh" ]

RUN a2enmod rewrite
RUN a2ensite api.conf
RUN service apache2 restart
EXPOSE 80