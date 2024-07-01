
FROM php:8.2.19-apache AS base


ENV APACHE_RUN_USER=www-data
ENV APACHE_RUN_GROUP=www-data
# PHP: on installe les librairies nécessaires à lancer symfony, et on les configure
# à l'aide des commandes docker-php-ext-install et docker-php-ext-enable
RUN apt-get update && apt-get upgrade -y
RUN  docker-php-ext-install pdo pdo_mysql
RUN docker-php-ext-install opcache
RUN pecl install xdebug && docker-php-ext-enable xdebug


RUN apt-get install -y zlib1g-dev libwebp-dev libpng-dev && docker-php-ext-install gd && apt-get clean
RUN apt-get install libzip-dev -y && docker-php-ext-install zip && apt-get clean
RUN apt-get install -y libicu-dev && docker-php-ext-configure intl && docker-php-ext-install intl && apt-get clean
# Composer est un système de gestion de dépendances et de librairies pour PHP. Comme 
# Symfony est un framework PHP, il est nécessaire d'installer Composer pour pouvoir l'utiliser.
RUN apt-get install curl -y && apt-get clean
RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer
# Apache est notre serveur web. On a créé des dossiers pour séparer les logs de chaque vhost.
RUN mkdir -p /var/log/apache2/80 /var/log/apache2/443
# On active les vhosts et les modules apache qu'il nous faut.
COPY ./conf/apache2.conf /etc/apache2/apache2.conf
COPY ./conf/api.conf /etc/apache2/sites-available/api.conf
RUN a2enmod rewrite
RUN a2dissite 000-default
RUN a2ensite api.conf
# Notre script de déploiement est contenu dans deployapi. Nous l'installons et le rendons exécutable 
# pour pouvoir le lancer au démarrage du container.
COPY ./conf/deployapi.sh /deployapi.sh
RUN chmod +x /deployapi.sh
EXPOSE 80 443
WORKDIR /var/www/html/pcs_api
CMD [ "/deployapi.sh" ]

FROM base AS prod
# On copie le code source de notre application dans le dossier /var/www/html/pcs_api
# Finalement, on installe les fichiers de l'application.
COPY ../pcs_api /var/www/html/pcs_api
COPY ./api/conf/.env.prod.local /var/www/html/pcs_api/.env.local