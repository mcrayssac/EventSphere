# EventSphere
EventSphere: Simplified Event Management in Symfony

## Notion todo tasks
[Notion todo tasks](https://www.notion.so/EventSphere-symfony-project-510f6a189f48422fbd4379a07b8b9e0c?pvs=4)

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
```bash
composer require knplabs/knp-paginator-bundle
```
```bash
composer require mailjet/mailjet-apiv3-php
```
```bash
composer require stripe/stripe-php
```

## Yarn install
```bash
yarn install
```
```bash
yarn encore dev
```

## NPM install
```bash
npm install --save-dev sass-loade^14.0.0 sass
```
```bash
npm run dev
```

## Dev users
```text
email: user@example.com
password: userpwd123
```
```text
email: admin@example.com
password: adminpwd123
```