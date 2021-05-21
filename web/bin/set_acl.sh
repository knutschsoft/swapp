#!/usr/bin/env bash

CONTAINER_USER=$1
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

if [ "$#" -eq 1 ]; then
    setfacl -R -m u:www-data:rwx -m u:${CONTAINER_USER}:rwx -m m:rwx \
      ${DIR}/../var/cache/ \
      ${DIR}/../var/log/ \
      ${DIR}/../config/jwt/ \
      ${DIR}/../public/build/ \
      ${DIR}/../public/images/

    setfacl -dR -m u:www-data:rwx -m u:${CONTAINER_USER}:rwx -m m:rwx \
      ${DIR}/../var/cache/ \
      ${DIR}/../var/log/ \
      ${DIR}/../config/jwt/ \
      ${DIR}/../public/build/ \
      ${DIR}/../public/images/
else
    setfacl -R -m u:www-data:rwx -m m:rwx \
      ${DIR}/../var/cache/ \
      ${DIR}/../var/log/ \
      ${DIR}/../config/jwt/ \
      ${DIR}/../public/build/ \
      ${DIR}/../public/images/

    setfacl -dR -m u:www-data:rwx -m m:rwx
      ${DIR}/../var/cache/ \
      ${DIR}/../var/log/ \
      ${DIR}/../config/jwt/ \
      ${DIR}/../public/build/ \
      ${DIR}/../public/images/
fi
