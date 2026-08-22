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

MISSING=0
for slug in home artikelen contact privacy adverteren partners; do
  if "${WP[@]}" post list --post_type=page --name="$slug" --format=count | grep -vq '^0$'; then
    echo "page /$slug/ exists — skip recreate"
  else
    echo "page /$slug/ missing"
    MISSING=1
  fi
done

# Import blueprint WXR when any structural page is missing (may create only new ones via selective create below)
if [[ "$MISSING" -eq 1 ]]; then
  if ! "${WP[@]}" post list --post_type=page --name=home --field=ID 2>/dev/null | grep -q '[0-9]'; then
    "${WP[@]}" import "$TMP_IMPORT" --authors=create
  else
    # Create any missing pages individually from blueprint stubs via wp post create
    python3 - <<PY
import re, subprocess
from pathlib import Path
xml = Path("$BLUEPRINT/pages.xml").read_text()
wp = ["wp", "--path=$WP_PATH", "--allow-root"]
for m in re.finditer(r"<item>(.*?)</item>", xml, re.S):
    it = m.group(1)
    if "page]]></wp:post_type>" not in it and ">page</wp:post_type>" not in it:
        continue
    def cd(tag):
        mm = re.search(rf"<{tag}><!\[CDATA\[(.*?)\]\]></{tag}>", it, re.S)
        return mm.group(1) if mm else ""
    slug = cd("wp:post_name")
    title = cd("title")
    body = cd("content:encoded")
    if not slug:
        continue
    exists = subprocess.check_output(wp + ["post", "list", "--post_type=page", f"--name={slug}", "--format=count"], text=True).strip()
    if exists != "0":
        continue
    subprocess.check_call(
        wp
        + [
            "post",
            "create",
            "--post_type=page",
            f"--post_title={title}",
            f"--post_name={slug}",
            "--post_status=publish",
            f"--post_content={body}",
        ]
    )
    print(f"created /{slug}/")
PY
  fi
else
  echo "All blueprint pages present; skipping WXR import"
fi
rm -f "$TMP_IMPORT"

echo "==> Contact Form 7 default form"
"${WP[@]}" eval-file "$REPO_ROOT/scripts/provision-cf7-wp.php" || true

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

echo "==> Purge dummy posts (fresh installs)"
"${WP[@]}" eval-file "$REPO_ROOT/scripts/purge-junk-posts-wp.php" || true

# Flush
"${WP[@]}" rewrite structure '/%postname%/' --hard
"${WP[@]}" rewrite flush --hard
"${WP[@]}" cache flush 2>/dev/null || true

echo "==> Provision complete"
"${WP[@]}" theme list
"${WP[@]}" plugin list --status=active
echo "Home: $("${WP[@]}" option get home)"
