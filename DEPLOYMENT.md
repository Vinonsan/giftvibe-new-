# GiftVibe LK — cPanel Deployment Guide

Copy-paste ready steps for **giftvibelk.lk** on cPanel hosting.

No Composer, Node.js, or build step required.

---

## Server requirements

- PHP **8.2+** with PDO MySQL, mbstring, fileinfo, GD
- MySQL 8+ or MariaDB 10.4+
- Apache `mod_rewrite` + `mod_headers`
- HTTPS enabled

---

## Step 1 — Upload files

1. Open **cPanel → File Manager**
2. Go to your account home (e.g. `/home/riversid/`)
3. Upload **`giftvibelk-deployment.zip`**
4. **Extract** the zip
5. You should see folders: `app`, `public`, `resources`, `routes`, `storage`, plus `config.php`, `schema.sql`, `.htaccess`

> Keep the project **outside** `public_html` if possible, and point the domain to `public/` (recommended).  
> If you must use `public_html`, upload so that `public_html/index.php` is the app front controller (see Step 2 option B).

---

## Step 2 — Point domain to `public/`

### Option A — Recommended (subfolder install)

1. cPanel → **Domains** → **Domains** or **Addon Domains**
2. Set document root for `giftvibelk.lk` to:
   ```
   /home/riversid/giftvibe-new-/public
   ```
   (adjust path to where you extracted the zip)

### Option B — Everything inside `public_html`

1. Copy **contents of `public/`** into `public_html/`
2. Edit `public_html/index.php` line 20 — change:
   ```php
   define('BASE_PATH', dirname(__DIR__));
   ```
   to:
   ```php
   define('BASE_PATH', dirname(__DIR__) . '/giftvibe-new-');
   ```
   (path to folder containing `app/`, `config.php`, etc.)

---

## Step 3 — Database (already configured)

Production database credentials are supplied by the deployment-only `.env` file included in the archive. Keep this file private and never place it inside the public document root.

| Setting | Value |
|---------|-------|
| Host | `localhost` |
| Database | `riversid_giftvibe_lk_db` |
| User | `riversid_giftvibe_lk_db` |
| Password | Set in the packaged `.env` file |

If cPanel shows different names, edit `config.php` only — no code changes needed.

Supported environment variables are `APP_URL`, `DB_HOST`, `DB_NAME`, `DB_USER`, and `DB_PASSWORD`; hosting-panel environment values override `.env` values.

### Import schema

1. cPanel → **phpMyAdmin**
2. Select database **`riversid_giftvibe_lk_db`**
3. **Import** → choose **`schema.sql`** from project root
4. Click **Go** (wait until success — all tables + starter content)

> **Fresh install only.** Importing on a live DB with orders will wipe data.

---

## Step 4 — Folder permissions

In File Manager, set folders to **755** and files to **644**.

These must be **writable** by PHP (755 or 775):

```
storage/sessions/
public/assets/uploads/
public/assets/uploads/products/
public/assets/uploads/combos/
public/assets/uploads/categories/
public/assets/uploads/hero/
public/assets/uploads/cta/
public/assets/uploads/testimonials/
public/assets/uploads/settings/
public/assets/uploads/receipts/
```

cPanel → **Select folder → Change Permissions → Write** for Owner (and Group if needed).

---

## Step 5 — PHP version

1. cPanel → **Select PHP Version** (or MultiPHP Manager)
2. Choose **PHP 8.2** or **8.3**
3. Enable extensions: `pdo_mysql`, `mbstring`, `fileinfo`, `gd`, `json`, `session`

---

## Step 6 — Verify

Open these URLs:

| URL | Expected |
|-----|----------|
| `https://giftvibelk.lk/` | Homepage loads |
| `https://giftvibelk.lk/shop` | Shop page |
| `https://giftvibelk.lk/admin` | Admin login |
| `https://giftvibelk.lk/sitemap.xml` | XML sitemap |
| `https://giftvibelk.lk/robots.txt` | Robots file |

### Admin login

Open `/admin/login` and sign in with the configured administrator phone number and OTP. Test OTP/SMS delivery before launch.

---

## Step 7 — Post-launch

1. Submit sitemap in Google Search Console:  
   `https://giftvibelk.lk/sitemap.xml`
2. Admin → Settings — update phone, address, logo, social links
3. Remove sample products/testimonials if not needed
4. Enable SSL (AutoSSL / Let's Encrypt) if not already active

---

## Security checklist

- [ ] Document root points to `public/` only
- [ ] `config.php` and `schema.sql` blocked by root `.htaccess` (included)
- [ ] Admin OTP/SMS delivery tested
- [ ] HTTPS forced in cPanel
- [ ] Database user limited to this database only

---

## Troubleshooting

| Problem | Fix |
|---------|-----|
| 500 error | Check PHP error log in cPanel; verify PHP 8.2+ |
| Blank page | Enable `display_errors` temporarily; check `storage/sessions` writable |
| CSS/images missing | Document root must be `public/` |
| DB connection failed | Re-check `config.php` names match cPanel MySQL |
| Upload fails | Fix permissions on `public/assets/uploads/` |
| `/admin` 404 | Enable `mod_rewrite`; check `.htaccess` in `public/` |

---

## Files included in zip

```
giftvibelk-deployment.zip
├── app/              PHP application code
├── public/           Web root (point domain here)
├── resources/        Views & assets
├── routes/           URL routes
├── storage/          Sessions (writable)
├── config.php        DB + app URL (production values)
├── schema.sql        Full DB schema + seed data
├── .htaccess         Root security + rewrite
└── DEPLOYMENT.md     This guide
```

---

## Production URLs

- Site: `https://giftvibelk.lk`
- Admin: `https://giftvibelk.lk/admin`
- Sitemap: `https://giftvibelk.lk/sitemap.xml`
