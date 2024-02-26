# The **Jen-storage** Project
## Requirements
- [Laravel 9.x](https://laravel.com/docs/9.x)
- [Docker >= 18.06.1-ce](https://docs.docker.com/install)
- [Docker-compose >= 1.29.0](https://docs.docker.com/compose/install)
- [PHP 8.1](https://www.php.net/downloads.php)
- [Nginx > = nginx/1.15.7](https://www.nginx.com/resources/wiki/start/topics/tutorials/install/)
- [Node >= v14.21.1](https://nodejs.org/en/download/)
- [Yarn >= 1.2.19](https://yarnpkg.com/en/docs/install#debian-stable)
- [PHP_CodeNiffer](https://siderlabs.com/blog/lets-clean-up-our-php-code-using-php-codesniffer-f4f18442a470/)
## Setup
- Copy file `.env.example` to `.env`.
- Modify `.env` config file (optional). If you modify the `mysql` configurations in `.env` file, remember to modify the configurations in `docker-compose.yml` file too.
- Create and start containers:
```BASH
docker-compose up -d
```
- Check all the containers: 
```
docker ps -a
```
## Installation
- Access the Workspace container:
```BASH
docker exec -it jen_workspace bash
```
- Install PHP packages:
```BASH
composer install
```
- Generate a new Application Key:
```
php artisan key:generate
```
- Run Migrations and Seeding command:
```BASH
php artisan migrate --seed

# Or running outside the docker container:
docker exec -it jen_workspace php artisan migrate --seed
```
- Generate a new Internal Password Grant OAuth2 clients:
```bash
php artisan passport:install
```
- The website will be published on 127.0.0.1:{`ports`} (`ports` is configured in `docker-compose.yml` > `services` > `nginx` > `ports`). 
- You can add the domain to the host file so we can access the website by domain:{`ports`} (edit the host in `./etc/hosts`).
- If you want to run the project on your local instead of Docker, just skip all steps about the Docker and create a virtual host. And modify `.env` config of `DB_HOST`, `DB_HOST_TEST` to `127.0.0.1`.
- **After running the Docker successfully, you can visit the website via `http://localhost:8888`.**
## Queue
- Run Queue command:
```
php artisan queue:work
```
## Convention
- Check coding conventions with the PSR2 rule:
```bash
docker exec -it jen_workspace phpcs --standard=PSR2 app routes config
```
- Or exec to the container `jen_workspace` and run:
```bash
npm run psr2
```
