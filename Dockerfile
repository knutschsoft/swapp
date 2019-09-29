FROM composer:1.9 as composer
FROM php:7.3-apache

ENV MYSQL_HOST=${MYSQL_HOST}
ENV MYSQL_USER=${MYSQL_USER}
ENV MYSQL_PASSWORD=${MYSQL_PASSWORD}
ENV MYSQL_DATABASE=${MYSQL_DATABASE}
ENV PROJECT_NAME_DOCKER=swapp

COPY --from=composer /usr/bin/composer /usr/bin/composer

# replace shell with bash so we can source files
RUN rm /bin/sh && ln -s /bin/bash /bin/sh

# set correct timezone
ENV TZ=Europe/Berlin
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# nvm environment variables
ENV NVM_DIR /usr/local/nvm
ENV NODE_VERSION 12.8.0
ENV NVM_VERSION 0.34.0
RUN mkdir $NVM_DIR

# install nvm
# https://github.com/creationix/nvm#install-script
RUN curl --silent -o- https://raw.githubusercontent.com/creationix/nvm/v$NVM_VERSION/install.sh | bash

# install node and npm
RUN source $NVM_DIR/nvm.sh \
    && nvm install $NODE_VERSION \
    && nvm alias default $NODE_VERSION \
    && nvm use default

# add node and npm to path so the commands are available
ENV NODE_PATH $NVM_DIR/v$NODE_VERSION/lib/node_modules
ENV PATH $NVM_DIR/versions/node/v$NODE_VERSION/bin:$PATH

# only needed for yarn :-/
RUN apt-get update && apt-get install -y \
        curl \
        gnupg \
        apt-transport-https

# yarn
RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add -
RUN echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list

RUN apt-get update && apt-get install -y \
        wget \
        netcat \
        yarn \
        libicu-dev \
        libzip-dev \
        libjpeg-dev \
        libpng-dev \
        libssl-dev \
        git-core \
        acl \
        default-mysql-client

RUN docker-php-ext-configure opcache --enable-opcache && \
        docker-php-ext-install opcache && \
        docker-php-ext-install -j$(nproc) pdo_mysql && \
        pecl install apcu && \
        docker-php-ext-enable apcu && \
        docker-php-ext-configure gd --with-jpeg-dir=/usr/include/ && \
        docker-php-ext-install gd && \
        docker-php-ext-configure intl && \
        docker-php-ext-install bcmath && \
        docker-php-ext-install intl

COPY .docker/web/etc/php/php.ini /usr/local/etc/php/
RUN pecl install zip
RUN pecl install xdebug

RUN echo "extension=zip.so"  >> /usr/local/etc/php/conf.d/zip.ini
RUN echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)"  >  /usr/local/etc/php/conf.d/xdebug.ini
RUN echo "xdebug.remote_enable=1"                                                 >> /usr/local/etc/php/conf.d/xdebug.ini
RUN echo "xdebug.remote_autostart=1"                                              >> /usr/local/etc/php/conf.d/xdebug.ini
RUN echo "xdebug.remote_host=172.17.0.1"                                          >> /usr/local/etc/php/conf.d/xdebug.ini

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /home/docker/.composer
# contains dev-mode packages
RUN composer global require "hirak/prestissimo:^0.3" "sllh/composer-versions-check:^2.0" "pyrech/composer-changelogs:^1.6" --prefer-dist --no-progress --no-suggest --classmap-authoritative

COPY .docker /

COPY composer.json composer.lock symfony.lock /var/www/html/
COPY src/Kernel.php /var/www/html/src/

RUN cd /var/www/html
RUN composer install --no-scripts --optimize-autoloader

COPY package.json yarn.lock /var/www/html/
RUN yarn install

WORKDIR /var/www/html
COPY . /var/www/html

ARG APP_ENVIRONMENT=prod
ENV APP_ENVIRONMENT=${APP_ENVIRONMENT}

RUN if [ "$APP_ENVIRONMENT" != "dev" ]; then \
        node_modules/.bin/encore production; \
    fi

COPY .docker/web/etc/apache2/sites-available/000-default.conf /etc/apache2/sites-enabled/
RUN a2enmod rewrite
RUN a2enmod headers
RUN a2enmod ssl

RUN openssl genrsa -passout pass:x -out /etc/ssl/private/swapp.pass.key 2048
RUN openssl rsa -passin pass:x -in /etc/ssl/private/swapp.pass.key -out /etc/ssl/private/swapp.key
RUN openssl req -new -key /etc/ssl/private/swapp.key -out /etc/ssl/private/swapp.csr -batch
RUN openssl x509 -req -days 365 -in /etc/ssl/private/swapp.csr -signkey /etc/ssl/private/swapp.key -out /etc/ssl/private/swapp.crt

COPY ./init-project.sh /init-project.sh

EXPOSE 80
EXPOSE 443

ENTRYPOINT /init-project.sh
