# Use an official PHP runtime as a parent image
FROM php:8.0-fpm

# Set working directory
WORKDIR /var/www/html

# Install dependencies including wget
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    wget \
    libsqlite3-dev \
    nodejs \
    npm \
    && docker-php-ext-install pdo pdo_sqlite

# Install Composer
ADD ./docker/install-composer.sh /install-composer.sh
RUN chmod +x /install-composer.sh && /install-composer.sh && rm -f /install-composer.sh
RUN composer self-update

# Install Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

# Copy existing application directory contents
COPY . /var/www/html

# Install Yarn
RUN npm install --global yarn

# Expose port 8000 and start php-fpm server
EXPOSE 8000
CMD ["php-fpm"]
