FROM php:7.2-apache

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    chrony \
    systemd \
    libmcrypt-dev \
  && rm -rf /var/lib/apt/lists


# Extensiones requeridas
RUN docker-php-ext-install pdo_mysql mysqli gd mbstring sockets soap dom

RUN docker-php-ext-enable gd

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

# Add cake and composer command to system path
ENV PATH="${PATH}:/var/www/html/lib/Cake/Console"
ENV PATH="${PATH}:/var/www/html/Vendor/bin"

# Set working directory
WORKDIR /var/www

# COPY apache site.conf file
COPY ./docker/apache/site.conf /etc/apache2/sites-available/000-default.conf

# Copy the source code into /var/www/html/ inside the image
COPY . .

# Create tmp directory and make it writable by the web server
RUN mkdir -p \
    tmp/cache/models \
    tmp/cache/persistent \
  && chown -R :www-data \
    tmp \
  && chmod -R 770 \
    tmp

# Enable Apache modules and restart
RUN a2enmod rewrite \
  && service apache2 restart