curl -sS https://getcomposer.org/installer | php
php composer.phar install

php bin/console doctrine:database:drop --if-exists --force
php bin/console doctrine:database:create --if-not-exists

#php bin/console doctrine:migrations:migrate --no-interaction
#php bin/console doctrine:schema:update --force --no-interaction
php bin/console doctrine:schema:create --no-interaction

php bin/console doctrine:fixtures:load --no-interaction

npm install
./node_modules/.bin/bower install

./bin/phpunit -c app
