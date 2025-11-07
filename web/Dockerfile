FROM composer:2.8 AS composer
FROM node:22-bookworm-slim AS node
FROM php:8.4-apache

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
ENV PANTHER_NO_SANDBOX=1
ENV PANTHER_CHROME_ARGUMENTS='--ignore-certificate-errors'

COPY --from=composer /usr/bin/composer /usr/bin/composer
RUN apt-get update && apt-get install -y curl ca-certificates gnupg \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && corepack enable \
    && corepack prepare yarn@stable --activate \
    && yarn set version stable

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions relay redis

# replace shell with bash so we can source files
RUN rm /bin/sh && ln -s /bin/bash /bin/sh

RUN apt-get update && apt-get install -y \
        curl \
        gnupg \
        apt-transport-https

# set correct timezone
ENV TZ=Europe/Berlin
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN apt-get update && apt-get install -y \
        wget \
        openjdk-21-jre-headless \
        netcat-traditional \
        libicu-dev \
        libzip-dev \
        libjpeg-dev \
        libpng-dev \
        libssl-dev \
        git-core \
        chromium \
        libflac-dev \
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
RUN if [ "${APP_ENVIRONMENT}" = "test" ] || [ "${APP_ENVIRONMENT}" = "dev" ]; then \
    apt-get update && apt-get install -y wget unzip xvfb libnss3 libatk1.0-0 libatk-bridge2.0-0 libcups2 libdrm2 libxkbcommon0 libxcomposite1 libxrandr2 libgbm1 libpango-1.0-0 libasound2 && \
    wget -q https://storage.googleapis.com/chrome-for-testing-public/141.0.7390.122/linux64/chromedriver-linux64.zip && \
    unzip chromedriver-linux64.zip -d /usr/local/bin/ && \
    rm chromedriver-linux64.zip && \
    apt-get clean autoclean && apt-get autoremove -y; \
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
RUN gosu ${CONTAINER_USER} composer global config --no-plugins allow-plugins.pyrech/composer-changelogs true
RUN gosu ${CONTAINER_USER} composer global require "pyrech/composer-changelogs:^2.1" --prefer-dist --no-progress --classmap-authoritative

COPY .docker /

# USE DOCKER CACHE FOR VENDORS:
# 1st: copy composer-files & run composer install
# 2nd: copy project sources => changing of project sources does'n break cache for previous step
COPY composer.json composer.lock package.json yarn.lock .yarnrc.yml .yarn symfony.lock /var/www/html/
RUN if [ "${APP_ENVIRONMENT}" != "dev" ]; then \
        chown -R ${CONTAINER_USER}:${CONTAINER_GROUP} /var/www/html && \
        cd /var/www/html && \
        gosu ${CONTAINER_USER} composer install --optimize-autoloader --no-interaction --no-scripts && \
        gosu ${CONTAINER_USER} yarn install; \
    fi

COPY . /var/www/html
RUN cd /var/www/html && \
    if [ "${APP_ENVIRONMENT}" != "dev" ]; then \
        chown -R ${CONTAINER_USER}:${CONTAINER_GROUP} /var/www/html && \
        bin/set_owner.sh && \
        bin/set_acl.sh ${CONTAINER_USER} && \
        gosu ${CONTAINER_USER} composer create-api-client && \
        gosu ${CONTAINER_USER} yarn build && \
        gosu ${CONTAINER_USER} php bin/console cache:clear --env="${APP_ENVIRONMENT}" --no-debug; \
    fi

# clear apt-cache for smaller docker images
RUN apt-get clean -y

COPY ./.docker/start-project.sh /start-project.sh

EXPOSE 443

WORKDIR /var/www/html

RUN chmod 777 /start-project.sh
ENTRYPOINT /start-project.sh
