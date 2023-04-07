FROM php:8.2-fpm-alpine

WORKDIR /var/www/html

RUN docker-php-ext-install pdo pdo_mysql

# install XDebug [Begin]
RUN apk add --no-cache --update --virtual buildDeps autoconf

RUN apk add g++

RUN apk add --update linux-headers

RUN apk add make

RUN pecl install xdebug

RUN docker-php-ext-enable xdebug
# install XDebug [End]