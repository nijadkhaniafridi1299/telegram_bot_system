# klaz-safe-pay

## Create a new Telegram bot

1. Contact @BotFather in Telegram
2. /newbot
3. Follow the instructions
4. Remember your bot username (it ends with _bot)
5. Remember your bot token (to access the HTTP API)
6. You can edit your bot with /mybots
7. Now we add our commands to the bot 
8. /mybots
9. Click on your bot
10. Click on "Edit Bot"
11. Click on "Edit Commands"
12. Copy and paste the following

```
help - Show bot commands help
create - Grab this ad and create a new product page from it
```

## Prepare the Installation

```
apt-get update -y && apt-get upgrade -y
```

Edit the installer/install.sh file and run it

```
chmod u+x ./install.sh
./install.sh
```


## Setup MySQL

Connect to your local MySQL server and create the database by using the following commands:

```
cat mysql_setup.txt
sudo mysql -u root
```

## Switch to the real website directory

```
cd /var/www/klaz.safe-pay.to/klaz-safe-pay-master
```

## Configuration (.env)

Copy the example env file if you do not have a .env file
```
cp .env.example .env
```

Now edit your .env file. Now you need the Telegram bot username and token.

You can also edit TELEGRAM_BOT_ADMIN_IDS later. When you write with your bot, it sends you your
Telegram chat id. If you edit the .env later, you have to deploy the website again (cd ./installer && ./deploy.sh)
```
nano .env
```

- Edit APP_URL
- Edit DB_DATABASE
- Edit DB_USERNAME
- Edit DB_PASSWORD
- Edit everything below the # =================
- Edit TELEGRAM_BOT_TOKEN
- Edit TELEGRAM_BOT_USERNAME
- Edit TELEGRAM_BOT_ADMIN_IDS
- Edit NOVA_LICENSE_KEY
- Edit NOVA_USERNAME
- Edit NOVA_PASSWORD


## Initial composer install

Composer is already installed. Now install the dependencies once.

```
composer install
```

Run the following command, to create the APP_KEY:
```
php artisan key:generate
```
Do not run this command a 2nd time. Other wise your passwords will be messed up in your database!

## Switch to the installer directory

```
cd installer
```

### Setup your web server

We use nginx. It is already installed.

We need to add a config for our website. Copy the nginx.conf
file to /etc/nginx/sites-available/<your-domain>

And then we create a symlink to enable the config.

You should use Cloudflare and create an origin certificate. Then you copy&paste the certificate and key in the cert.pem and key.pem file.

```
cp nginx.conf /etc/nginx/sites-available/klaz.safe-pay.to
ln -s /etc/nginx/sites-available/klaz.safe-pay.to /etc/nginx/sites-enabled/klaz.safe-pay.to

nano /etc/nginx/nginx.conf
# now replace YOUR-DOMAIN.de with your domain. there are 2 entries: "root YOUR-DOMAIN.de" -> "root klaz.pay-me-now.de"

mkdir /etc/nginx/certs
nano /etc/nginx/certs/cert.pem
nano /etc/nginx/certs/key.pem

nginx -t
systemctl restart nginx
```

If there is an error with the nginx server_names_hash_bucket_size, 
please increase the server_names_hash_bucket_size: 
https://stackoverflow.com/a/13906493

### Setup a cronjob for Laravel jobs

With this command you can edit the jobs manually. I recommend to select nano as the editor if you will be asked.
```
crontab -e
```

If needed, edit the DOMAIN and REPO_NAME value in cronjob_setup.sh

Now execute the script to create your cron job.

```
chmod u+x ./cronjob_setup.sh
./cronjob_setup.sh
crontab -l
```

### Setup a supervisor to monitor Horizon

Supervisor is already installed. Now we setup everything for Horizon.

Edit the horizon.conf file and change the command and stdout_logfile value if needed!

```
cp ./horizon.conf /etc/supervisor/conf.d/horizon.conf
```

Now we restart supervisor with the following script:

```
chmod u+x ./restart_supervisor.sh
./restart_supervisor.sh
```

## Update to a new version

Go to the project folder with cd.

```
cd ./installer
./deploy.sh
```

Now we are done.

## Helpful commands

### Change or set the password of a user

Helpful after the first installation to change the admin password.

```
php artisan user:change-pw
```

### Check if you know the password of a user

```
php artisan user:check-pw
```

## Helpful links

### Nova (Admin Panel)

```
/nova
```
https://your-domain.com/nova


### Horizon (Job management)

```
/horizon
```
https://your-domain.com/horizon


### Telescope (Debugging)

```
/telescope
```
https://your-domain.com/telescope
