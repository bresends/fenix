ARG PHP_VERSION=${PHP_VERSION:-8.3}
FROM php:${PHP_VERSION}-fpm-alpine AS php-system-setup

# Install system dependencies
RUN apk add --no-cache dcron busybox-suid libcap curl zip unzip git

# Install PHP extensions
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/
RUN install-php-extensions intl bcmath gd pdo_mysql pdo_pgsql opcache redis uuid exif pcntl zip

# Install supervisord implementation
RUN apk update && apk add --no-cache supervisor

# Install caddy
COPY --from=caddy:2.2.1 /usr/bin/caddy /usr/local/bin/caddy
RUN setcap 'cap_net_bind_service=+ep' /usr/local/bin/caddy

# Install composer
COPY --from=composer/composer:2 /usr/bin/composer /usr/local/bin/composer

####### Observability #######

# Install New Relic
ARG NEW_RELIC_AGENT_VERSION
ARG NEW_RELIC_LICENSE_KEY
ARG NEW_RELIC_APPNAME

RUN \
    cd /tmp \
    # Discover the latest released version:
    && export NEW_RELIC_AGENT_VERSION=$(curl -s https://download.newrelic.com/php_agent/release/ | grep -o '[1-9][0-9]\?\(\.[0-9]\+\)\{3\}' | head -n1) \
    # Discover libc provider
    && export NR_INSTALL_PLATFORM=$(ldd --version 2>&1 | grep -q musl && echo "linux-musl" || echo "linux") \
    # Download the discovered version:
    && curl -o newrelic-php-agent.tar.gz https://download.newrelic.com/php_agent/release/newrelic-php5-${NEW_RELIC_AGENT_VERSION}-${NR_INSTALL_PLATFORM}.tar.gz \
    # Install the downloaded agent:
    && tar xzf newrelic-php-agent.tar.gz \
    && NR_INSTALL_USE_CP_NOT_LN=1 NR_INSTALL_SILENT=0 ./*/newrelic-install install \
    # Configure the agent to use license key from NEW_RELIC_LICENSE_KEY env var:
    && sed -ie 's/[ ;]*newrelic.license[[:space:]]=.*/newrelic.license=${NEW_RELIC_LICENSE_KEY}/' $(php-config --ini-dir)/newrelic.ini \
    # Configure the agent to use app name from NEW_RELIC_APP_NAME env var:
    && sed -ie 's/[ ;]*newrelic.appname[[:space:]]=.*/newrelic.appname=${NEW_RELIC_APP_NAME}/' $(php-config --ini-dir)/newrelic.ini \
    # Cleanup temporary files:
    && rm newrelic-php-agent.tar.gz && rm -rf newrelic-php5-*-linux



#######     APP     #######
FROM php-system-setup AS app-setup

# Set working directory
ENV LARAVEL_PATH=/srv/app
WORKDIR $LARAVEL_PATH

# Add non-root user: 'app'
ARG NON_ROOT_GROUP=${NON_ROOT_GROUP:-app}
ARG NON_ROOT_USER=${NON_ROOT_USER:-app}
RUN addgroup -S $NON_ROOT_GROUP && adduser -S $NON_ROOT_USER -G $NON_ROOT_GROUP
RUN addgroup $NON_ROOT_USER wheel

# Set cron job
COPY ./.deploy/config/crontab /etc/crontabs/$NON_ROOT_USER
RUN chmod 777 /usr/sbin/crond
RUN chown -R $NON_ROOT_USER:$NON_ROOT_GROUP /etc/crontabs/$NON_ROOT_USER && setcap cap_setgid=ep /usr/sbin/crond

# Ensure the storage directory has the correct ownership to show user uploaded data
RUN mkdir -p /srv/app/public/storage && chown -R $NON_ROOT_USER:$NON_ROOT_GROUP /srv/app/public/storage

# Fix New Relic permissions
RUN mkdir -p /var/log/newrelic /var/run/newrelic \
    && chown -R $NON_ROOT_USER:$NON_ROOT_GROUP /var/log/newrelic /var/run/newrelic \
    && chmod -R 775 /var/log/newrelic /var/run/newrelic

RUN touch /var/log/newrelic/newrelic-daemon.log \
    && chown $NON_ROOT_USER:$NON_ROOT_GROUP /var/log/newrelic/newrelic-daemon.log \
    && chmod 664 /var/log/newrelic/newrelic-daemon.log


# Switch to non-root 'app' user & install app dependencies
COPY composer.json composer.lock ./
RUN chown -R $NON_ROOT_USER:$NON_ROOT_GROUP $LARAVEL_PATH
USER $NON_ROOT_USER
RUN composer install --prefer-dist --no-scripts --no-dev --no-autoloader
RUN rm -rf /home/$NON_ROOT_USER/.composer

# Copy app
COPY --chown=$NON_ROOT_USER:$NON_ROOT_GROUP . $LARAVEL_PATH/
COPY ./.deploy/config/php/local.ini /usr/local/etc/php/conf.d/local.ini

# Install the `npm` package
USER root
RUN apk update && apk add --no-cache npm

RUN npm install && npm run build

RUN rm -rf node_modules

USER $NON_ROOT_USER

# Set any ENVs
ARG APP_KEY=${APP_KEY}
ARG APP_NAME=${APP_NAME}
ARG APP_URL=${APP_URL}
ARG APP_ENV=${APP_ENV}
ARG APP_DEBUG=${APP_DEBUG}

ARG LOG_CHANNEL=${LOG_CHANNEL}

ARG DB_CONNECTION=${DB_CONNECTION}
ARG DB_HOST=${DB_HOST}
ARG DB_PORT=${DB_PORT}
ARG DB_DATABASE=${DB_DATABASE}
ARG DB_USERNAME=${DB_USERNAME}
ARG DB_PASSWORD=${DB_PASSWORD}

ARG BROADCAST_DRIVER=${BROADCAST_DRIVER}
ARG CACHE_DRIVER=${CACHE_DRIVER}
ARG QUEUE_CONNECTION=${QUEUE_CONNECTION}
ARG SESSION_DRIVER=${SESSION_DRIVER}
ARG SESSION_LIFETIME=${SESSION_LIFETIME}

ARG REDIS_HOST=${REDIS_HOST}
ARG REDIS_PASSWORD=${REDIS_PASSWORD}
ARG REDIS_PORT=${REDIS_PORT}

ARG MAIL_MAILER=${MAIL_MAILER}
ARG MAIL_HOST=${MAIL_HOST}
ARG MAIL_PORT=${MAIL_PORT}
ARG MAIL_USERNAME=${MAIL_USERNAME}
ARG MAIL_PASSWORD=${MAIL_PASSWORD}
ARG MAIL_ENCRYPTION=${MAIL_ENCRYPTION}
ARG MAIL_FROM_ADDRESS=${MAIL_FROM_ADDRESS}
ARG MAIL_ENCRYPTION=${MAIL_ENCRYPTION}
ARG MAIL_FROM_NAME=${APP_NAME}

ARG PUSHER_APP_ID=${PUSHER_APP_ID}
ARG PUSHER_APP_KEY=${PUSHER_APP_KEY}
ARG PUSHER_APP_SECRET=${PUSHER_APP_SECRET}
ARG PUSHER_APP_CLUSTER=${PUSHER_APP_CLUSTER}

# Start app
EXPOSE 80
COPY ./.deploy/entrypoint.sh /

ENTRYPOINT ["sh", "/entrypoint.sh"]
