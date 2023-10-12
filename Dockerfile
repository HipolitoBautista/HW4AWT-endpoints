FROM php:8.1-apache

RUN docker-php-ext-install mysqli pdo_mysql

RUN apt-get update && dpkg --configure -a \
    && apt-get install -y git vim
    
RUN pecl install redis && docker-php-ext-enable redis
ENV PHP_TIMEZONE America/Belize

RUN a2enmod rewrite

ARG USER_ID
ARG GROUP_ID

RUN addgroup --gid 1000 user
RUN adduser --disabled-password --gecos '' --uid 1000 --gid 1000 user
USER user
