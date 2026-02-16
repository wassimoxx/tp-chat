FROM php:8.2-apache

COPY . /var/www/html/

RUN a2enmod rewrite

# Set Apache document root to public
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Allow access to public directory
RUN printf '<Directory /var/www/html/public>\n\
AllowOverride All\n\
Require all granted\n\
</Directory>' > /etc/apache2/conf-available/public.conf \
&& a2enconf public

# Fix storage permissions
RUN mkdir -p /var/www/html/storage && chown -R www-data:www-data /var/www/html/storage
