#!/usr/bin/env bash
# Deploy LBDS theme into a Ploi WordPress site after git pull.
# Usage:
#   SITE_PATH=/home/ploi/example.nl/public \
#   REPO=/home/ploi/linkbuilding-design-system \
#   ./scripts/ploi-deploy-theme.sh
set -euo pipefail

SITE_PATH="${SITE_PATH:-/home/ploi/verbouwing.onlinemarketingbakery.nl/public}"
REPO="${REPO:-/home/ploi/linkbuilding-design-system}"

if [[ ! -f "$SITE_PATH/wp-config.php" ]]; then
  echo "Missing WordPress at SITE_PATH=$SITE_PATH" >&2
  exit 1
fi
if [[ ! -d "$REPO/theme" ]]; then
  echo "Missing theme at REPO=$REPO/theme" >&2
  exit 1
fi

mkdir -p "$SITE_PATH/wp-content/themes/linkbuilding-design-system"
rsync -a --delete "$REPO/theme/" "$SITE_PATH/wp-content/themes/linkbuilding-design-system/"

if command -v wp >/dev/null 2>&1; then
  wp --path="$SITE_PATH" theme activate linkbuilding-design-system
elif [[ -x "$HOME/bin/wp" ]]; then
  "$HOME/bin/wp" --path="$SITE_PATH" theme activate linkbuilding-design-system
fi

echo "Theme deployed to $SITE_PATH/wp-content/themes/linkbuilding-design-system"
