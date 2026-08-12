#!/usr/bin/env bash
set -euo pipefail

: "${DB_HOST:?DB_HOST obrigatoria}"
: "${DB_NAME:?DB_NAME obrigatoria}"
: "${DB_USER:?DB_USER obrigatoria}"
: "${DB_PASSWORD:?DB_PASSWORD obrigatoria}"

BACKUP_DIR="${BACKUP_DIR:-./storage/backups}"
RETENTION_DAYS="${BACKUP_RETENTION_DAYS:-14}"
mkdir -p "$BACKUP_DIR"
chmod 700 "$BACKUP_DIR"

STAMP="$(date -u +%Y%m%dT%H%M%SZ)"
FILE="$BACKUP_DIR/${DB_NAME}_${STAMP}.sql.gz"
export MYSQL_PWD="$DB_PASSWORD"
mariadb-dump --host="$DB_HOST" --port="${DB_PORT:-3306}" --user="$DB_USER" --single-transaction --routines --triggers "$DB_NAME" | gzip -9 > "$FILE"
unset MYSQL_PWD
chmod 600 "$FILE"
find "$BACKUP_DIR" -type f -name '*.sql.gz' -mtime "+$RETENTION_DAYS" -delete
printf 'Backup criado: %s\n' "$FILE"
