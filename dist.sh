#!/usr/bin/env bash
## Exit immediately if a command exits with a non-zero status.
set -e
#set distribution folder alias
dist="$HOME"/GoogleDrive-rick.roberts.9/ayso/s1/web/ayso1ref/certrs
config="$HOME"/Sites/AYSO/certrs/config
PHP=/usr/local/etc/php/7.1

## clear the screen
echo -e "\0033"

var=$(git rev-parse --abbrev-ref HEAD)
echo "  Deploying branch $var from Git repository..."
echo

echo "  Build public resources..."
./node_modules/.bin/gulp build
echo

echo "  Purge composer development items..."
## Disable xdebug for composer performance
if [ -e $PHP"/conf.d/ext-xdebug.ini" ]
then
    mv $PHP"/conf.d/ext-xdebug.ini" $PHP"/conf.d/ext-xdebug.~ini"
fi
composer install --no-dev
echo

echo "  Clear distribution folder..."
rm -f -r $dist
echo

echo "  Setup distribution folder..."
mkdir $dist
mkdir $dist/var
mkdir $dist/var/uploads
mkdir $dist/src
echo

echo "  Copying app folders to distribution..."
cp -f -r app $dist/app
cp -f -r vendor $dist/vendor
cp -f -r public $dist/public
cp -f -r templates $dist/templates
##cp -f -r $config $dist/config
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

echo "  Restore composer development items..."
## Restore xdebug
if [ -e $PHP"/conf.d/ext-xdebug.~ini" ]
then
    mv $PHP"/conf.d/ext-xdebug.~ini" $PHP"/conf.d/ext-xdebug.ini"
fi
composer install
echo

echo "...distribution complete"
echo
echo "As required, upload the config folder $config"
