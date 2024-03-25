FROM php:8.2-fpm-alpine

# Install OS packages required at runtime
# Install system dependencies
RUN apk update && apk add --no-cache \
    zip \
    libzip-dev \
    zlib-dev \
    git ;

# Install PHP extensions
RUN docker-php-ext-install \
    mysqli \
    pdo \
    pdo_mysql \
    opcache \
    zip \
    bcmath ;

# Install Nginx and supervisor
# Ngnix needs a run directory
# Send log output to supervisord's standard I/O
# (Note that supervisord is process 1)

RUN apk add --no-cache nginx supervisor && \
    install -d /run/nginx && \
    ln -s /proc/1/fd/1 /var/log/nginx/access.log && \
    ln -s /proc/1/fd/2 /var/log/nginx/error.log;

# Copy Nginx configuration
COPY ../conf/nginx-http.conf    /etc/nginx/http.d/0-http.conf
COPY ../conf/nginx-default.conf /etc/nginx/http.d/default.conf

# Copy PHP-FPM configuration
COPY ../conf/php-fpm.conf /usr/local/etc/php-fpm.d/zz-docker_nginx.conf

# Copy Supervisor configuration
COPY ../conf/supervisord.conf         /etc/supervisor.d/supervisord.ini
COPY ../conf/supervisord-php-fpm.conf /etc/supervisor.d/php-fpm.ini
COPY ../conf/supervisord-nginx.conf   /etc/supervisor.d/nginx.ini
COPY conf/supervisord-queue.conf   /etc/supervisor.d/queue.ini

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Set working directory to where the application lives
WORKDIR /var/www/application

COPY . /var/www/application
# Install application dependencies
RUN composer install --no-dev

# Set command to run
CMD [ "supervisord", "--nodaemon", "-c", "/etc/supervisord.conf" ]

# Expose HTTP port
EXPOSE 80
