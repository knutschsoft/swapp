#!/usr/bin/env bash

##############################################################
# wait for es services to be available
##############################################################

# enable/disable xdebug according to docker environment var
if [ "$PHP_XDEBUG_ENABLED" -eq "0" ]; then
    sed -i -e 's/zend_extension/;zend_extension/g' /usr/local/etc/php/conf.d/xdebug.ini
else
    sed -i -e "s/xdebug\.remote_host.*/xdebug.remote_host=$PHP_XDEBUG_REMOTE_HOST/g" /usr/local/etc/php/conf.d/xdebug.ini
fi

MYSQL_UP=$(nc -z -v -w30 mysql 3306 2>/dev/null; echo $?);
while [ $MYSQL_UP -ne 0 ]
do
    echo "Waiting for MySQL connection "
    sleep 5
    MYSQL_UP=$(nc -z -v -w30 mysql 3306 2>/dev/null; echo $?);
done


if [ "${APP_ENVIRONMENT}" = "dev" ]; then
    APP_ENV=${APP_ENVIRONMENT} composer self-update
    APP_ENV=${APP_ENVIRONMENT} composer install
    php bin/console assets:install --env=${APP_ENVIRONMENT}
    ##############################################################
    # fix permission problems
    ##############################################################
    groupadd --gid $HOST_GID $CONTAINER_GROUP
    useradd --uid $HOST_UID --gid $HOST_GID -ms /bin/bash $CONTAINER_USER
    chown -R $HOST_UID:$HOST_GID $PWD
    chown -R $CONTAINER_USER:$CONTAINER_GROUP /home/$CONTAINER_USER

    setfacl -R -m u:www-data:rwx -m u:$HOST_UID:rwx -m m:rwx var public/images public/screenshots
    setfacl -dR -m u:www-data:rwx -m u:$HOST_UID:rwx -m m:rwx var public/images public/screenshots
elif [ "${APP_ENVIRONMENT}" = "test" ]; then
    APP_ENV=${APP_ENVIRONMENT} composer self-update
    APP_ENV=${APP_ENVIRONMENT} composer install
    php bin/console doctrine:database:drop --full-database --force --env=${APP_ENVIRONMENT}
    php bin/console doctrine:database:create --no-interaction --env=${APP_ENVIRONMENT}
    php bin/console doctrine:schema:update --force --env=${APP_ENVIRONMENT}
    php bin/console assets:install --env=${APP_ENVIRONMENT}
    php bin/console hautelook:fixtures:load --no-interaction --env=${APP_ENVIRONMENT}
else
    APP_ENV=${APP_ENVIRONMENT} composer self-update
    APP_ENV=${APP_ENVIRONMENT} composer install --optimize-autoloader
    APP_ENV=${APP_ENVIRONMENT} composer dump-autoload --optimize
    php bin/console cache:clear --env=${APP_ENVIRONMENT} --no-debug
    php bin/console cache:warmup --env=${APP_ENVIRONMENT} --no-debug
    php bin/console doctrine:database:create --if-not-exists --no-interaction --env=${APP_ENVIRONMENT}
    php bin/console doctrine:migrations:migrate --no-interaction --env=${APP_ENVIRONMENT}
    php bin/console assets:install --env=${APP_ENVIRONMENT}
fi

if [ "${APP_ENVIRONMENT}" != "dev" ]; then
    #setfacl -R -m u:www-data:rwx -m m:rwx var public/uploads
    #setfacl -dR -m u:www-data:rwx -m m:rwx var public/uploads
    chown -R www-data:www-data var public/images public/screenshots
fi

##############################################################
# execute the default command
##############################################################
apache2-foreground
