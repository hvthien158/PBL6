FROM php:8.1-cli-alpine

WORKDIR /var/www/html

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN docker-php-ext-install mysqli pdo pdo_mysql

COPY . .

CMD ["php", "artisan", "serve", "--host", "0.0.0.0"]

EXPOSE 8000
