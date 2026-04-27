#!/usr/bin/env bash
# Bewusst kein `set -o pipefail`: `yes y | yarn set version berry` wirft sonst
# Exit 141 (SIGPIPE), wenn yarn fertig ist und `yes` weiterhin schreibt.
set -eu

##############################################################
# Warten auf MySQL
##############################################################
until nc -z -v -w30 mysql 3306; do
    echo "Waiting for MySQL..."
    sleep 5
done

##############################################################
# Verzeichnisstruktur und Permissions
##############################################################
mkdir -p var/cache var/log config/jwt public/build public/bundles public/images
mkdir -p /var/log/apache2  # access.log/error.log gehen weiter dort hin (ELK)
bin/set_owner.sh
bin/set_acl.sh "${CONTAINER_USER}"

yes y | yarn set version berry

##############################################################
# Environment-spezifische Setup-Schritte
##############################################################
if [ "${APP_ENVIRONMENT}" = "dev" ]; then
    APP_ENVIRONMENT=${APP_ENVIRONMENT} gosu "${CONTAINER_USER}" composer install
    gosu "${CONTAINER_USER}" php bin/console assets:install --env="${APP_ENVIRONMENT}"
    gosu "${CONTAINER_USER}" vendor/bin/bdi detect drivers
    gosu "${CONTAINER_USER}" vendor/bin/captainhook install -f
elif [ "${APP_ENVIRONMENT}" = "prod" ]; then
    gosu "${CONTAINER_USER}" php bin/console doctrine:migrations:sync-metadata-storage --env="${APP_ENVIRONMENT}" --no-debug --no-interaction
    gosu "${CONTAINER_USER}" php bin/console doctrine:database:create --if-not-exists --no-interaction --env="${APP_ENVIRONMENT}"
    gosu "${CONTAINER_USER}" php bin/console doctrine:migrations:migrate --no-interaction --env="${APP_ENVIRONMENT}"
    gosu "${CONTAINER_USER}" php bin/console assets:install --env="${APP_ENVIRONMENT}"
fi

##############################################################
# FrankenPHP starten (statt apache2-foreground)
# - --config zeigt auf den eingebauten Caddyfile
# - exec, damit Signale (SIGTERM bei docker stop) sauber durchgereicht werden
##############################################################
exec frankenphp run --config /etc/caddy/Caddyfile
