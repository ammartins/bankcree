FROM ubuntu:22.04

ADD app /srv/app
ADD composer.json /srv/composer.json
ADD composer.lock /srv/composer.lock
ADD renovate.json /srv/renovate.json
ADD src /srv/src
ADD web /srv/web

ARG DEBIAN_FRONTEND=noninteractive

RUN apt-get update && \
    apt-get dist-upgrade -y && \
    apt-get install sudo -y && \
    apt-get autoremove -y && \
    apt-get autoclean -y

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    nginx \
    libpng-dev \
    libonig-dev \
    gettext-base

RUN apt-get install software-properties-common -y && \
    add-apt-repository ppa:ondrej/php && \
    apt-get update && \
    apt-get install php7.3 php7.3-cli php7.3-xml php7.3-curl curl git libmysqlclient-dev zip unzip php-zip php7.3-mysql -y
RUN apt-get install curl git libmysqlclient-dev zip unzip php7.3-mysql -y

# Install Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN ls -lsa
RUN php composer-setup.php --version=1.6.3
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer

WORKDIR /srv/

RUN composer install --no-scripts --no-interaction
# COPY nginx/default.conf /etc/nginx/sites-available/default

ENV APP_ENV=prod

# Set up environment variables for database configuration
# Start PHP-FPM and Nginx

ENTRYPOINT php app/console server:run 0.0.0.0:8000
#CMD ["sh", "-c", "envsubst < /etc/nginx/sites-available/default > /etc/nginx/sites-available/default && nginx -g 'daemon off;'"]
