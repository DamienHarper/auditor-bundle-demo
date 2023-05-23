# `auditor-bundle` demo application

This application demos what [damienharper/auditor-bundle](https://github.com/DamienHarper/auditor-bundle) can do.

## Usage
Before running the demo, you first need to install dev dependencies:

```bash
composer install
```

Then run the following commands to:
1. create the local database (SQLite)
2. populate it with demo data
3. clear the cache
4. start the local symfony web server

```bash
bin/console doctrine:database:drop --force
bin/console doctrine:database:create
bin/console doctrine:schema:update --force
bin/console doctrine:fixtures:load --append
bin/console cache:clear
symfony serve
```

Then open your browser and go to `https://127.0.0.1:8000/author/` to see the list of author entities.

## Screenshots

### Audit inventory
![Audit inventory](https://github.com/DamienHarper/auditor-bundle-demo/blob/master/screenshots/inventory.png)

### Entity history
![Entity history](https://github.com/DamienHarper/auditor-bundle-demo/blob/master/screenshots/entity_history.png)

### Entry details
![Entry details](https://github.com/DamienHarper/auditor-bundle-demo/blob/master/screenshots/entry_details.png)
