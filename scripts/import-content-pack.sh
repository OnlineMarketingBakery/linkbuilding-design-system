#!/usr/bin/env bash
# Import a content pack into a provisioned LBDS WordPress site.
# Usage: ./scripts/import-content-pack.sh /path/to/wordpress /path/to/pack [old-domain]
set -euo pipefail

WP_PATH="${1:-}"
PACK="${2:-}"
OLD_DOMAIN="${3:-}"

if [[ -z "$WP_PATH" || -z "$PACK" || ! -f "$WP_PATH/wp-config.php" ]]; then
  echo "Usage: $0 /path/to/wordpress /path/to/pack [old-domain]" >&2
  exit 1
fi

WP=(wp --path="$WP_PATH" --allow-root)
REPO_ROOT="$(cd "$(dirname "$0")/.." && pwd)"

echo "==> Apply brand.json if present"
if [[ -f "$PACK/brand.json" ]]; then
  mkdir -p "$WP_PATH/wp-content/uploads"
  cp "$PACK/brand.json" "$WP_PATH/wp-content/uploads/brand.json"
  if command -v jq >/dev/null 2>&1; then
    NAME=$(jq -r '.blogname // empty' "$PACK/brand.json")
    DESC=$(jq -r '.tagline // .blogdescription // empty' "$PACK/brand.json")
    [[ -n "$NAME" ]] && "${WP[@]}" option update blogname "$NAME"
    [[ -n "$DESC" ]] && "${WP[@]}" option update blogdescription "$DESC"
  else
    python3 - <<PY
import json, subprocess
b=json.load(open("$PACK/brand.json"))
wp=["wp","--path=$WP_PATH","--allow-root"]
if b.get("blogname"):
    subprocess.check_call(wp+["option","update","blogname",b["blogname"]])
tag=b.get("tagline") or b.get("blogdescription")
if tag:
    subprocess.check_call(wp+["option","update","blogdescription",tag])
PY
  fi
elif [[ -f "$PACK/meta.json" ]]; then
  python3 - <<PY
import json, subprocess
m=json.load(open("$PACK/meta.json"))
wp=["wp","--path=$WP_PATH","--allow-root"]
name=(m.get("blogname") or "").strip()
if name:
    subprocess.check_call(wp+["option","update","blogname",name])
PY
fi

echo "==> Ensure importer plugin"
"${WP[@]}" plugin is-installed wordpress-importer >/dev/null 2>&1 || "${WP[@]}" plugin install wordpress-importer --activate
"${WP[@]}" plugin activate wordpress-importer >/dev/null 2>&1 || true

CONTENT_XML=""
if [[ -f "$PACK/content.xml" ]]; then
  CONTENT_XML="$PACK/content.xml"
elif ls "$PACK"/*.xml >/dev/null 2>&1; then
  CONTENT_XML=$(ls "$PACK"/*.xml | head -1)
fi

if [[ -z "$CONTENT_XML" ]]; then
  echo "No WXR XML found in pack" >&2
  exit 1
fi

echo "==> Import WXR: $CONTENT_XML"
"${WP[@]}" import "$CONTENT_XML" --authors=create

echo "==> Sanitize Elementor / builder markup in post_content"
"${WP[@]}" eval-file "$REPO_ROOT/scripts/sanitize-content-wp.php"

echo "==> Ensure CF7 Contact form + normalize pack utility pages"
"${WP[@]}" eval-file "$REPO_ROOT/scripts/provision-cf7-wp.php" || true
"${WP[@]}" eval-file "$REPO_ROOT/scripts/apply-pack-pages-wp.php" || true

if [[ -n "$OLD_DOMAIN" ]]; then
  NEW_HOME=$("${WP[@]}" option get home)
  echo "==> Search-replace $OLD_DOMAIN -> $NEW_HOME (content URLs)"
  # Replace https and http variants of old domain in content only where safe
  "${WP[@]}" search-replace "https://$OLD_DOMAIN" "$NEW_HOME" --precise --all-tables --skip-columns=guid || true
  "${WP[@]}" search-replace "http://$OLD_DOMAIN" "$NEW_HOME" --precise --all-tables --skip-columns=guid || true
fi

echo "==> Optional media sideload from media-manifest.json (featured-ish URLs)"
if [[ -f "$PACK/media-manifest.json" ]]; then
  "${WP[@]}" eval-file "$REPO_ROOT/scripts/sideload-media-wp.php" || true
fi

echo "==> Rebuild menus (includes Partners in footer)"
LBDS_REPO="$REPO_ROOT" "${WP[@]}" eval-file "$REPO_ROOT/scripts/build-menus-wp.php" || true

"${WP[@]}" rewrite flush --hard
"${WP[@]}" cache flush 2>/dev/null || true

echo "==> Import summary"
echo "Posts: $("${WP[@]}" post list --post_type=post --post_status=publish --format=count)"
echo "Pages: $("${WP[@]}" post list --post_type=page --post_status=publish --format=count)"
echo "Done."
