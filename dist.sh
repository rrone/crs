#!/usr/bin/env bash
## Exit immediately if a command exits with a non-zero status.
set -e
#set distribution folder alias
src="$HOME"/Sites/AYSO/_dev/crs
dist="$HOME"/Sites/AYSO/_services/crs
config="$HOME"/Sites/AYSO/_dev/crs/config
PHP=/usr/local/etc/php/7.3/conf.d

## clear the screen
printf "\033c"

echo ">>> Checkout master branch from Git repository..."
#git checkout master
echo

echo ">>> Purge development items..."
## Disable xdebug for composer performance
if [[ -e ${PHP}"/ext-xdebug.ini" ]]
then
    mv "$PHP"/ext-xdebug.ini "$PHP"/ext-xdebug.~ini
fi

echo ">>> Clear distribution folder..."
rm -rf "${dist:?}"
mkdir "${dist}"
echo

echo ">>> Copying app to distribution..."
cp ./.env.dist "${dist}"/.env
cp -f ./*.json "${dist}"
cp -f ./*.lock "${dist}"

mkdir "${dist}"/bin
cp bin/console "${dist}"/bin

echo ">>> Copying config to distribution..."
cp -rf "${config}" "${dist}"

echo ">>> Clear distribution config..."
rm -rf "${dist}"/config/packages/dev
rm -rf "${dist}"/config/packages/test
rm -rf "${dist}"/config/routes/dev

cp -rf public "${dist}"
cp -rf src "${dist}"
mkdir  "${dist}"/var
cp -rf var/xlsx/ "${dist}"/var/xlsx/
cp -rf templates "${dist}"
echo

echo ">>> Removing OSX jetsam..."
find "${dist}" -type f -name '.DS_Store' -delete
echo

echo ">>> Removing development jetsam..."
find "${dist}"/src -type f -name '*Test.php' -delete
echo

cd "${dist}"
    cp -f -r "${src}"/assets .

    rm -f -r ./assets
    rm -f -r ./bin/doctrine*

    yarn install --prod=true
    composer install --no-dev

    bin/console cache:clear

cd "${src}"

echo ">>> Restore development items..."
## Restore xdebug
if [[ -e ${PHP}"/ext-xdebug.~ini" ]]
then
    mv "${PHP}"/ext-xdebug.~ini "${PHP}"/ext-xdebug.ini
fi
echo

echo "...distribution complete"

