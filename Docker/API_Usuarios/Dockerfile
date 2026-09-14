FROM php:8.2-apache

# Extensión necesaria para conectar con MySQL vía mysqli
RUN docker-php-ext-install mysqli

# Apache debe servir desde /public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf /etc/apache2/apache2.conf

# Permite que las rutas tipo index.php?accion=... funcionen bien
RUN a2enmod rewrite

WORKDIR /var/www/html
COPY . .

EXPOSE 80
