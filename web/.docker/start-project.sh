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

bin/set_owner.sh
bin/set_acl.sh ${CONTAINER_USER}

if [ "$APP_ENVIRONMENT" != 'prod' ] && [ ! -f config/jwt/private.pem ]; then
  jwt_passphrase=${JWT_PASSPHRASE}
  if ! echo "$jwt_passphrase" | openssl pkey -in config/jwt/private.pem -passin stdin -noout > /dev/null 2>&1; then
    echo "Generating public / private keys for JWT"
    gosu ${CONTAINER_USER} echo "$jwt_passphrase" | gosu ${CONTAINER_USER} openssl genpkey -out config/jwt/private.pem -pass stdin -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
    gosu ${CONTAINER_USER} echo "$jwt_passphrase" | gosu ${CONTAINER_USER} openssl pkey -in config/jwt/private.pem -passin stdin -out config/jwt/public.pem -pubout
    setfacl -R -m u:www-data:rX -m u:"$(whoami)":rwX config/jwt
    setfacl -dR -m u:www-data:rX -m u:"$(whoami)":rwX config/jwt
  fi
fi

if [ "${APP_ENVIRONMENT}" = "dev" ]; then
    APP_ENVIRONMENT=${APP_ENVIRONMENT} gosu ${CONTAINER_USER} composer self-update --2
    APP_ENVIRONMENT=${APP_ENVIRONMENT} gosu ${CONTAINER_USER} composer install
    gosu ${CONTAINER_USER} php bin/console assets:install --env=${APP_ENVIRONMENT}
    gosu ${CONTAINER_USER} vendor/bin/bdi detect drivers

    setfacl -R -m u:www-data:rwx -m u:$HOST_UID:rwx -m m:rwx var public/images
    setfacl -dR -m u:www-data:rwx -m u:$HOST_UID:rwx -m m:rwx var public/images
elif [ "${APP_ENVIRONMENT}" = "prod" ]; then
    gosu ${CONTAINER_USER} php bin/console doctrine:migrations:sync-metadata-storage --env=${APP_ENVIRONMENT} --no-debug --no-interaction
    gosu ${CONTAINER_USER} php bin/console doctrine:database:create --if-not-exists --no-interaction --env=${APP_ENVIRONMENT}
    gosu ${CONTAINER_USER} php bin/console doctrine:migrations:migrate --no-interaction --env=${APP_ENVIRONMENT}
    gosu ${CONTAINER_USER} php bin/console assets:install --env=${APP_ENVIRONMENT}
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
