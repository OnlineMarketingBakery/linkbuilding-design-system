# Linkbuilding Design System

Shared **custom WordPress theme** + **site blueprint** + **provision scripts** for Online Marketing Bakery linkbuilding sites.

Repo: https://github.com/OnlineMarketingBakery/linkbuilding-design-system

## Architecture

| Layer | Purpose |
|-------|---------|
| `theme/` | Design system (layouts, CSS tokens, templates) |
| `blueprint/` | Structural pages, menus, plugins, options — **no** blog posts |
| `scripts/` | `provision-site.sh`, `import-content-pack.sh` |
| Content packs | Per-domain data **outside** this repo (`site-rebuild-context/<domain>/`) |

Do **not** clone fat Elementor sites. Do **not** deploy new production sites on infected servers.

## Ploi Git deploy

1. Create a WordPress site in Ploi (core stays on the server).
2. Connect this GitHub repository.
3. Set deploy path / directory so the **theme folder** lands at:

   `wp-content/themes/linkbuilding-design-system`

   Typical approaches:
   - Deploy only subdirectory `theme/` as that theme name, **or**
   - Keep a deploy script that rsyncs `theme/` → `public/wp-content/themes/linkbuilding-design-system`

4. Deploy hook (recommended) after pull:

```bash
rsync -a --delete theme/ public/wp-content/themes/linkbuilding-design-system/
wp theme activate linkbuilding-design-system --path=public
```

## Factory: create site N

1. **Ploi** — New WordPress site + SSL on `sites-prod-01`.
2. **Deploy theme** from this repo (see above).
3. **Provision blueprint**:

```bash
cd /home/ploi/linkbuilding-design-system   # or clone path
chmod +x scripts/*.sh
./scripts/provision-site.sh /home/ploi/YOUR-SITE/public
```

4. **Content pack** — place pack at e.g. `/home/ploi/site-rebuild-context/example.nl/` containing:
   - `content.xml` (WXR)
   - `meta.json` / `brand.json`
   - optional `media-manifest.json`
5. **Import**:

```bash
./scripts/import-content-pack.sh \
  /home/ploi/YOUR-SITE/public \
  /home/ploi/site-rebuild-context/example.nl \
  example.nl
```

6. **QA** — home, 5 posts, contact, mobile, Rank Math basics.
7. **DNS** — point domain to `sites-prod-01` when ready; keep old host briefly for rollback.

## Branding per site

Copy `theme/brand.json.example` → `wp-content/uploads/brand.json` (or pack `brand.json`).

Keys: `color_primary`, `color_accent`, `color_ink`, `color_paper`, `color_muted`, `font_display`, `font_body`, `tagline`, `blogname`.

Also editable under **Appearance → Customize → Linkbuilding Brand**.

## Pilot

- Staging: `https://verbouwing.onlinemarketingbakery.nl`
- Pack: `site-rebuild-context/verbouwingaanhuis.nl`
- Target production domain later: `verbouwingaanhuis.nl` (DNS cutover only after QA)

## Requirements

- PHP 8.1+ (pilot server: 8.5)
- WP-CLI on the server
- Plugins from `blueprint/plugins.txt` (Rank Math, Contact Form 7)

## Pilot status (verbouwing)

Staging: https://verbouwing.onlinemarketingbakery.nl

Completed on `sites-prod-01`:
- Theme `linkbuilding-design-system` active
- Blueprint pages + menus provisioned
- Content pack imported (~266 posts)
- Rank Math + Contact Form 7 active
- Locale `nl_NL`

Admin user created as `ombadmin` (password file on server: `/home/ploi/.verbouwing-admin-pass`). Change this password after first login.

Ploi deploy helper:

```bash
SITE_PATH=/home/ploi/verbouwing.onlinemarketingbakery.nl/public \
REPO=/home/ploi/linkbuilding-design-system \
  /home/ploi/linkbuilding-design-system/scripts/ploi-deploy-theme.sh
```

Or add that as the site deploy script after `git pull` of this repo into `/home/ploi/linkbuilding-design-system`.
