#!/bin/bash

DOMAIN="klaz.safe-pay.to"
REPO_NAME="klaz-safe-pay-master"

crontab -l > mycron
echo "* * * * * cd /var/www/${DOMAIN}/${REPO_NAME} && php artisan schedule:run >> /dev/null 2>&1" >> mycron
crontab mycron
rm mycron
