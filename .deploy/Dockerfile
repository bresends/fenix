FROM serversideup/php:8.3-frankenphp AS php-dependencies

USER root

RUN install-php-extensions \
    intl \
    bcmath \
    gd \
    pdo_pgsql \
    redis \
    uuid \
    exif \
    pcntl \
    zip

USER www-data
WORKDIR /var/www/html

COPY --chown=www-data:www-data composer.json composer.lock ./

RUN composer install \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --no-dev \
    --no-scripts \
    --no-autoloader

FROM node:22-alpine AS frontend

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund

COPY resources ./resources
COPY postcss.config.js tailwind.config.js vite.config.js ./
COPY --from=php-dependencies /var/www/html/vendor ./vendor

RUN npm run build

FROM php-dependencies AS production

COPY --chown=www-data:www-data . ./
COPY --from=frontend --chown=www-data:www-data /app/public/build ./public/build

RUN composer dump-autoload \
    --no-interaction \
    --no-dev \
    --optimize

USER root

# Application code is immutable to the runtime user. Laravel only needs write
# access to its cache and storage directories.
RUN chown -R root:root /var/www/html \
    && chown -R www-data:www-data /var/www/html/bootstrap/cache /var/www/html/storage \
    && chmod -R u=rwX,g=rX,o=rX /var/www/html \
    && chmod -R u=rwX,g=rwX,o= /var/www/html/bootstrap/cache /var/www/html/storage

USER www-data
