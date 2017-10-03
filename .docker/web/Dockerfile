FROM php:5.6-apache

COPY ./init-container.sh /init-container.sh
COPY ./init-project.sh /init-project.sh

COPY . /var/www/html

# replace shell with bash so we can source files
RUN rm /bin/sh && ln -s /bin/bash /bin/sh

RUN apt-get update && apt-get install -y \
        wget \
        libicu-dev \
        libzip-dev \
        libjpeg-dev \
        libpng-dev \
        git-core \
        php5-sqlite \
        php5-intl \
        php5-mysql \
        php5-curl \
        php5-dev \
        acl \
        mysql-client && \
        docker-php-ext-configure gd --with-jpeg-dir=/usr/include/  &&  \
        docker-php-ext-install gd

# nvm environment variables
ENV NVM_DIR /usr/local/nvm
ENV NODE_VERSION 8.1.3

# install nvm
# https://github.com/creationix/nvm#install-script
RUN curl --silent -o- https://raw.githubusercontent.com/creationix/nvm/v0.33.2/install.sh | bash

# install node and npm
RUN source $NVM_DIR/nvm.sh \
    && nvm install $NODE_VERSION \
    && nvm alias default $NODE_VERSION \
    && nvm use default

# add node and npm to path so the commands are available
ENV NODE_PATH $NVM_DIR/v$NODE_VERSION/lib/node_modules
ENV PATH $NVM_DIR/versions/node/v$NODE_VERSION/bin:$PATH

# confirm installation
RUN node -v
RUN npm -v

RUN curl -o- -L https://yarnpkg.com/install.sh | bash

RUN docker-php-ext-install -j$(nproc) pdo_mysql
RUN ["/init-container.sh"]

WORKDIR /var/www/html

EXPOSE 80