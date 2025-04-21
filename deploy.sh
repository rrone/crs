#!/usr/bin/env bash
## Exit immediately if a command exits with a non-zero status.
set -e
#set folder aliases

ayso="$HOME"/development/_websites

dev="${ayso}"/_dev/crs
config="${dev}"/config

prod="${ayso}"/_services/crs.ayso1ref.com/crs

PHP=/usr/local/etc/php/8.1/conf.d

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
cp -f ./*.md "${prod}"
cp -f ./*.txt "${prod}"
cp -f .yarnrc.yml "${prod}"
cp -rf .yarn "${prod}"

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
    composer install --no-dev
    yarn workspaces focus --production

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

