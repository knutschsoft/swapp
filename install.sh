#curl -sS https://getcomposer.org/installer | php
php composer.phar install

php bin/console doctrine:database:drop --if-exists --force
php bin/console doctrine:database:create --if-not-exists

php bin/console doctrine:migrations:migrate --no-interaction

php bin/console hautelook_alice:doctrine:fixtures:load --no-interaction

#yarn install

./vendor/bin/phpunit
