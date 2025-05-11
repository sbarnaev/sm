# Используем официальный образ PHP 7.2 + Apache
FROM php:7.2-apache

# Устанавливаем ionCube Loader
RUN curl -o ioncube.tar.gz https://downloads.ioncube.com/loader_downloads/ioncube_loaders_lin_x86-64.tar.gz \
    && tar -xzvf ioncube.tar.gz \
    && mv ioncube/ioncube_loader_lin_7.2.so /usr/local/lib/php/extensions/*/ \
    && echo "zend_extension=ioncube_loader_lin_7.2.so" > /usr/local/etc/php/conf.d/00-ioncube.ini \
    && rm -rf ioncube.tar.gz ioncube

# Добавьте эти строки для ZIP:
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    && docker-php-ext-install zip pdo_mysql

# Устанавливаем зависимости для Composer и модули PHP
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    && docker-php-ext-install pdo_mysql

# Устанавливаем Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Настраиваем Apache на порт 8080
RUN sed -i 's/Listen 80/Listen 8080/' /etc/apache2/ports.conf && \
    sed -i 's/<VirtualHost \*:80>/<VirtualHost \*:8080>/' /etc/apache2/sites-available/000-default.conf

EXPOSE 8080

# Копируем файлы проекта
COPY . /var/www/html/

# Устанавливаем зависимости из composer.json
RUN composer install --no-dev

# Даем права Apache
RUN chown -R www-data:www-data /var/www/html
