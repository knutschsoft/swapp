#!/usr/bin/env bash
CONTAINER_USER=$1
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

# Liste der zu bearbeitenden Verzeichnisse
DIRECTORIES=(
  "${DIR}/../var/"
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

# ACL setzen
if [ -n "$CONTAINER_USER" ]; then
  setfacl -R -m u:www-data:rwx -m u:"$CONTAINER_USER":rwx -m m:rwx "${DIRECTORIES[@]}"
  setfacl -dR -m u:www-data:rwx -m u:"$CONTAINER_USER":rwx -m m:rwx "${DIRECTORIES[@]}"
else
  setfacl -R -m u:www-data:rwx -m m:rwx "${DIRECTORIES[@]}"
  setfacl -dR -m u:www-data:rwx -m m:rwx "${DIRECTORIES[@]}"
fi
