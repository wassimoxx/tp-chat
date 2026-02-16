FROM php:8.2-apache

# Enable Apache rewrite module
RUN a2enmod rewrite

# Copy project files into container
COPY . /var/www/html/

# Set Apache document root to /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/sites-available/000-default.conf
RUN sed -ri "s!/var/www/!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/apache2.conf

# Allow access to public directory
RUN printf '<Directory /var/www/html/public>\n\
AllowOverride All\n\
Require all granted\n\
</Directory>' > /etc/apache2/conf-available/public.conf \
&& a2enconf public

# Ensure storage folder exists and writable
RUN mkdir -p /var/www/html/storage \
 && chown -R www-data:www-data /var/www/html/storage \
 && chmod -R 775 /var/www/html/storage

# Expose port (Render auto-detects but safe)
EXPOSE 80
