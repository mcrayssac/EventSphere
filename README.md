# EventSphere

EventSphere: Simplified Event Management in Symfony

## Notion Todo Tasks

Stay on top of your tasks with our [Notion Todo Tasks](https://www.notion.so/EventSphere-symfony-project-510f6a189f48422fbd4379a07b8b9e0c?pvs=4) board.

## Docker Container Setup
```bash
docker-compose up -d --build
docker exec -it event_sphere_app bash
```

## Project Installation

```bash
composer install
bin/console doctrine:database:create
bin/console doc:sc:up -f
bin/console doctrine:fixtures:load
yarn install 
yarn build 
symfony server:start 
```

It should run, but you are most likely to run into a database error when interacting with the database. **Please check the fix below**.

## Developer Notes

### Common Issues and Resolutions

**Error**: `SQLSTATE[HY000]General Error: 8 attempt to write a readonly database`

**Solution**:
```bash
sudo chown -R :www-data /EventSphere
sudo chmod -R 775 /EventSphere
```

### Running Unit Tests

1. **Set the Environment for Testing**:
    ```bash
    composer dump-env test
    ```

2. **Run All Tests**:
    ```bash
    php bin/phpunit
    ```

3. **Revert to Development Environment**:
    ```bash
    composer dump-env dev
    ```

**Note**: Remember to revert to the development environment to launch the server.


## Dev users
```text
email: user@example.com
password: userpwd123
```
```text
email: admin@example.com
password: adminpwd123
```