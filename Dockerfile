# Étape 1 : Préparer l'image système de base
FROM php:8.2.19-fpm as system

# Variables d'environnement
ENV OS=linux
ENV COMPOSER_HOME /var/composer
ENV COMPOSER_ALLOW_SUPERUSER 1

WORKDIR /app

# Installation des dépendances système
RUN apt-get update && apt-get install -y \
    wget \
    curl \
    git \
    libcurl4-gnutls-dev \
    zlib1g-dev \
    libicu-dev \
    g++ \
    libxml2-dev \
    libpq-dev \
    zip \
    libzip-dev \
    unzip \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libmcrypt-dev \
    libpng-dev \
    libxpm-dev \
    libjpeg-dev \
    libwebp-dev \
    gnupg2 \
    poppler-utils \
    libmagickwand-dev \
    imagemagick \
    ghostscript \
    libc-client-dev \
    libkrb5-dev \
    --no-install-recommends \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Configuration et installation des extensions PHP nécessaires
RUN docker-php-ext-install gettext sockets pdo pdo_pgsql intl opcache gd zip bcmath

# Installation de Composer
RUN mkdir /var/composer \
    && mkdir /var/composer/cache \
    && chmod -R 777 /var/composer/cache \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --2.4

# Étape 2 : Installer les dépendances PHP avec Composer
FROM system as builder

# Copier uniquement les fichiers nécessaires pour installer les dépendances
COPY composer.json composer.lock symfony.lock ./

# Installer les dépendances du projet
RUN set -eux; \
    composer install --prefer-dist --no-scripts --no-progress --no-suggest; \
    composer clear-cache

# Étape 3 : Préparer l'image d'exécution finale
FROM builder as runner

# Copier tout le code source de l'application
COPY ./ ./

# Réchauffer le cache Symfony
RUN bin/console cache:warmup

# Exposer le port utilisé par PHP-FPM
EXPOSE 8000

# Démarrer PHP-FPM en tant que processus principal
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]