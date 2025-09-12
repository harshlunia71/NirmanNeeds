#!/usr/bin/env sh

strip_quotes () {
	echo "$*" | xargs
}

# Wait for mysql connection
/usr/local/bin/nc-mysql

# Script to update wp options
wp eval-file "${SCRIPT_PATH}/wp-update-options.php"

# Ensure themes and plugins activated
wp theme activate "${WP_THEME}"
wp plugin activate $( strip_quotes "${WP_PLUGINS} ${WP_CUSTOM_PLUGIN}")
