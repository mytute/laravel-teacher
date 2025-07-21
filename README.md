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
laravel-app/
├── docker-compose.yml
├── Dockerfile
├── .env         ← Laravel env file (created after app)
└── (Laravel project files)

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
    volumes:
      - .:/var/www/html
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
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage

EXPOSE 80
CMD ["apache2-foreground"]
```

Build the Container & Install Laravel  
```bash
$ docker-compose build
$ docker-compose run --rm app composer create-project laravel/laravel .  # create laravel inside container
```

Configure Laravel .env for Docker DB  
```dotenv
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=rootpass
```

start laravel application  
```bash
$ docker compouse up -d
```

Test DB with Artisan (should create tables in your MySQL Docker DB)
```bash
$ docker exec -it laravel-app bash
>#  php artisan migrate
```



