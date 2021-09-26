bin/console doctrine:database:drop --force
bin/console doctrine:database:create
bin/console doctrine:schema:update --force
bin/console doctrine:fixtures:load --append
bin/console cache:clear
symfony serve

