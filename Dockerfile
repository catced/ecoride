FROM php:8.2-apache

# Installer les extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    git unzip zip libicu-dev libpq-dev libzip-dev \
    && docker-php-ext-install intl pdo pdo_pgsql zip opcache

# Activer mod_rewrite pour Apache
RUN a2enmod rewrite

# Copier Composer depuis l'image officielle
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le dossier de travail
WORKDIR /var/www/html

# Copier le projet complet (incluant .env)
COPY . .

# Permettre l?exécution des scripts même en root
ENV COMPOSER_ALLOW_SUPERUSER=1

# Installer les dépendances PHP (avec scripts Symfony)
RUN composer install --no-dev --optimize-autoloader

# Fixer le document root Apache sur /public
RUN sed -i 's!/var/www/html!/var/www/html/public!' /etc/apache2/sites-available/000-default.conf

# Donner les droits à Apache
RUN chown -R www-data:www-data /var/www/html

# (Optionnel) Installer Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

EXPOSE 80
