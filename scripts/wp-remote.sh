#!/usr/bin/env bash
#
# Run a WP-CLI command against the live site over SSH.
#
#   scripts/wp-remote.sh post list --post_type=page --format=table
#   scripts/wp-remote.sh option get page_on_front
#   scripts/wp-remote.sh menu list --fields=name,count,locations --format=table
#
# Connection details come from `.env` (gitignored — see `.env.example`).
#
# This is for READING the site. Writes belong in
# `.github/workflows/deploy-content.yml`, which backs up the database first and
# leaves a log of what changed; a seeder run from a laptop leaves neither.
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

if [ ! -f "$ROOT/.env" ]; then
	echo "No .env found. Copy .env.example to .env and fill it in." >&2
	exit 1
fi

set -a
# shellcheck disable=SC1091
. "$ROOT/.env"
set +a

: "${HOSTINGER_HOST:?not set in .env}"
: "${HOSTINGER_USER:?not set in .env}"
: "${WP_PATH:?not set in .env}"

KEY="${HOSTINGER_SSH_KEY_PATH:-$HOME/.ssh/hostinger_pfp_deploy}"
KEY="${KEY/#\~/$HOME}"

if [ ! -f "$KEY" ]; then
	echo "No SSH key at $KEY — check HOSTINGER_SSH_KEY_PATH in .env." >&2
	exit 1
fi

if [ "$#" -eq 0 ]; then
	echo "Usage: scripts/wp-remote.sh <wp-cli args>" >&2
	exit 1
fi

exec ssh -i "$KEY" -p "${HOSTINGER_PORT:-65002}" \
	-o StrictHostKeyChecking=accept-new \
	"$HOSTINGER_USER@$HOSTINGER_HOST" \
	"cd '$WP_PATH' && wp $*"
