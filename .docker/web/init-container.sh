#!/usr/bin/env bash

APACHE_VHOST=/etc/apache2/sites-available/000-default.conf
APACHE_ROOT=/var/www/html
APACHE_HOST_NAME=swapp

# ###############################################
# Prepare Apache VHost
# ###############################################
rm "$APACHE_VHOST"

sh -c "echo 'ServerName ${APACHE_HOST_NAME}'                                            >> $APACHE_VHOST"
sh -c "echo '<VirtualHost *:80>'                                                        >> $APACHE_VHOST"
sh -c "echo '    ServerName ${APACHE_HOST_NAME}'                                        >> $APACHE_VHOST"
sh -c "echo '    DocumentRoot ${APACHE_ROOT}/web'                                       >> $APACHE_VHOST"
sh -c "echo '    <Directory ${APACHE_ROOT}/web>'                                        >> $APACHE_VHOST"
sh -c "echo '        # enable the .htaccess rewrites'                                   >> $APACHE_VHOST"
sh -c "echo '        AllowOverride All'                                                 >> $APACHE_VHOST"
sh -c "echo '        Order allow,deny'                                                  >> $APACHE_VHOST"
sh -c "echo '        Allow from All'                                                    >> $APACHE_VHOST"
sh -c "echo '    </Directory>'                                                          >> $APACHE_VHOST"
sh -c "echo '    ErrorLog /var/log/apache2/${APACHE_HOST_NAME}_error.log'               >> $APACHE_VHOST"
sh -c "echo '    CustomLog /var/log/apache2/${APACHE_HOST_NAME}_access.log combined'    >> $APACHE_VHOST"
sh -c "echo '</VirtualHost>'                                                            >> $APACHE_VHOST"

# ###############################################
# Prepare PHP
# ###############################################
sh -c "echo '[PHP]'                                                     >> /usr/local/etc/php/php.ini"
sh -c "echo '; Maximum amount of memory a script may consume'           >> /usr/local/etc/php/php.ini"
sh -c "echo 'memory_limit = -1'                                         >> /usr/local/etc/php/php.ini"
sh -c "echo '; Maximum allowed size for uploaded files.'                >> /usr/local/etc/php/php.ini"
sh -c "echo '; http://php.net/upload-max-filesize'                      >> /usr/local/etc/php/php.ini"
sh -c "echo 'upload_max_filesize = 10M'                                 >> /usr/local/etc/php/php.ini"
sh -c "echo ''                                                          >> /usr/local/etc/php/php.ini"
sh -c "echo '[Date]'                                                    >> /usr/local/etc/php/php.ini"
sh -c "echo '; Defines the default timezone used by the date functions' >> /usr/local/etc/php/php.ini"
sh -c "echo 'date.timezone=\"Europe/Berlin\"'                           >> /usr/local/etc/php/php.ini"

pecl install zip
pecl install intl
pecl install xdebug

sh -c "echo 'extension=zip.so'  >> /usr/local/etc/php/conf.d/zip.ini"
sh -c "echo 'extension=intl.so' >> /usr/local/etc/php/conf.d/intl.ini"

sh -c "echo 'zend_extension='$(find /usr/local/lib/php/extensions/ -name xdebug.so)  >  /usr/local/etc/php/conf.d/xdebug.ini"
sh -c "echo 'xdebug.remote_enable=1'                                                 >> /usr/local/etc/php/conf.d/xdebug.ini"
sh -c "echo 'xdebug.remote_autostart=1'                                              >> /usr/local/etc/php/conf.d/xdebug.ini"
sh -c "echo 'xdebug.remote_host=172.17.0.1'                                          >> /usr/local/etc/php/conf.d/xdebug.ini"

# ###############################################
# Install symfony installer
# ###############################################
curl -LsS https://symfony.com/installer -o /usr/local/bin/symfony
chmod a+x /usr/local/bin/symfony