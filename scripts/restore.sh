#!/usr/bin/env bash
set -euo pipefail

: "${DB_HOST:?DB_HOST obrigatoria}"
: "${DB_NAME:?DB_NAME obrigatoria}"
: "${DB_USER:?DB_USER obrigatoria}"
: "${DB_PASSWORD:?DB_PASSWORD obrigatoria}"

FILE="${1:-}"
if [[ -z "$FILE" || ! -f "$FILE" ]]; then
  echo "Uso: CONFIRM_RESTORE=YES bash scripts/restore.sh <backup.sql.gz>" >&2
  exit 2
fi
if [[ "${CONFIRM_RESTORE:-NO}" != "YES" ]]; then
  echo "Restore bloqueado. Defina CONFIRM_RESTORE=YES após confirmar banco e backup." >&2
  exit 3
fi

export MYSQL_PWD="$DB_PASSWORD"
gzip -dc "$FILE" | mariadb --host="$DB_HOST" --port="${DB_PORT:-3306}" --user="$DB_USER" "$DB_NAME"
unset MYSQL_PWD
printf 'Restore concluído a partir de: %s\n' "$FILE"
