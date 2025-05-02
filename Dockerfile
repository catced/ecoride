FROM php:8.2-apache

# Installer les extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    git unzip zip libicu-dev libpq-dev libzip-dev \
    && docker-php-ext-install intl pdo pdo_pgsql zip opcache

# Activer mod_rewrite pour Apache
RUN a2enmod rewrite

# Installer Composer en amont
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier uniquement composer.json et composer.lock pour éviter de casser le cache Docker
WORKDIR /var/www/html
COPY composer.json composer.lock ./

COPY . /var/www/html
WORKDIR /var/www/html
RUN apt-get update && apt-get install -y unzip git \
    && curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# Installer les dépendances PHP
#RUN composer install --no-scripts --no-dev --optimize-autoloader
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --optimize-autoloader

# Copier le reste du code après l'installation
COPY . .

# Exécuter les scripts Composer auto-scripts maintenant que tout est là
RUN composer run-script @auto-scripts || true

# Fixer le document root Apache sur /public
RUN sed -i 's!/var/www/html!/var/www/html/public!' /etc/apache2/sites-available/000-default.conf

# Donner les droits à Apache
RUN chown -R www-data:www-data /var/www/html

# Installer Symfony CLI (optionnel, utile pour dev mais pas toujours en prod)
RUN curl -sS https://get.symfony.com/cli/installer | bash && \
    mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

EXPOSE 80
