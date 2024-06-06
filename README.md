# EventSphere
EventSphere: Simplified Event Management in Symfony

## Installation
```bash
docker-compose up -d --build
docker exec -it event_sphere_app bash
```

## DB Check
```bash
sqlite3 var/data.db
.tables
.schema event
```

## DB update
```bash
php bin/console doctrine:schema:update --force
php bin/console doctrine:fixtures:load
```

## Composer require
```bash
composer require symfony/security-bundle
composer require symfony/validator
composer require doctrine/annotations
composer require orm-fixtures --dev 
composer require symfony/maker-bundle --dev
composer require form
```

## Yarn install
```bash
yarn install
yarn encore dev
```