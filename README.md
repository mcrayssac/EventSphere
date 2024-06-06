# EventSphere
EventSphere: Simplified Event Management in Symfony

## Installation
```bash
docker-compose up -d --build
```
```bash
docker exec -it event_sphere_app bash
```

## DB Check
```bash
sqlite3 var/data.db
```
```bash
.tables
```
```bash
.schema event
```

## DB update
```bash
php bin/console doctrine:schema:update --force
```
```bash
php bin/console doctrine:fixtures:load
```

## Composer require
```bash
composer require symfony/security-bundle
```
```bash
composer require symfony/validator
```
```bash
composer require doctrine/annotations
```
```bash
composer require orm-fixtures --dev 
```
```bash
composer require symfony/maker-bundle --dev
```
```bash
composer require form
```

## Yarn install
```bash
yarn install
```
```bash
yarn encore dev
```