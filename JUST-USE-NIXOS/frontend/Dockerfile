FROM php:8.0-apache

# Install SQLite3 extension
RUN apt-get update && apt-get install -y \
    sqlite3 \
    libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite \
    && rm -rf /var/lib/apt/lists/*


# Create app directory
WORKDIR /var/www/html

# Copy app
COPY www/ /var/www/html


RUN mkdir /var/www/html/database  && \
    chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html && \
    chmod +x /var/www/html/scripts/reset_db.sh && \
    /var/www/html/scripts/reset_db.sh


# Enable Apache to set correct read/write permissions
RUN a2enmod rewrite

