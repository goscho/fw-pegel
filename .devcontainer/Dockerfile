FROM php:8.3-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    zip \
    && docker-php-ext-install zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Install Xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Copy PHP configuration
COPY php.ini /usr/local/etc/php/conf.d/99-xdebug.ini

# Copy Apache configuration
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# Enable Apache modules
RUN a2enmod rewrite

# Create a non-root user
RUN useradd -m -s /bin/bash vscode \
    && usermod -aG www-data vscode

# Set proper permissions
RUN chown -R vscode:www-data /var/www/html

# Switch to non-root user
USER vscode

WORKDIR /workspaces/php-api-slim
