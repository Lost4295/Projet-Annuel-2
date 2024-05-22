FROM php:8.2.19-apache


ENV APACHE_RUN_USER=www-data
ENV APACHE_RUN_GROUP=www-data

# PHP
RUN apt-get update && apt-get upgrade -y
RUN apt-get install -y zlib1g-dev libwebp-dev libpng-dev && docker-php-ext-install gd && apt-get clean
RUN apt-get install libzip-dev -y && docker-php-ext-install zip && apt-get clean
RUN apt-get install -y libicu-dev && docker-php-ext-configure intl && docker-php-ext-install intl && apt-get clean
RUN  docker-php-ext-install pdo pdo_mysql
RUN docker-php-ext-install opcache
RUN pecl install xdebug && docker-php-ext-enable xdebug


# Composer
RUN apt-get install curl -y && apt-get clean
RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer


# Apache

RUN mkdir -p /var/log/apache2/80 
COPY ./conf/apache2.conf /etc/apache2/apache2.conf
COPY ./conf/website.conf /etc/apache2/sites-available/website.conf

COPY ./conf/deployweb.sh /deployweb.sh
RUN chmod +x /deployweb.sh
CMD [ "/deployweb.sh" ]
RUN a2enmod rewrite
RUN a2dissite 000-default
RUN a2ensite website.conf
RUN service apache2 restart
EXPOSE 80
WORKDIR /var/www/html/appWeb
