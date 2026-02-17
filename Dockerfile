FROM php:8.2-apache

RUN a2enmod rewrite

COPY . /var/www/html/

ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/sites-available/000-default.conf

RUN mkdir -p /var/www/html/storage && chown -R www-data:www-data /var/www/html/storage
