#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "$0")" && pwd)"
cd "$ROOT"

if [[ ! -f artisan ]]; then
  echo "Error: artisan not found. Run this script from the project root." >&2
  exit 1
fi

if [[ ! -f .env ]] && [[ -f .env.example ]]; then
  echo "Copying .env.example to .env (first run)."
  cp .env.example .env
  php artisan key:generate --no-interaction --ansi
fi

# Stale config cache can point migrate at the wrong DB after .env changes.
php artisan config:clear --no-interaction --ansi

# Apply every pending migration; safe to run on every start.
php artisan migrate --no-interaction --force --ansi

php artisan db:seed --no-interaction --force --ansi

LAN_IP="$(ipconfig getifaddr en0 2>/dev/null || ipconfig getifaddr en1 2>/dev/null || true)"
PORT="${APP_PORT:-8000}"
HOST="${APP_HOST:-0.0.0.0}"

for arg in "$@"; do
  case "$arg" in
    --host=*)
      HOST="${arg#--host=}"
      ;;
    --port=*)
      PORT="${arg#--port=}"
      ;;
  esac
done

echo "App will be reachable on your local network at: http://${LAN_IP:-localhost}:${PORT}"

HAS_HOST_ARG=0
HAS_PORT_ARG=0
for arg in "$@"; do
  [[ "$arg" == --host=* ]] && HAS_HOST_ARG=1
  [[ "$arg" == --port=* ]] && HAS_PORT_ARG=1
done

SERVE_ARGS=("$@")
[[ $HAS_HOST_ARG -eq 0 ]] && SERVE_ARGS+=("--host=${HOST}")
[[ $HAS_PORT_ARG -eq 0 ]] && SERVE_ARGS+=("--port=${PORT}")

exec php artisan serve "${SERVE_ARGS[@]}"
