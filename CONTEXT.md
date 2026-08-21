# Linkbuilding rebuild — full context handoff

**Purpose:** Enough context for any engineer or AI with access to these servers to continue the project without prior chat history.

**Last updated:** 2026-08-21  
**Theme version (pilot):** `2.0.8`  
**GitHub tip:** `00d2fd4` on `main` — https://github.com/OnlineMarketingBakery/linkbuilding-design-system

---

## 1. Goal (why this exists)

Rebuild ~70 Dutch niche / linkbuilding WordPress sites that lived on old shared hosting (and partly on an infected Ploi box) onto a **clean** server, with:

- One shared **custom theme** (Masterblog design system) — not Elementor
- One shared **blueprint** (structural pages, menus, plugins, options)
- Per-site **content packs** (brand + posts + media indexes)
- Separate WP installs per domain (no Multisite)

**Brand-only differences between sites:** name, tagline, accent color, logo, categories/posts/partners links. Same layout and page set.

---

## 2. Architecture (three layers)

```text
GitHub: OnlineMarketingBakery/linkbuilding-design-system
  theme/       → design system (PHP templates, CSS, JS)
  blueprint/   → Home, Artikelen, Contact, Privacy, Adverteren, Partners + menus + plugins
  scripts/     → provision + import tooling

Per site (NOT in git — large):
  site-rebuild-context/<domain>/
    content.xml, brand.json / meta.json, categories, media-manifest, indexes
```

| Layer | Shared? | Changes per site |
|-------|---------|------------------|
| Theme | Yes | Almost never (git deploy) |
| Blueprint | Yes | Almost never |
| Content pack | No | Brand + posts + partners list + media |

**Do not:** clone fat WP sites, copy plugins/themes from infected hosts, use Elementor on new sites, or deploy new production on `omb-seo01`.

---

## 3. Servers and roles

| Host | IP | Hostname | Role |
|------|-----|----------|------|
| **SEO01** (Cursor often opens here) | `89.167.58.88` | `omb-seo01` | Infected / legacy Ploi box. **Read-only source** for packs and inventory. **Never** put new production rebuilds here. |
| **Production01** (clean) | `135.181.26.249` | `sites-prod-01` | **All new WP sites**, live theme deploy target, **canonical git repo for pushes**. |
| **StableServer** (old cPanel) | `s4409.fra1.stableserver.net` | user `rubin` | Original ~70 addon domains; source for pack extraction over SSH. |

**SSH user on both Ploi boxes:** `ploi`  
**Credentials file (mode 600):** `/home/ploi/.ploi-server-credentials` on SEO01 (also mirrored in Cursor rules). Contains sudo/MySQL notes for SEO01. Do not commit secrets to git.

**SSH between boxes:** SEO01 → Production01 works with key “SEO01”. SEO01 → StableServer (`rubin@…`) works for pack extraction.

**WP-CLI on Production01:** `/home/ploi/bin/wp` (add to `PATH`).

---

## 4. Important paths

### Production01 (`sites-prod-01`)

| What | Path |
|------|------|
| Design system git repo | `/home/ploi/linkbuilding-design-system/` |
| Pilot WP root | `/home/ploi/verbouwing.onlinemarketingbakery.nl/public/` |
| Active theme | `…/wp-content/themes/linkbuilding-design-system/` |
| Brand override | `…/wp-content/uploads/brand.json` |
| Content packs | `/home/ploi/site-rebuild-context/<domain>/` |
| Theme rollback backup | `/home/ploi/theme-backups/linkbuilding-design-system-2.0.6/` |
| Rollback script | `/home/ploi/theme-backups/rollback-verbouwing-2.0.6.sh` |
| Token source for GitHub push | `/home/ploi/static-demo.onlinemarketingbakery.nl/.git/config` (HTTPS remote embeds org credential — **never print/commit**) |

### SEO01 (`omb-seo01`)

| What | Path |
|------|------|
| Working copy of monorepo (may drift) | `/home/ploi/linkbuilding-design-system/` |
| Packs / inventory | `/home/ploi/site-rebuild-context/` |
| Plans (Cursor) | `/home/ploi/.cursor/plans/` |
| Agent transcripts | `/home/ploi/.cursor/projects/home-ploi/agent-transcripts/` |

