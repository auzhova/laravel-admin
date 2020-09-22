FROM php:7.3-fpm

ARG user_uid
RUN usermod -u ${user_uid? invalid argument} www-data
RUN groupmod -g ${user_uid? invalid argument} www-data

RUN apt-get update && apt-get upgrade -y  \
    $PHPIZE_DEPS \
    bash \
    git \
    libmagickwand-dev \
    libmcrypt-dev \
    libpng-dev \
    libwebp-dev \
    libzip-dev \
    libpq-dev \
    nodejs \
    npm \
    openssl \
    postgresql \
    sudo \
    sqlite \
    unzip \
    vim \
    wget \
    zip

RUN pecl install imagick
RUN docker-php-ext-enable imagick

RUN docker-php-ext-install \
    bcmath \
    gd \
    mbstring \
    pdo \
    pdo_pgsql \
    tokenizer \
    zip

RUN npm install apidoc -g

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin/ --filename=composer