FROM php:7.0-apache
COPY php.ini /usr/local/etc/php/
RUN mkdir /usr/local/etc/php/session
RUN chown -R root.root /usr/local/etc/php/session
RUN chmod 777 /usr/local/etc/php/session
RUN apt-get update \
  && apt-get install -y libfreetype6-dev libjpeg62-turbo-dev libpng-dev libmcrypt-dev \
  && docker-php-ext-install pdo_mysql mysqli mbstring gd iconv mcrypt
