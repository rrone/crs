#!/usr/bin/env bash
## Exit immediately if a command exits with a non-zero status.
set -e
#set distribution folder alias
dev="$HOME"/Sites/AYSO/_dev/crs
prod="$HOME"/Sites/AYSO/_services/crs
config="$HOME"/Sites/AYSO/_dev/crs/config
PHP=/usr/local/etc/php/7.4/conf.d

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
echo

echo ">>> Copying app to distribution..."
cp ./.env.dist "${prod}"/.env
cp -f ./*.json "${prod}"
cp -f ./*.lock "${prod}"

mkdir "${prod}"/bin
cp bin/console "${prod}"/bin

echo ">>> Copying config to distribution..."
cp -rf "${config}" "${prod}"

echo ">>> Clear distribution config..."
rm -rf "${prod}"/config/packages/dev
rm -rf "${prod}"/config/packages/test
rm -rf "${prod}"/config/routes/dev

cp -rf public "${prod}"
cp -rf src "${prod}"
mkdir  "${prod}"/var
cp -rf var/xlsx/ "${prod}"/var/xlsx/
cp -rf templates "${prod}"
echo

echo ">>> Removing OSX jetsam..."
find "${prod}" -type f -name '.DS_Store' -delete
echo

echo ">>> Removing development jetsam..."
find "${prod}"/src -type f -name '*Test.php' -delete
echo

cd "${prod}"
    yarn install --prod=true
    composer install --no-dev

    rm -f -r ./assets
    rm -f -r ./migrations
    rm -f -r ./webpack.config.js

    bin/console cache:clear

cd "${dev}"

echo ">>> Re-enable xdebug..."
## Restore xdebug
if [[ -e ${PHP}"/ext-xdebug.~ini" ]]
then
    mv "${PHP}"/ext-xdebug.~ini "${PHP}"/ext-xdebug.ini
fi
echo

echo "...distribution complete"

