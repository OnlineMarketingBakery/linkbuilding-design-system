#!/usr/bin/env bash
# Extract a content-only pack from an existing WordPress site (SEO01 source).
# Usage: ./scripts/extract-content-pack.sh /path/to/wordpress /path/to/pack-output [domain]
set -euo pipefail

WP_PATH="${1:-}"
OUT="${2:-}"
DOMAIN="${3:-}"

if [[ -z "$WP_PATH" || -z "$OUT" || ! -f "$WP_PATH/wp-config.php" ]]; then
  echo "Usage: $0 /path/to/wordpress /path/to/pack-output [domain]" >&2
  exit 1
fi

if ! command -v wp >/dev/null 2>&1; then
  echo "wp-cli not found in PATH" >&2
  exit 1
fi

WP=(wp --skip-plugins --path="$WP_PATH" --allow-root)
mkdir -p "$OUT"

if [[ -z "$DOMAIN" ]]; then
  DOMAIN=$("${WP[@]}" option get home 2>/dev/null | sed -E 's#^https?://([^/]+)/?$#\1#' || true)
fi

echo "==> Export WXR to $OUT/content.xml"
"${WP[@]}" export --dir="$OUT" --filename_format=content.xml --skip_comments --max_file_size=512 2>/dev/null || \
  "${WP[@]}" export --dir="$OUT" --filename_format=content.xml --skip_comments
# wp export may create content.xml.000.xml etc — normalize to content.xml
if [[ ! -f "$OUT/content.xml" ]]; then
  FIRST=$(ls "$OUT"/content.xml*.xml 2>/dev/null | head -1 || true)
  if [[ -n "$FIRST" ]]; then
    mv "$FIRST" "$OUT/content.xml"
    rm -f "$OUT"/content.xml*.xml 2>/dev/null || true
  fi
fi

echo "==> Write meta.json"
python3 - <<PY
import json, subprocess, datetime
wp = ["wp", "--skip-plugins", "--path=$WP_PATH", "--allow-root"]
def opt(k):
    try:
        return subprocess.check_output(wp + ["option", "get", k], text=True).strip()
    except subprocess.CalledProcessError:
        return ""
posts = subprocess.check_output(wp + ["post", "list", "--post_type=post", "--post_status=publish", "--format=count"], text=True).strip()
pages = subprocess.check_output(wp + ["post", "list", "--post_type=page", "--post_status=publish", "--format=count"], text=True).strip()
media = subprocess.check_output(wp + ["post", "list", "--post_type=attachment", "--format=count"], text=True).strip()
meta = {
    "domain": "$DOMAIN",
    "extracted_at": datetime.datetime.utcnow().strftime("%Y-%m-%dT%H:%M:%SZ"),
    "siteurl": opt("siteurl"),
    "home": opt("home"),
    "blogname": opt("blogname"),
    "blogdescription": opt("blogdescription"),
    "template": opt("template"),
    "stylesheet": opt("stylesheet"),
    "permalink": opt("permalink_structure"),
    "posts_published": int(posts or 0),
    "pages_published": int(pages or 0),
    "media_count": int(media or 0),
}
json.dump(meta, open("$OUT/meta.json", "w"), indent=2, ensure_ascii=False)
print(json.dumps(meta, indent=2))
PY

echo "==> Write indexes"
"${WP[@]}" post list --post_type=post --post_status=publish --fields=ID,post_title,post_name,post_date --format=json > "$OUT/posts-index.json"
"${WP[@]}" post list --post_type=page --post_status=publish --fields=ID,post_title,post_name --format=json > "$OUT/pages-index.json"
"${WP[@]}" term list category --format=json > "$OUT/categories.json"
"${WP[@]}" post list --post_type=attachment --fields=ID,guid,post_mime_type,post_title --format=json > "$OUT/media-manifest.json"

echo "==> Write brand.json stub (edit accent/about before import)"
python3 - <<PY
import json
m = json.load(open("$OUT/meta.json"))
name = (m.get("blogname") or "").strip()
desc = (m.get("blogdescription") or "").strip()
brand = {
    "blogname": name.replace("\n", " ").strip(),
    "tagline": desc.replace("\n", " ").strip(),
    "about": f"{name.strip()} deelt artikelen over persoonlijke groei, gezondheid en mindset.",
    "accent": "#2671D6",
}
json.dump(brand, open("$OUT/brand.json", "w"), indent=2, ensure_ascii=False)
PY

cat > "$OUT/BRIEF.md" <<MD
# $DOMAIN — rebuild brief

## Source
- Host: SEO01 (\`omb-seo01\` / \`89.167.58.88\`)
- Path: \`$WP_PATH\`
- Extracted: content only (WXR + JSON indexes). No themes/plugins copied.

## Target
- Clean server: \`sites-prod-01\` (\`135.181.26.249\`)
- Staging: \`betterandstronger.onlinemarketingbakery.nl\`
- Stack: custom shared theme (Masterblog), GitHub deploy

## Content snapshot
See \`meta.json\`, \`posts-index.json\`, \`pages-index.json\`, \`categories.json\`, \`media-manifest.json\`, \`content.xml\`.
MD

echo "==> Pack written to $OUT"
ls -la "$OUT"
