#!/bin/sh
set -eu

if [ -n "${DB_SSL_CA:-}" ]; then
  printf '%s\n' "$DB_SSL_CA" > /tmp/aiven-ca.pem
  chmod 600 /tmp/aiven-ca.pem
  export DB_SSL_CA_PATH=/tmp/aiven-ca.pem
fi

echo "[staging] Starting Apache immediately so connectivity checks can respond."
apache2-foreground &
APACHE_PID=$!

trap 'kill -TERM "$APACHE_PID" 2>/dev/null || true' INT TERM

(
  MAX_ATTEMPTS=20
  ATTEMPT=1

  while true; do
    echo "[staging] Applying database migrations (attempt ${ATTEMPT}/${MAX_ATTEMPTS})..."

    if php scripts/migrate.php migrate; then
      echo "[staging] Database migrations completed successfully."
      exit 0
    fi

    if [ "$ATTEMPT" -ge "$MAX_ATTEMPTS" ]; then
      echo "[staging] WARNING: database is not ready or migrations failed after ${MAX_ATTEMPTS} attempts." >&2
      echo "[staging] Apache will stay online for /ping.html and /health diagnostics." >&2
      exit 0
    fi

    ATTEMPT=$((ATTEMPT + 1))
    sleep 3
  done
) &

wait "$APACHE_PID"
