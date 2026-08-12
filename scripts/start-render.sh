#!/bin/sh
set -eu

MAX_ATTEMPTS=20
ATTEMPT=1

while true; do
  echo "[staging] Applying database migrations (attempt ${ATTEMPT}/${MAX_ATTEMPTS})..."

  if php scripts/migrate.php migrate; then
    break
  fi

  if [ "$ATTEMPT" -ge "$MAX_ATTEMPTS" ]; then
    echo "[staging] Database did not become ready or migrations failed after ${MAX_ATTEMPTS} attempts." >&2
    exit 1
  fi

  ATTEMPT=$((ATTEMPT + 1))
  sleep 3
done

echo "[staging] Database ready. Starting Apache."
exec apache2-foreground
