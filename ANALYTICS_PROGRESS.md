# Analytics — Final Decision (2026-02-21)

## Strategy by hosting type

### VPS sites — Python system
Use `analytics.py` + `run-analytics.sh` + `stats.html`.
- Cron unpacks DirectAdmin daily log tarballs and feeds them to `analytics.py`
- Produces `report-YYYY-MM-DD.json` consumed by `stats.html`
- Full coverage of all requests (reads real Apache logs)
- Canonical files: `~/work/projects/shared-tools/access-logger/`

### DirectAdmin shared hosting (faucetlist.org, satoshihost.top) — AWStats
Use AWStats directly via the DirectAdmin interface. No custom analytics needed.
- AWStats already reads real Apache logs — full coverage including static files
- Enabled in DirectAdmin for faucetlist.org (2026-02-21)
- No maintenance burden, no PHP logger, no cron jobs to manage
- The custom `logger.php` / `analytics.php` / `refresh.php` approach was abandoned:
  it only captured `GET /` (PHP requests only), missing all static page views

---

## analytics.py improvements (2026-02-21)

Brought in line with `analytics.php` (the PHP equivalent):
- **Expanded bot list** — added `facebookexternalhit`, `semrush`, `ahrefsbot`,
  `mj12bot`, `dotbot`, `petalbot`; switched from regex to plain `in` check (faster)
- **Static asset filtering** — now skips `.css`, `.js`, `.png`, `.ico` etc.;
  only counts page-like paths (no extension, `.php`, `.html`, `.htm`)

---

## Shared tools location

```
~/work/projects/shared-tools/access-logger/
  analytics.php   — PHP parser (shared hosting, token-gated URL trigger)
  analytics.py    — Python parser (VPS, cron + log tarball workflow)
  logger.php      — PHP request logger (not used on DirectAdmin sites)
  refresh.php     — token-free refresh wrapper (not used on DirectAdmin sites)
  stats.html      — dashboard frontend (used on VPS sites)
  README.md
```
