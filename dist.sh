#!/usr/bin/env bash
## Exit immediately if a command exits with a non-zero status.
set -e
#set distribution folder alias
dist="$HOME"/GoogleDrive-ayso1sra/s1/web/ayso1ref/services/certrs
config="$HOME"/Sites/AYSO/certrs/config
PHP=/usr/local/etc/php/7.3/conf.d

## clear the screen
echo -e "\0033"

var=$(git rev-parse --abbrev-ref HEAD)
echo "  Deploying branch $var from Git repository..."
echo

echo "  Build public resources..."
./node_modules/.bin/gulp build
echo

echo "  Setup production environment..."
## Disable xdebug for composer performance
if [ -e $PHP"/ext-xdebug.ini" ]
then
    mv $PHP"/ext-xdebug.ini" $PHP"/ext-xdebug.~ini"
fi
echo "  Purge composer development items..."
composer install --no-dev
echo "  Purge yarn development items..."
yarn install --production=true
echo

echo "  Clear distribution folder..."
rm -f -r $dist
echo

echo "  Setup distribution folder..."
mkdir $dist
mkdir $dist/var
mkdir $dist/var/uploads
mkdir $dist/var/node_modules
mkdir $dist/src
mkdir $dist/config
echo

echo "  Copying app folders to distribution..."
cp -f -r app $dist/app
cp -f -r vendor $dist/vendor
cp -f -r node_modules $dist/node_modules
cp -f -r public $dist/public
cp -f -r templates $dist/templates
cp -f -r $config/config_prod.php $dist/config/config.php
cp -f -r src/Action $dist/src
echo

echo "  Updating index to production..."
mv -f $dist/public/app_prod.php $dist/public/app.php
echo

echo "  Removing OSX jetsam..."
find $dist -type f -name '.DS_Store' -delete
echo

echo "  Removing development jetsam..."
find $dist -type f -name 'app_*' -delete
find $dist/src -type f -name '*Test.php' -delete
rm -f -r $dist/public/app_prod.php
rm -f -r $dist/public/app_dev.php
##rm -f -r $dist/config/.git
##find $dist/config -type f -name '.env' -delete
echo

echo "  Restore development environment..."
## Restore xdebug
if [ -e $PHP"/ext-xdebug.~ini" ]
then
    mv $PHP"/ext-xdebug.~ini" $PHP"/ext-xdebug.ini"
fi
echo "  Restore composer development items..."
composer install
echo "  Restore composer development items..."
yarn install
echo

echo "...distribution complete"
