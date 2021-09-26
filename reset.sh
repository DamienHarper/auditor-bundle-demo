rm composer.lock
rm symfony.lock
rm -rf vendor/*
composer update
./reload.sh