**Note:** Prefer editing and **git push from Production01**. SEO01 was used as a Cursor workspace and once had an empty theme working tree; Production01 + GitHub are sources of truth for code.

---

## 5. Pilot site (approved design)

| Item | Value |
|------|--------|
| Staging URL | https://verbouwing.onlinemarketingbakery.nl |
| Production domain (not cut over yet) | `verbouwingaanhuis.nl` (still on old host) |
| Theme | `linkbuilding-design-system` **2.0.8** |
| Design | Masterblog-inspired: Newsreader + Public Sans, paper background, terracotta `--accent` (pilot `#B5502E`) |
| Homepage | Light sticky chrome + cat-strip → **dark cinematic featured hero** → light article sections |
| Admin | `ombadmin` (password file on Production01: `/home/ploi/.verbouwing-admin-pass` — rotate if shared) |

### Canonical blog pages (blueprint)

| Slug | Role |
|------|------|
| `home` | Front page (theme `front-page.php`) |
| `artikelen` | Posts page |
| `contact` | CF7 form (`title="Contact"`) |
| `adverteren` | Advertise intro + same CF7 |
| `partners` | Partner link list (pack content overrides stub) |
| `privacy` | Legal stub |

**Menus:** Primary = Home / Artikelen / Contact / Adverteren. Topbar util = Adverteren · Partners · Contact. Footer Over = Contact / Partners / Privacy / Adverteren.

**Plugins (minimal):** Contact Form 7, Rank Math (`seo-by-rank-math`). No Elementor.

---

## 6. Design / theme notes (so you don’t “redesign”)

- **Source of visual language:** Masterblog static reference was used; live pilot is now the approved look (v2.0.8).
- **Layout class:** Use `.mb-page` for max-width containers — **never** `.page` (WordPress adds `page` to `<body>` on static pages and would box the whole homepage).
- **Brand:** `uploads/brand.json` or theme brand helpers → CSS `--accent`, blogname, tagline.
- **Favicon:** `theme/assets/favicon.svg` (skipped if WP Customizer Site Icon is set).
- **CSS file is large/duplicated** from HTML merges; polish blocks are appended at the **end** of `theme/assets/css/main.css` (sticky, CTA hover fix, atmosphere, cinema hero, gap tighten). Prefer appending overrides over editing early duplicate rules.
- **Rollback:** if a design change is disliked, run `/home/ploi/theme-backups/rollback-verbouwing-2.0.6.sh` on Production01 (restores pre-cinema 2.0.6). Newer backups can be added the same way before risky deploys.

---

## 7. Factory runbook (site N)

1. **Ploi on Production01** — create WordPress site + SSL (staging subdomain first).
2. **Deploy theme** from GitHub `linkbuilding-design-system` →  
   `public/wp-content/themes/linkbuilding-design-system`  
   Helper: `scripts/ploi-deploy-theme.sh` with `SITE_PATH` / `REPO`.
3. **Provision blueprint:**
   ```bash
   export PATH="/home/ploi/bin:$PATH"
   /home/ploi/linkbuilding-design-system/scripts/provision-site.sh /path/to/wordpress/public /home/ploi/linkbuilding-design-system
   ```
   Creates missing pages, CF7 form titled `Contact`, menus, permalinks.
4. **Import content pack:**
   ```bash
   …/scripts/import-content-pack.sh /path/to/wordpress/public /home/ploi/site-rebuild-context/<domain> <old-domain>
   ```
   Applies brand, imports WXR, sanitizes Elementor markup, normalizes CF7 shortcodes, prefer pack content for partners/adverteren/contact.
5. **QA:** home hero, artikelen sort/pagination, contact/adverteren forms, partners list, categories, mobile, Rank Math basics.
6. **DNS cutover** only after staging sign-off (point domain to Production01; keep old host briefly).

### Branding a new site (minimal)

Create/update pack `brand.json` (or meta) with at least:

- `blogname`, `tagline` / `blogdescription`
- `accent` / `color_accent` (hex)

Copy to `wp-content/uploads/brand.json` (import script does this when present).

---

## 8. GitHub workflow (critical)

**Canonical push host = Production01**, not SEO01.

Previous successful pushes used the org HTTPS credential already present in:

