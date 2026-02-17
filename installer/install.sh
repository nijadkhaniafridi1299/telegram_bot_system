#!/bin/bash

DOMAIN="klaz.safe-pay.to"
ZIP_PATH="/root/klaz-safe-pay-master.zip"
ZIP_NAME="klaz-safe-pay-master.zip"
REPO_NAME="klaz-safe-pay-master"

sudo apt-get update -y && sudo apt-get upgrade -y

sudo apt install -y ca-certificates apt-transport-https
sudo apt install -y software-properties-common

sudo add-apt-repository ppa:ondrej/php
sudo add-apt-repository ppa:ondrej/nginx-mainline -y

sudo apt-get update -y && sudo apt-get upgrade -y

sudo apt-get install -y php8.2-bcmath php8.2-bz2 php8.2-cli php8.2-common php8.2-curl php8.2-dev php8.2-gd php8.2-igbinary php8.2-imagick php8.2-imap php8.2-intl php8.2-mbstring php8.2-memcached php8.2-msgpack php8.2-mysql php8.2-opcache php8.2-pgsql php8.2-readline php8.2-redis php8.2-ssh2 php8.2-tidy php8.2-xml php8.2-xmlrpc php8.2-zip -y

sudo apt-get install -y unzip

sudo apt install curl acl nginx php8.2-fpm -y
sudo apt install nginx-extras -y
sudo systemctl enable nginx

#redis
sudo apt install -y redis-server
sudo sed -i -e 's/supervised no/supervised systemd/g' /etc/redis/redis.conf
sudo systemctl restart redis.service

cd /var/www
sudo mkdir ${DOMAIN}
sudo mv ${ZIP_PATH} /var/www/
sudo unzip ${ZIP_NAME} -d ${DOMAIN}

setfacl -Rm m:rwx ${DOMAIN}/
setfacl -Rdm m:rwx ${DOMAIN}/
setfacl -Rm www-data:rwx ${DOMAIN}/
setfacl -Rdm www-data:rwx ${DOMAIN}/

cd ./${DOMAIN}/${REPO_NAME}
cp .env.example .env

php -r "copy('https://getcomposer.org/installer', '/tmp/composer-setup.php');"
php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer
php -r "unlink('/tmp/composer-setup.php');"

curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs

sudo apt-get install -y supervisor
sudo systemctl enable supervisor

sudo apt install -y mysql-server
sudo systemctl enable mysql.service
sudo systemctl start mysql.service
