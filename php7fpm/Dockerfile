FROM php:7-fpm
# Install modules
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libmcrypt-dev \
    libpng12-dev \
    && docker-php-ext-install mbstring \
    && docker-php-ext-install gd
CMD ["php-fpm"]