`/home/ploi/static-demo.onlinemarketingbakery.nl/.git/config`

Pattern (do **not** echo the token):

```bash
ssh ploi@135.181.26.249
cd /home/ploi/linkbuilding-design-system
# set origin to https://USER:TOKEN@github.com/OnlineMarketingBakery/linkbuilding-design-system.git via python reading static-demo config
git add -A
git -c user.email="dev@onlinemarketingbakery.nl" -c user.name="OMB Deploy" commit -m "…"
git push origin main
git remote set-url origin https://github.com/OnlineMarketingBakery/linkbuilding-design-system.git   # scrub token
```

**Cursor GitHub App** access does **not** grant `git push` from these Ploi shells by itself.

Commit author used historically: **OMB Deploy** `<dev@onlinemarketingbakery.nl>`.

---

## 9. What is done vs not done

### Done

- Clean server Production01 ready; pilot WP installed
- Theme Masterblog rebuild + pilot staging signed off visually (user: design liked)
- Canonical pages including Partners + CF7 on Adverteren/Contact
- Sticky header, CTA hover fix, cinematic hero, section spacing, favicon
- Blueprint + provision/import scripts in repo
- Verbouwingaanhuis content pack imported (~265 posts)
- Page slug inventory sample → `site-rebuild-context/_inventory/pages-frequency.json` (on SEO01)
- GitHub `main` at `00d2fd4` (v2.0.8)

### Not done

- DNS cutover for `verbouwingaanhuis.nl`
- Batch rebuild of remaining ~70 domains
- Content pack extraction for domains beyond verbouwingaanhuis
- Directory/Business niche templates (woonwinkels, inschrijven-*, etc.) — out of Masterblog blog blueprint
- Full media sideload completeness (many featured images fixed manually on pilot; packs still need systematic media)
- Deep Elementor HTML cleanup on all post bodies
- Optional: dedicated `blueprint.onlinemarketingbakery.nl` live generator site

---

## 10. Plans / history (Cursor)

| Plan file | Topic |
|-----------|--------|
| `/home/ploi/.cursor/plans/linkbuilding_factory_system_d2d812f1.plan.md` | Factory architecture |
| `/home/ploi/.cursor/plans/masterblog_theme_rebuild_8bf4ad87.plan.md` | Masterblog theme port |
| `/home/ploi/.cursor/plans/canonical_blog_pages_3d940065.plan.md` | Partners + Adverteren page set |
| `/home/ploi/.cursor/plans/contrast_hero_pop_16ff0c63.plan.md` | Cinematic hero contrast |

Long chat transcript:  
`/home/ploi/.cursor/projects/home-ploi/agent-transcripts/c631e8d1-e42f-455a-b675-95079a77f73a/c631e8d1-e42f-455a-b675-95079a77f73a.jsonl`

---

## 11. Suggested next actions

1. Pick **domain #2**; extract pack from StableServer/SEO01 into `site-rebuild-context/<domain>/` on Production01.
2. Provision second staging site on Production01 with same theme + blueprint + import.
3. Prove the loop is boring/repeatable, then batch.
4. Only then DNS cutovers.
5. Keep theme changes in git via Production01 push pattern above.

---

## 12. Hard rules (do not violate)

1. New production sites **only** on `sites-prod-01`.
2. No Elementor / no copying old `wp-content` from infected hosts.
3. Theme layout width class = `.mb-page`, not `.page`.
4. Content packs stay **out** of the theme git repo.
5. Scrub GitHub tokens from `git remote` after push.
6. Prefer appending CSS polish at end of `main.css` until a proper CSS cleanup pass exists.
7. Directory niches get a **later** template — don’t stretch the blog blueprint for them.

---

## 13. Quick health checks

```bash
# Staging theme version
ssh ploi@135.181.26.249 'grep LBDS_VERSION /home/ploi/verbouwing.onlinemarketingbakery.nl/public/wp-content/themes/linkbuilding-design-system/functions.php'

# GitHub tip
curl -sL https://api.github.com/repos/OnlineMarketingBakery/linkbuilding-design-system/commits/main | head

# Pilot homepage
curl -sI https://verbouwing.onlinemarketingbakery.nl/ | head
```

This document lives in the monorepo as `CONTEXT.md` and should stay updated when architecture or server roles change.
