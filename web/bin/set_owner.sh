#!/usr/bin/env bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

DIRECTORIES=(
  "${DIR}/../var/api-client/"
  "${DIR}/../var/cache/"
  "${DIR}/../var/log/"
  "${DIR}/../config/jwt/"
  "${DIR}/../public/build/"
  "${DIR}/../public/bundles/"
  "${DIR}/../public/images/"
)

# Verzeichnisse anlegen
for dir in "${DIRECTORIES[@]}"; do
  mkdir -p "$dir"
done

# Besitzer setzen
chown -R www-data:www-data "${DIRECTORIES[@]}"

