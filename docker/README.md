# laravel-teacher


## Composer  

Composer is a tool for dependency management in PHP. It allows you to declare the libraries your project depends on and it will manage (install/update) them for you.  
Composer is not a package manager in the same sense as NPM or YARN are. but, it deals with "packages" or libraries.  

to install composer on fedora  
```bash
$ 
```

### 

folder structure 
```bash
/your-project
├── docker/
│   ├── Dockerfile
│   └── docker-compose.yaml
├── src/ ← Laravel will be installed here

```

create project folder   
```bash
$ mkdir laravel-app && cd laravel-app
```

docker compose file  
```yaml
version: '3.8'

services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: laravel-app
    ports:
      - "8000:80"
      - "5173:5173" 
    volumes:
      - ../src:/var/www/html
    depends_on:
      - db

  # MySQL Database
  db:
    image: mysql:8.0
    container_name: mysql-db
    restart: always
    ports:
      - "3307:3306"
    environment:
      MYSQL_DATABASE: iplanner_live_0325
      MYSQL_ROOT_PASSWORD: rootpass
    volumes:
      - db_data:/var/lib/mysql

volumes:
  db_data:
```

Dockerfile  
```yaml
FROM php:8.2-apache

# Install PHP extensions
RUN apt-get update && apt-get install -y \
    git unzip zip curl nodejs npm libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy Composer from official image
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Set public directory as default document root
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Permissions fix
# RUN chown -R www-data:www-data /var/www/html \
#    && chmod -R 755 /var/www/html/storage

EXPOSE 80
CMD ["apache2-foreground"]
```

Build the Container & Install Laravel  
```bash
$ docker compose build
$ docker compose up -d
```

Not to track permission change for git   
```bash
> git config core.fileMode false # only inside current repo
> git config --global core.fileMode false # for all the repo in your machine
```

Set permission  
```bash
$ docker compose exec app bash
$ composer create-project laravel/laravel .
$ chmod -R 775 storage bootstrap/cache
$ chown -R www-data:www-data storage bootstrap/cache
$ exit
```

Configure Laravel .env for Docker DB  
```dotenv
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306 # in network 3306 and out network 3307
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=rootpass
```

start laravel application  
```bash
$ docker compose build 
$ docker compose up -d
```

Test DB with Artisan (should create tables in your MySQL Docker DB)
```bash
$ docker exec -it laravel-app bash
># composer install
># php artisan config:clear
># php artisan config:cache
># php artisan migrate:fresh
>#  php artisan migrate
># php artisan db:seed
```
set permission to edit files inside src folder 
```bash
$ cd /path/to/laravel-docker-project/src 
$ sudo chown -R $USER:$USER src
$ chmod -R u+rwX src
$ docker compose down 
$ docker compose up -d
```
mysql public key issue
```bash
ALTER USER 'root'@'%' IDENTIFIED WITH mysql_native_password BY 'rootpass';
FLUSH PRIVILEGES;
```

add .gitignore file  
```bash
# Laravel
/vendor
/node_modules
/public/storage
/public/hot
/public/build
/public/mix-manifest.json
/public/js/*.js
/public/css/*.css
/public/js/*.map
/public/css/*.map

/storage/*.key
/storage/app/public
/storage/debugbar
/storage/framework/cache/*
/storage/framework/sessions/*
/storage/framework/testing/*
/storage/framework/views/*
/storage/logs/*

.env
.env.backup
.env.production
.env.*.local

.phpunit.result.cache
/.phpunit.cache
/phpunit.result.cache

# Laravel IDE helpers
/_ide_helper.php
/_ide_helper_models.php

# Docker-related
docker-compose.override.yml
docker/.env
docker/**/logs
*.log

# Homestead
Homestead.json
Homestead.yaml

# Authentication credentials
auth.json

# Editor/IDE settings
/.idea
/.vscode
/.fleet

# OS files
.DS_Store
Thumbs.db

# Composer
composer.lock

# NPM/Yarn logs
npm-debug.log*
yarn-debug.log*
yarn-error.log*

# Laravel Sail vendor binaries
/vendor-bin

# Code coverage
coverage/
```

to check docker logs  
```bash
$ docker logs -f mysql-db # -f or --follow
```
to check Laravel logs  
```php
 logger()->debug('CentralProvider config in getTokenUrl:', $this->config); 
```
```bash
# check logs
tail -f storage/logs/laravel.log
```

Clear Laravel caches (best practice)
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```
Get mysql dump  
```bash
$ docker exec -i 1a7d5d78a49d  mysqldump -u root -p metax_d > metax_d_backup4.sql # this will create dump file on where docker compose file located.
$ docker cp metax_d_backup.sql d241ef896d55:/tmp/metax_d_backup.sql # copy laptop located metax_d_backup4.sql file in to docker's "/tmp" file.
$ docker exec d241ef896d55 ls -lh /tmp/metax_d_backup.sql # # check if above command copied or not.
$ docker exec -i d241ef896d55 mysql -uroot -proot -e "CREATE DATABASE IF NOT EXISTS meta_fts_d CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
$ docker exec -i d241ef896d55 sh -c 'mysql -uroot -proot meta_fts_d < /tmp/metax_d_backup.sql'
```


