#!/bin/sh
set -e

/app/scripts/ensure-storage.sh

exec docker-php-entrypoint --config /Caddyfile --adapter caddyfile 2>&1
