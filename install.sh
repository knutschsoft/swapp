curl -sS https://getcomposer.org/installer | php
php composer.phar install

php app/console doctrine:database:drop --if-exists --force
php app/console doctrine:database:create --if-not-exists

#php app/console doctrine:migrations:migrate --no-interaction
#php app/console doctrine:schema:update --force --no-interaction
php app/console doctrine:schema:create --no-interaction

php app/console doctrine:fixtures:load --no-interaction

npm install
./node_modules/.bin/bower install

./bin/phpunit -c app
