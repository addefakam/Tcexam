FROM php:8.1-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libldap2-dev \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu/ \
    && docker-php-ext-install -j$(nproc) \
        mysqli \
        gd \
        zip \
        ldap \
        bcmath

# Enable Apache rewrite module
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Set permissions for TCExam directories
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /var/www/html/cache \
    && chmod -R 777 /var/www/html/admin/backup \
    && chmod -R 777 /var/www/html/images

# Expose port 80
EXPOSE 80

CMD ["apache2-foreground"]
