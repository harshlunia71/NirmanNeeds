#!/usr/bin/env sh

PROJECT_PATH="$( dirname $( dirname $(realpath "$0")))"
BACKUP_FOLDER="$PROJECT_PATH/.backup"
LS_ERROR_FILE="$( mktemp -q /tmp/nn.backup_ls_error-XXXXXX )"
INPUT_FILE="$BACKUP_FOLDER/$1"

if [ -n "$1" ] && [ -r "$INPUT_FILE" ]; then
	BACKUP_GZIP="$INPUT_FILE"
else
	if [ -n "$1" ]; then
		echo "Cannot read given backup file. Using latest backup instead."
	fi
	BACKUP_GZIP="$(ls -t $BACKUP_FOLDER/*.sql.gz 2> $LS_ERROR_FILE | head -1 )"
fi

if [ -z "$BACKUP_GZIP" ]; then
	echo "Could not find latest backup file."
	cat "$LS_ERROR_FILE" >&2
	exit 1;
elif ! [ -r "$BACKUP_GZIP" ]; then
	echo "Cannot read backup file."
	exit 2;
else
	echo "Validated backup file."
fi
rm "$LS_ERROR_FILE"

GZIP_ERROR_FILE="$( mktemp -q /tmp/nn.backup_gzip_error-XXXXXX )"

gunzip -fk "$BACKUP_GZIP" 2> "$GZIP_ERROR_FILE"
if [ $? == 0 ]; then
	echo "Backup file uncompressed."
else
	cat "$GZIP_ERROR_FILE" >&2
	exit 3
fi
rm "$GZIP_ERROR_FILE"

BACKUP_SQL="${BACKUP_GZIP%.*}"
SQL_ERROR_FILE="$( mktemp -q /tmp/nn.backup_sql_error-XXXXXX)"

echo "Restoring database. This may take a few mins..."
docker compose run -T --build --rm --remove-orphans wp-cli wp db import - < "$BACKUP_SQL" 2> "$SQL_ERROR_FILE"
if [ $? == 0 ]; then
	echo "Database restored."
	rm -f "$BACKUP_FOLDER"/*.sql "$SQL_ERROR_FILE"
	exit 0
else
	cat "$SQL_ERROR_FILE" >&2
	exit 4
fi
