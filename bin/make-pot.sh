#!/usr/bin/env sh


CUSTOM_PLUGIN_PATH="${WP_PATH}/wp-content/plugins/${WP_CUSTOM_PLUGIN}"
wp i18n make-pot "$CUSTOM_PLUGIN_PATH" "$CUSTOM_PLUGIN_PATH/languages/${WP_CUSTOM_PLUGIN}.pot" --exclude=node_modules,vendor
