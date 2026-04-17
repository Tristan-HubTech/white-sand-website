# White Sand Resort Deployment Guide

This guide is the fastest practical way to publish this CodeIgniter 4 project online.

## Recommended Stack

- Hosting: Hostinger or any cPanel shared host
- Code source: GitHub repository
- Database: MySQL (phpMyAdmin)
- SSL and DNS: Cloudflare (optional but recommended)

## What Is Already Prepared In This Project

- Runtime and temp files are ignored in source control via .gitignore
- A production env template is included in .env.production.example
- Migrations and seeders exist for core schema and admin user setup

## Step 1: Push Project To GitHub

From the project root, run:

```bash
git add .
git commit -m "Prepare production deployment"
git branch -M main
git remote add origin https://github.com/YOUR-ACCOUNT/white-sand-website.git
git push -u origin main
```

If origin already exists, skip the remote add command.

## Step 2: Prepare Hosting

1. Buy or open a cPanel hosting account.
2. Add your domain in cPanel.
3. In cPanel, create a MySQL database and user.
4. Assign the user to the database with all privileges.

Save these values:

- database name
- database user
- database password
- database host (often localhost)

## Step 3: Deploy Code To Hosting

Use one of these methods:

1. Git in cPanel (preferred):

- Open Git Version Control in cPanel.
- Clone your GitHub repo.
- Set deploy path to a folder such as /home/USER/apps/white-sand.

1. Manual upload:

- Download source zip from GitHub.
- Upload and extract in File Manager to /home/USER/apps/white-sand.

## Step 4: Point Public Web Root Correctly

CodeIgniter must serve from the project public directory.

Use one of these approaches:

1. Best: set domain document root to /home/USER/apps/white-sand/public
2. If host cannot change document root: copy public contents into public_html and update public/index.php path constants to point to app and system

## Step 5: Create Production .env

On the server, create .env in the project root from .env.production.example and fill real values.

Minimum required keys:

- CI_ENVIRONMENT = production
- app.baseURL = <https://YOUR-DOMAIN.COM/>
- database.default.* values
- SMTP values for inquiry emails

Important:

- Keep .env outside public web access.
- Use strong credentials.

## Step 6: Install Composer Dependencies On Server

SSH into hosting (or use terminal tool if available):

```bash
cd /home/USER/apps/white-sand
composer install --no-dev --optimize-autoloader
```

If your host has no Composer, install locally then upload the vendor directory.

## Step 7: Run Database Setup

From project root on server:

```bash
php spark migrate --all
php spark db:seed AdminUserSeeder
```

Then immediately change the seeded admin password after first login.

## Step 8: File Permissions

Ensure writable paths are writable by PHP:

- writable/cache
- writable/logs
- writable/session
- writable/uploads

Typical safe setting:

- directories: 755
- files: 644

## Step 9: Final Validation

Check these URLs and flows:

1. Home page loads
2. Gallery page loads and images display
3. Inquiry form stores records
4. Admin login works
5. Admin gallery upload and inquiry status update work

## Optional Step 10: Cloudflare

1. Connect domain to Cloudflare
2. Enable Full (strict) SSL after origin certificate is ready
3. Enable caching and bot protection

## Rollback Plan

If deployment fails:

1. Restore previous deployed commit
2. Restore previous database backup
3. Re-check .env and writable permissions
4. Re-run php spark migrate:status to confirm schema state
