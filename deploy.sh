rsync --verbose --delete --delete-after --delete-excluded --links --times --exclude=/app/cache/* --exclude=/app/logs/* --exclude=/web/app_*.php --exclude=/web/config.php --recursive --cvs-exclude * swapp:/var/www/swapp

# execute on server
ssh swapp 'chown -R www-data:www-data /var/www/swapp/web/ /var/www/swapp/app/logs/ /var/www/swapp/app/cache/'

# TODO insecure! :-/
ssh swapp 'chown -R www-data:www-data *'

ssh swapp 'cd /var/www/swapp/ && ./install.sh'
