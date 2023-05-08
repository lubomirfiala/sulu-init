#!/bin/bash
set -e

# If we are running in dev env, install dev composer dependencies
if [ "$APP_ENV" != 'prod' ]; then
  composer install --prefer-dist --no-progress --no-interaction
fi

if [ $# -eq 0 ]; then
    # Clear cache before doing anything
    php /srv/bin/console cache:clear
#    php /srv/bin/adminconsole cache:clear
#    php /srv/bin/websiteconsole cache:clear

    # Make sure the database exists, if not, create it
#    php /srv/bin/console doctrine:database:create --no-interaction --if-not-exists

    # Always run DB migration on startup
#    php /srv/bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

    # Generate folder for apache2 logs if not exists
    mkdir -p /srv/var/apache2/log
    mkdir -p /srv/public/uploads/manual

    chown -R www-data:www-data /srv/var
    chown -R www-data:www-data /srv/public

    # No command was sent - run apache
    apache2-foreground
else
    # Command was sent, execute it
    exec "$@"
fi
