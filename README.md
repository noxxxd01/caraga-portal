# DICT Caraga PMT Training Portal — Componentized

This is the original single-file `dict_caraga_training_portal.php` split into a
normal multi-file PHP project. No behavior was changed — only file boundaries.
All 31 JS functions and all 8 API actions from the original file are preserved
exactly, just relocated.

## Folder structure

```
dict-caraga-portal/
├── index.php                     # Entry point — wires everything together
├── config/
│   ├── database.php               # PDO connection ($db)
│   └── install.php                # CREATE TABLE + seed data (idempotent)
├── api/                            # One file per AJAX action (was the switch/case)
│   ├── _bootstrap.php              # shared: JSON headers + require database.php
│   ├── dashboard_data.php          # GET  action=get_dashboard_data
│   ├── training_save.php           # POST action=save_training
│   ├── training_delete.php         # POST action=delete_training
│   ├── allocation_update.php       # POST action=update_pmt_allocation
│   ├── downloads_get.php           # GET  action=get_pmt_downloads
│   ├── downloads_save.php          # POST action=save_download
│   ├── downloads_delete.php        # POST action=delete_download
│   └── reset_database.php          # POST action=reset_database
├── partials/                       # HTML chunks, included by index.php
│   ├── head.php                    # <head>, CDN <script>/<link> tags, Tailwind config
│   ├── header.php                  # Top navy branded bar + tab nav buttons
│   ├── tab_dashboard.php           # GIS map + KPI cards + provincial ledger
│   ├── tab_tracker.php             # Training tracker table + toolbar
│   ├── tab_financial.php           # Financial ledger + bar chart column
│   ├── tab_api_explorer.php        # "Simulated REST API" viewer tab
│   ├── tab_downloads.php           # Central Office PMT downloads registry
│   ├── modal_download.php          # Add/Edit PMT download modal
│   └── modal_training.php          # Add/Edit training modal (the big CRUD form)
└── assets/
    ├── css/
    │   └── style.css                # Extracted from the old <style> block
    └── js/                          # Old ~1,200-line <script> split by responsibility
        ├── state.js                 # Coordinates, globals, status colors (load 1st)
        ├── map.js                   # Leaflet: init, markers, heatmap
        ├── charts.js                # Chart.js: doughnut + bar
        ├── dashboard.js             # synchronizeDashboardState, KPI/ledger rendering
        ├── tracker.js               # Table rendering, CRUD modal, CSV export
        ├── downloads.js             # Downloads registry CRUD, baseline reset
        ├── ui.js                    # Tab switching, filters, formatCurrency
        └── main.js                  # window.onload boot sequence (load last)
```

## What changed vs. the original file

- The single `switch ($action)` block is now 8 separate `api/*.php` files.
  Every `fetch("<?php echo $_SERVER['PHP_SELF']; ?>?action=xxx")` call in the
  JS was rewritten to call the matching file directly, e.g. `fetch("api/training_save.php")`.
- The inline `<style>` block is now `assets/css/style.css`, linked from `partials/head.php`.
- The ~1,200-line inline `<script>` is now 8 files under `assets/js/`, loaded
  in dependency order from the bottom of `index.php`:
  `state.js → map.js → charts.js → dashboard.js → tracker.js → downloads.js → ui.js → main.js`.
- Nothing in the SQL, HTML markup, CSS rules, or JS logic itself was rewritten —
  every extracted JS function was verified with `node --check` and every HTML
  partial was checked for balanced `<div>` tags.

## Setup (XAMPP), same as before

1. Copy the whole `dict-caraga-portal/` folder into `htdocs/`.
2. Start Apache + MySQL in the XAMPP Control Panel.
3. Visit `http://localhost/dict-caraga-portal/index.php`.
4. `config/install.php` auto-creates the database/tables and seeds baseline
   data on first run, exactly like the original file did.

## Suggested next steps (optional, not done here)

- Wrap the raw `fetch()` calls scattered in `tracker.js`/`downloads.js`/`dashboard.js`
  into a small `assets/js/api.js` client (`Api.saveTraining(data)`, etc.) so
  endpoint URLs live in exactly one place.
- Add `.htaccess` or move `config/` and `api/_bootstrap.php` outside the web
  root if this ever goes past a local XAMPP demo, since DB credentials are
  currently plain PHP variables in a web-servable folder.
- Move the seed data in `config/install.php` into a `.sql` file and load it
  with `SOURCE`, so schema/seed changes don't require touching PHP.
