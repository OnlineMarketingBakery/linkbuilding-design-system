#!/usr/bin/env bash
# Provision a fresh WP site with LBDS blueprint (plugins, pages, options, menus).
# Usage: ./scripts/provision-site.sh /path/to/wordpress [/path/to/repo-root]
set -euo pipefail

WP_PATH="${1:-}"
REPO_ROOT="${2:-$(cd "$(dirname "$0")/.." && pwd)}"

if [[ -z "$WP_PATH" || ! -f "$WP_PATH/wp-config.php" ]]; then
  echo "Usage: $0 /path/to/wordpress [repo-root]" >&2
  exit 1
fi

BLUEPRINT="$REPO_ROOT/blueprint"
THEME_SRC="$REPO_ROOT/theme"
THEME_DEST="$WP_PATH/wp-content/themes/linkbuilding-design-system"

if ! command -v wp >/dev/null 2>&1; then
  echo "wp-cli not found in PATH" >&2
  exit 1
fi

WP=(wp --path="$WP_PATH" --allow-root)

echo "==> Sync theme to $THEME_DEST"
mkdir -p "$THEME_DEST"
rsync -a --delete \
  --exclude 'brand.json.example' \
  "$THEME_SRC/" "$THEME_DEST/"

echo "==> Activate theme"
"${WP[@]}" theme activate linkbuilding-design-system

echo "==> Install plugins from blueprint/plugins.txt"
while read -r slug; do
  [[ -z "$slug" || "$slug" =~ ^# ]] && continue
  if "${WP[@]}" plugin is-installed "$slug" 2>/dev/null; then
    "${WP[@]}" plugin activate "$slug" || true
  else
    "${WP[@]}" plugin install "$slug" --activate
  fi
done < "$BLUEPRINT/plugins.txt"

echo "==> Apply site options"
python3 - <<PY
import json, subprocess, sys
opts = json.load(open("$BLUEPRINT/site-options.json"))
wp = ["wp", "--path=$WP_PATH", "--allow-root"]
# Skip blogname/description if already customized (non-default)
skip_if_set = {"blogname", "blogdescription"}
for k, v in opts.items():
    if k in skip_if_set:
        cur = subprocess.check_output(wp + ["option", "get", k], text=True).strip()
        if cur and cur not in ("Site naam", "Korte tagline", "WordPress", "Just another WordPress site"):
            print(f"skip {k} (already set: {cur[:40]})")
            continue
    subprocess.check_call(wp + ["option", "update", k, str(v)])
    print(f"set {k}")
PY

echo "==> Import structural pages (skip existing by slug)"
# Import only missing blueprint pages
TMP_IMPORT=$(mktemp)
cp "$BLUEPRINT/pages.xml" "$TMP_IMPORT"
"${WP[@]}" plugin is-installed wordpress-importer >/dev/null 2>&1 || "${WP[@]}" plugin install wordpress-importer --activate
"${WP[@]}" plugin activate wordpress-importer >/dev/null 2>&1 || true

for slug in home artikelen contact privacy adverteren; do
  if "${WP[@]}" post list --post_type=page --name="$slug" --format=count | grep -vq '^0$'; then
    echo "page /$slug/ exists — skip recreate"
  else
    echo "will import missing pages via WXR (idempotent-ish)"
  fi
done

# Always try import; WordPress importer may duplicate — prefer create if missing
if ! "${WP[@]}" post list --post_type=page --name=home --field=ID 2>/dev/null | grep -q '[0-9]'; then
  "${WP[@]}" import "$TMP_IMPORT" --authors=create
else
  echo "Blueprint pages appear present; skipping WXR import"
fi
rm -f "$TMP_IMPORT"

echo "==> Front page / posts page"
HOME_ID=$("${WP[@]}" post list --post_type=page --name=home --field=ID | head -1)
BLOG_ID=$("${WP[@]}" post list --post_type=page --name=artikelen --field=ID | head -1)
if [[ -n "${HOME_ID:-}" ]]; then
  "${WP[@]}" option update show_on_front page
  "${WP[@]}" option update page_on_front "$HOME_ID"
fi
if [[ -n "${BLOG_ID:-}" ]]; then
  "${WP[@]}" option update page_for_posts "$BLOG_ID"
fi

echo "==> Menus"
LBDS_REPO="$REPO_ROOT" "${WP[@]}" eval-file "$REPO_ROOT/scripts/build-menus-wp.php"

# Flush
"${WP[@]}" rewrite structure '/%postname%/' --hard
"${WP[@]}" rewrite flush --hard
"${WP[@]}" cache flush 2>/dev/null || true

echo "==> Provision complete"
"${WP[@]}" theme list
"${WP[@]}" plugin list --status=active
echo "Home: $("${WP[@]}" option get home)"
