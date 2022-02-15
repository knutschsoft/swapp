FROM composer:2 as composer
FROM php:8.1-apache

ARG APP_ENVIRONMENT=prod
ARG HOST_UID=1000
ARG HOST_GID=1000
ARG CONTAINER_USER=docker
ARG CONTAINER_GROUP=docker

ENV APP_ENVIRONMENT=${APP_ENVIRONMENT}
ENV HOST_UID=${HOST_UID}
ENV HOST_GID=${HOST_UID}
ENV CONTAINER_GROUP=${CONTAINER_GROUP}
ENV CONTAINER_USER=${CONTAINER_USER}
ENV PANTHER_NO_SANDBOX 1

COPY --from=composer /usr/bin/composer /usr/bin/composer

# replace shell with bash so we can source files
RUN rm /bin/sh && ln -s /bin/bash /bin/sh

RUN apt-get update && apt-get install -y \
        curl \
        gnupg \
        apt-transport-https

# set correct timezone
ENV TZ=Europe/Berlin
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# nvm environment variables
ENV NVM_DIR /usr/local/nvm
ENV NODE_VERSION 16.2.0
ENV NVM_VERSION 0.38.0
RUN mkdir $NVM_DIR

# install nvm
# https://github.com/creationix/nvm#install-script
RUN curl --silent -o- https://raw.githubusercontent.com/nvm-sh/nvm/v$NVM_VERSION/install.sh | bash

# install node and npm
RUN source $NVM_DIR/nvm.sh \
    && nvm install $NODE_VERSION \
    && nvm alias default $NODE_VERSION \
    && nvm use default

# add node and npm to path so the commands are available
ENV NODE_PATH $NVM_DIR/v$NODE_VERSION/lib/node_modules
ENV PATH $NVM_DIR/versions/node/v$NODE_VERSION/bin:$PATH

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
        gosu \
        acl \
        unzip \
        default-mysql-client

RUN docker-php-ext-configure opcache --enable-opcache && \
        docker-php-ext-install opcache && \
        docker-php-ext-install -j$(nproc) pdo_mysql && \
        pecl install apcu && \
        docker-php-ext-enable apcu && \
        docker-php-ext-configure gd --with-jpeg=/usr/include/ && \
        docker-php-ext-install gd && \
        docker-php-ext-configure intl && \
        docker-php-ext-install bcmath && \
        docker-php-ext-install intl

COPY .docker/web/etc/php/php.ini /usr/local/etc/php/
RUN docker-php-ext-install zip
RUN pecl install xdebug && docker-php-ext-enable xdebug

RUN sh -c "echo 'xdebug.remote_enable=1' >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini" && \
    sh -c "echo 'xdebug.remote_autostart=1' >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini" && \
    sh -c "echo 'xdebug.remote_host=172.17.0.1' >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini" && \
    sed -i -e 's/zend_extension/;zend_extension/g' /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini                                     >> /usr/local/etc/php/conf.d/xdebug.ini

### adjust panther ChromeDriver to installed chromium version
RUN  if [ "${APP_ENVIRONMENT}" = "test" ] || [ "${APP_ENVIRONMENT}" = "dev" ]; then \
        apt-get update && apt-get install -y libzip-dev zlib1g-dev chromium; \
    fi

COPY .docker/web/etc/apache2/sites-available/000-default.conf /etc/apache2/sites-enabled/
RUN a2enmod rewrite
RUN a2enmod headers
RUN a2enmod ssl
RUN a2enmod remoteip

RUN openssl genrsa -passout pass:x -out /etc/ssl/private/swapp.pass.key 2048
RUN openssl rsa -passin pass:x -in /etc/ssl/private/swapp.pass.key -out /etc/ssl/private/swapp.key
RUN openssl req -new -key /etc/ssl/private/swapp.key -out /etc/ssl/private/swapp.csr -batch
RUN openssl x509 -req -days 365 -in /etc/ssl/private/swapp.csr -signkey /etc/ssl/private/swapp.key -out /etc/ssl/private/swapp.crt

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser

RUN groupadd --gid ${HOST_GID} ${CONTAINER_GROUP} && \
    useradd --uid ${HOST_UID} --gid ${HOST_GID} -ms /bin/bash ${CONTAINER_USER} && \
    usermod -a -G www-data ${CONTAINER_USER} && \
    chown ${CONTAINER_USER}:${CONTAINER_GROUP} /usr/bin/composer

# contains dev-mode packages
RUN gosu ${CONTAINER_USER} composer global require "pyrech/composer-changelogs:^1.6" --prefer-dist --no-progress --classmap-authoritative

COPY .docker /

# USE DOCKER CACHE FOR VENDORS:
# 1st: copy composer-files & run composer install
# 2nd: copy project sources => changing of project sources does'n break cache for previous step
COPY composer.json composer.lock package.json yarn.lock symfony.lock /var/www/html/
RUN if [ "${APP_ENVIRONMENT}" != "dev" ]; then \
        chown -R ${CONTAINER_USER}:${CONTAINER_GROUP} /var/www/html; \
        cd /var/www/html; \
        gosu ${CONTAINER_USER} composer install --optimize-autoloader --no-interaction; \
        gosu ${CONTAINER_USER} yarn install; \
    fi

COPY . /var/www/html
RUN cd /var/www/html && \
    if [ "${APP_ENVIRONMENT}" != "dev" ]; then \
        chown -R ${CONTAINER_USER}:${CONTAINER_GROUP} /var/www/html; \
        bin/set_acl.sh ${CONTAINER_USER}; \
        gosu ${CONTAINER_USER} yarn encore production; \
        gosu ${CONTAINER_USER} php bin/console cache:clear --env="${APP_ENVIRONMENT}" --no-debug; \
    fi

# clear apt-cache for smaller docker images
RUN apt-get clean -y

COPY ./.docker/start-project.sh /start-project.sh

EXPOSE 443

WORKDIR /var/www/html

RUN ls -la

RUN chmod 777 /start-project.sh
ENTRYPOINT /start-project.sh
