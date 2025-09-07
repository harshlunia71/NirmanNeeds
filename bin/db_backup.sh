#!/usr/bin/env sh

PROJECT_PATH="$( dirname $( dirname $( realpath "$0" ) ) )"
BACKUP_FOLDER="$PROJECT_PATH/.backup"
BACKUP_SQL="$BACKUP_FOLDER/backup_$(date "+%Y-%m-%d_%H_%M").sql"

LS_ERROR_FILE="$( mktemp -q /tmp/nn.backup_ls_error-XXXXXX )"
LATEST_BACKUP=$( ls -t "$BACKUP_FOLDER"/*.sql.gz 2> $LS_ERROR_FILE | head -1 )
if [ -z "$LATEST_BACKUP" ]; then
	cat "$LS_ERROR_FILE" >&2
	echo "Ignoring... Continuing backup."
fi
rm "$LS_ERROR_FILE"

SQL_ERROR_FILE="$( mktemp -q /tmp/nn.backup_sql_error-XXXXXX )"

echo "Creating SQL backup. This may take a few mins..."
docker compose run --build -T --rm --remove-orphans wp-cli wp db export - > "$BACKUP_SQL" 2> "$SQL_ERROR_FILE"
if [ $? == 0 ]; then
	echo "Backup SQL created."
else
	cat "$SQL_ERROR_FILE" >&2
	exit 1;
fi
rm "$SQL_ERROR_FILE"

BACKUP_GZIP="$BACKUP_SQL.gz"

GZIP_ERROR_FILE="$( mktemp -q /tmp/nn.backup_gzip_error-XXXXXX )"
gzip -f "$BACKUP_SQL" 2> "$GZIP_ERROR_FILE"
if [ $? == 0 ]; then
	echo "Backup compressed."
else
	cat "$GZIP_ERROR_FILE" >&2
	exit 2;
fi
rm "$GZIP_ERROR_FILE"

cmp -s -- "$BACKUP_GZIP" "$LATEST_BACKUP"
if [ $? == 0 ]; then
	echo "Database has not changed."
	if [ "$BACKUP_GZIP" != "$LATEST_BACKUP" ]; then
		rm "$LATEST_BACKUP"
	fi
	exit 0;
fi

KEEP_BACKUP_DAYS=30
find "$BACKUP_FOLDER" -mtime "$KEEP_BACKUP_DAYS" -delete

