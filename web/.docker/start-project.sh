#!/usr/bin/env bash

##############################################################
# wait for es services to be available
##############################################################

# enable/disable xdebug:
# - only enable in dev environment
# - only enable if PHP_XDEBUG_ENABLED is set to 1
if [ "${APP_ENVIRONMENT}" != "dev" ] || [ "$PHP_XDEBUG_ENABLED" != "1" ]; then
    sed -i -e 's/zend_extension/;zend_extension/g' /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
else
    PHP_XDEBUG_REMOTE_HOST=$(hostname --ip-address | awk -F '.' '{printf "%d.%d.%d.1",$1,$2,$3}')

    sed -i -e "s/xdebug\.remote_host.*/xdebug.remote_host=$PHP_XDEBUG_REMOTE_HOST/g" /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
    sed -i -e 's/;zend_extension/zend_extension/g'                                   /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
fi

MYSQL_UP=$(nc -z -v -w30 mysql 3306 2>/dev/null; echo $?);
while [ $MYSQL_UP -ne 0 ]
do
    echo "Waiting for MySQL connection "
    sleep 5
    MYSQL_UP=$(nc -z -v -w30 mysql 3306 2>/dev/null; echo $?);
done

if [ "$APP_ENVIRONMENT" != 'prod' ] && [ ! -f config/jwt/private.pem ]; then
  jwt_passphrase=$(grep '^JWT_PASSPHRASE=' .env | cut -f 2 -d '=')
  if ! echo "$jwt_passphrase" | openssl pkey -in config/jwt/private.pem -passin stdin -noout > /dev/null 2>&1; then
    echo "Generating public / private keys for JWT"
    mkdir -p config/jwt
    echo "$jwt_passphrase" | openssl genpkey -out config/jwt/private.pem -pass stdin -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
    echo "$jwt_passphrase" | openssl pkey -in config/jwt/private.pem -passin stdin -out config/jwt/public.pem -pubout
    setfacl -R -m u:www-data:rX -m u:"$(whoami)":rwX config/jwt
    setfacl -dR -m u:www-data:rX -m u:"$(whoami)":rwX config/jwt
  fi
fi

if [ "${APP_ENVIRONMENT}" = "dev" ]; then
    APP_ENVIRONMENT=${APP_ENVIRONMENT} composer self-update --2
    APP_ENVIRONMENT=${APP_ENVIRONMENT} composer install
    php bin/console assets:install --env=${APP_ENVIRONMENT}
    vendor/bin/bdi detect drivers
    ##############################################################
    # fix permission problems
    ##############################################################
    groupadd --gid $HOST_GID $CONTAINER_GROUP
    useradd --uid $HOST_UID --gid $HOST_GID -ms /bin/bash $CONTAINER_USER
    chown -R $HOST_UID:$HOST_GID $PWD
    chown -R $CONTAINER_USER:$CONTAINER_GROUP /home/$CONTAINER_USER

    setfacl -R -m u:www-data:rwx -m u:$HOST_UID:rwx -m m:rwx var public/images
    setfacl -dR -m u:www-data:rwx -m u:$HOST_UID:rwx -m m:rwx var public/images
elif [ "${APP_ENVIRONMENT}" = "test" ]; then
    APP_ENVIRONMENT=${APP_ENVIRONMENT} composer self-update --2
    APP_ENVIRONMENT=${APP_ENVIRONMENT} composer install
else
    APP_ENVIRONMENT=${APP_ENVIRONMENT} composer self-update --2
    APP_ENVIRONMENT=${APP_ENVIRONMENT} composer install --optimize-autoloader
    APP_ENVIRONMENT=${APP_ENVIRONMENT} composer dump-autoload --optimize
    php bin/console doctrine:migrations:sync-metadata-storage --env=${APP_ENVIRONMENT} --no-debug --no-interaction
    php bin/console cache:clear --env=${APP_ENVIRONMENT} --no-debug
    php bin/console cache:warmup --env=${APP_ENVIRONMENT} --no-debug
    php bin/console doctrine:database:create --if-not-exists --no-interaction --env=${APP_ENVIRONMENT}
    php bin/console doctrine:migrations:migrate --no-interaction --env=${APP_ENVIRONMENT}
    php bin/console assets:install --env=${APP_ENVIRONMENT}
fi

if [ "${APP_ENVIRONMENT}" != "dev" ]; then
    #setfacl -R -m u:www-data:rwx -m m:rwx var public/uploads
    #setfacl -dR -m u:www-data:rwx -m m:rwx var public/uploads
    chown -R www-data:www-data var public/images
fi

##############################################################
# execute the default command
##############################################################
apache2-foreground
