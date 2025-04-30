# Utilise une image PHP officielle avec Apache
FROM php:8.2-apache

# Installer les extensions nécessaires à Symfony
RUN apt-get update && apt-get install -y \
    git unzip zip libicu-dev libpq-dev libzip-dev \
    && docker-php-ext-install intl pdo pdo_pgsql zip opcache

# Activer mod_rewrite pour Apache (utile pour Symfony routing)
RUN a2enmod rewrite

# Copier les fichiers du projet dans le conteneur
COPY . /var/www/html/

# Définir le working dir
WORKDIR /var/www/html

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Configurer Apache pour pointer vers le dossier public
RUN sed -i 's!/var/www/html!/var/www/html/public!' /etc/apache2/sites-available/000-default.conf

# Définir les permissions (optionnel, mais utile)
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
