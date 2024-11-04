FROM php:8.3-fpm-alpine3.19

# Install dependencies
RUN apk update && apk add --no-cache \
    linux-headers \
    gcc \
    musl-dev \
    make \
    mariadb-client \
    # for xdebug:
    $PHPIZE_DEPS \
    # for intl extension
    icu-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl curl-dev \
    libxml2-dev openssl-dev zlib-dev \
    nodejs npm \
    # NPM
    && npm install npm@latest -g \
    # Install Oniguruma manually
    && curl -LO https://github.com/kkos/oniguruma/releases/download/v6.9.9/onig-6.9.9.tar.gz \
    && tar -xzvf onig-6.9.9.tar.gz \
    && cd onig-6.9.9 \
    && ./configure \
    && make \
    && make install \
    && cd .. \
    && rm -rf onig-6.9.9.tar.gz onig-6.9.9

# Install extensions
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    # enable opcache
    && docker-php-ext-install opcache \
    # install intl
    && docker-php-ext-configure intl && docker-php-ext-install intl \
    # install bcmath
    && docker-php-ext-install bcmath \
    # xdebug
    && pecl install xdebug && docker-php-ext-enable xdebug && touch /var/log/xdebug.log && chmod a+w /var/log/xdebug.log

# Set working directory
WORKDIR /var/www

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
# Add user for laravel application
RUN addgroup -g 1000 www && \
    adduser -u 1000 -D -G www www
# Change current user to www
USER www

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
