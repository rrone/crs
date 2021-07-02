#!/usr/bin/env bash
## Exit immediately if a command exits with a non-zero status.
set -e
#set folder aliases
ayso="$HOME"/Sites/AYSO
dev="${ayso}"/_dev/crs
config="${dev}"/config

prod="${ayso}"/_services/crs.ayso1ref.com

PHP=/usr/local/etc/php/8.0/conf.d

## clear the screen
printf "\033c"

echo ">>> Checkout master branch from Git repository..."
#git checkout master
echo

echo ">>> Build production assets..."
yarn encore production --progress
echo

echo ">>> Purge development items..."
## Disable xdebug for composer performance
if [[ -e ${PHP}"/ext-xdebug.ini" ]]
then
    mv "$PHP"/ext-xdebug.ini "$PHP"/ext-xdebug.~ini
fi

echo ">>> Clear distribution folder..."
rm -rf "${prod:?}"
mkdir "${prod}"
mkdir "${prod}"/crs
echo

echo ">>> Copying app to distribution..."
cp ./.env.dist "${prod}"/crs/.env
cp -f ./*.json "${prod}"/crs
cp -f ./*.lock "${prod}"/crs
cp -f .yarnrc.yml "${prod}"/crs
cp -rf .yarn "${prod}"/crs

mkdir "${prod}"/crs/bin
cp bin/console "${prod}"/crs/bin

echo ">>> Copying config to distribution..."
cp -rf "${config}" "${prod}"/crs

echo ">>> Clear distribution config..."
rm -rf "${prod}"/crs/config/packages/dev
rm -rf "${prod}"/crs/config/packages/test
rm -rf "${prod}"/crs/config/routes/dev

cp -rf public "${prod}/crs"
cp -rf src "${prod}/crs"
mkdir  "${prod}"/crs/var
cp -rf var/xlsx/ "${prod}"/crs/var/xlsx/
cp -rf templates "${prod}"/crs
echo

echo ">>> Removing OSX jetsam..."
find "${prod}" -type f -name '.DS_Store' -delete
echo

echo ">>> Removing development jetsam..."
find "${prod}"/crs/src -type f -name '*Test.php' -delete
echo

cd "${prod}"/crs
    yarn workspaces focus --production
    composer install --no-dev

    rm -f -r ./assets
    rm -f -r ./migrations
    rm -f -r ./webpack.config.js
    rm -f .yarnrc.yml
    rm -rf .yarn

    bin/console cache:clear

    ln -s public ../public_html

cd "${dev}"

echo ">>> Re-enable xdebug..."
## Restore xdebug
if [[ -e ${PHP}"/ext-xdebug.~ini" ]]
then
    mv "${PHP}"/ext-xdebug.~ini "${PHP}"/ext-xdebug.ini
fi
echo

echo "...distribution complete"

