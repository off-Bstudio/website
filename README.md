# Breaker Studio — PHP + MySQL edition

The same site (Home, Studio, Games, Careers, EN/FR toggle) and admin panel
as before, rewritten in PHP so it runs directly on InfinityFree (or any
standard PHP + MySQL shared host).

## Before you upload anything

**1. Create the database.**
Log in to the InfinityFree control panel → MySQL Databases. Your account
prefix is `if0_42427264`; create a database (e.g. name it `breakerstudio`)
and it'll become something like `if0_42427264_breakerstudio`. The panel
screen shows the exact name once created.

**2. Edit `config.php`.**
Open `config.php` and set `DB_NAME` to that exact database name:

```php
define('DB_NAME', 'if0_42427264_breakerstudio'); // <- your real name here
```

The other values (`DB_HOST`, `DB_USER`, `DB_PASS`) are already filled in
from what you shared. Also change `SESSION_SECRET` to any long random
string — it's just used to sign session cookies.

**3. Import the schema.**
In the control panel, open **phpMyAdmin** for that database → **Import**
tab → choose `schema.sql` → Go. This creates all 5 tables and seeds the
site with the original 5 games, 4 job offers, and a default admin login:

```
username: admin
password: admin123
```

**Change this password immediately after your first login** (Accounts tab
→ "Your admin password", at the bottom of the dashboard).

**4. Upload the files.**
Use InfinityFree's File Manager or an FTP client (FileZilla, etc. — your
FTP details are in the same control panel) to upload everything in this
folder into `htdocs/` (InfinityFree's web root). Keep the folder structure
exactly as-is:

```
htdocs/
├── index.php, studio.php, games.php, careers.php
├── config.php
├── css/, js/
├── includes/
└── admin/
```

That's it — visit your domain for the public site, and
`yourdomain.com/admin/login.php` for the admin panel.

## What's different from the Flask/Python version

Functionally, nothing — same pages, same admin features (accounts, games,
careers/recruiting toggle), same EN/FR toggle, same look. Under the hood:

- **PHP instead of Python/Flask** — runs on any standard shared host,
  which is what let this move to InfinityFree in the first place (their
  free tier doesn't run Python).
- **MySQL/MariaDB instead of SQLite** — matches what InfinityFree provides.
- Sessions and forms use plain PHP (`$_SESSION`, PDO prepared statements)
  instead of Flask/Jinja.
- Added CSRF tokens on every form (this wasn't in the Flask version — worth
  backporting there too, honestly, if you ever go back to it).

## What the admin panel controls

Same as the Flask version:

- **Accounts tab** — create, search/filter, edit, suspend/reactivate,
  delete player accounts; change your own admin password.
- **Games tab** — add/edit/delete game cards (title, genre in both
  languages, year, status, description in both languages, card color,
  display order). Top 3 by display order feature on the homepage.
- **Careers tab** — one button turns recruiting on/off site-wide (off
  shows a "not hiring" message everywhere and hides the job list, without
  deleting anything); add/edit/delete job offers.

## Files

```
breaker-studio-php/
├── config.php              Database connection settings — edit DB_NAME
├── schema.sql               Import this via phpMyAdmin
├── index.php / studio.php / games.php / careers.php     Public pages
├── css/site.css, js/site.js         Public site styling + behavior
├── css/admin.css, js/admin.js       Admin panel styling + behavior
├── includes/
│   ├── functions.php        Shared helpers (escaping, CSRF, settings)
│   ├── auth.php             Login/session handling
│   ├── site_head.php / site_foot.php     Public page shell
│   ├── hiring_cta.php        Shared "We're hiring" banner
│   └── admin_head.php / admin_foot.php   Admin page shell + tabs
└── admin/
    ├── login.php / logout.php
    ├── dashboard.php         Accounts tab
    ├── games.php              Games tab
    └── careers.php            Careers tab
```

## Security notes

- Passwords are hashed with PHP's `password_hash()` (bcrypt) — never
  stored in plain text.
- All forms are protected against CSRF.
- All database queries use prepared statements (protects against SQL
  injection).
- Still worth doing before this handles anything sensitive: HTTPS (check
  if your InfinityFree domain supports free SSL in the panel), rate
  limiting on `/admin/login.php`, and rotating the MySQL password you
  shared earlier since it's now been posted in a chat log.
