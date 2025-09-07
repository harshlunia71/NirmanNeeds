#!/usr/bin/env sh

function strip_quotes () {
	echo $* | xargs
}

# Wait for database connection
echo "waiting for mysql:${MYSQL_EXTERNAL_PORT}"
until nc -z mysql ${MYSQL_EXTERNAL_PORT}; do
	sleep 1
done
echo "Can find mysql."

# Install Wordpress
wp core install \
	--path="/var/www/html" \
	--url=${WP_EXTERNAL_URL} \
	--title="$(strip_quotes ${WP_TITLE})" \
	--admin_user="${WP_ADMIN_USER}" \
	--admin_password="${WP_ADMIN_PASSWORD}" \
	--admin_email="${WP_ADMIN_EMAIL}"

# Theme installation
wp theme install $(strip_quotes ${WP_THEME}) --activate
wp theme delete --all

# Delete unwanted plugins
wp plugin delete akismet hello

# Plugin installation
wp plugin install $(strip_quotes ${WP_PLUGINS_TO_INSTALL}) --activate

# Custom plugin activation
wp plugin activate ${WP_CUSTOM_PLUGINS_TO_ACTIVATE}
wp plugin auto-updates enable --all

echo -e "\nREPORT\n"

# List users
echo "===User List==="
wp user list
echo ""

# Show installed themes
echo "===Theme List==="
wp theme list
echo ""

# Show installed plugins
echo "===Plugin List==="
wp plugin list
echo ""


