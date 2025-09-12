#!/usr/bin/env sh

# Wait for database connection
echo "waiting for mysql:${MYSQL_EXTERNAL_PORT}"
until nc -z mysql "${MYSQL_EXTERNAL_PORT}"; do
	sleep 1
done
echo "Can find mysql."
