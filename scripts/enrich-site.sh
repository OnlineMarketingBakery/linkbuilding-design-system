#!/usr/bin/env bash
# Post-import enrichment: favicon, categories, featured images.
# Usage: ./scripts/enrich-site.sh /path/to/wordpress [old-domain]
set -euo pipefail

WP_PATH="${1:-}"
OLD_DOMAIN="${2:-}"

if [[ -z "$WP_PATH" || ! -f "$WP_PATH/wp-config.php" ]]; then
  echo "Usage: $0 /path/to/wordpress [old-domain]" >&2
  exit 1
fi

WP=(wp --path="$WP_PATH" --allow-root)
REPO_ROOT="$(cd "$(dirname "$0")/.." && pwd)"

echo "==> Generate brand favicon"
"${WP[@]}" eval-file "$REPO_ROOT/scripts/generate-favicon-wp.php"

echo "==> Enrich categories + featured images"
export LBDS_OLD_DOMAIN="$OLD_DOMAIN"
export LBDS_SIDeload_LIMIT="${LBDS_SIDeload_LIMIT:-150}"
"${WP[@]}" eval-file "$REPO_ROOT/scripts/enrich-posts-wp.php"

echo "==> Rebuild menus (categories may have changed counts)"
LBDS_REPO="$REPO_ROOT" "${WP[@]}" eval-file "$REPO_ROOT/scripts/build-menus-wp.php"

"${WP[@]}" cache flush 2>/dev/null || true
echo "==> Enrichment complete"
