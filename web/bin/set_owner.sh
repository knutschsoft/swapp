#!/usr/bin/env bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

chown -R www-data:www-data \
  ${DIR}/../var/cache/ \
  ${DIR}/../var/log/ \
  ${DIR}/../config/jwt/ \
  ${DIR}/../public/build/ \
  ${DIR}/../public/images/

#if [ "$#" -eq 1 ]; then
#  chmod -R g+w \
#      ${DIR}/../var/cache/ \
#      ${DIR}/../var/log/ \
#      ${DIR}/../var/spool/ \
#      ${DIR}/../var/test-spool/ \
#      ${DIR}/../var/sqlite \
#      ${DIR}/../uploads/ \
#      ${DIR}/../public/build/
#fi
