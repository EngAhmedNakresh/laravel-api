# Project Setup

## Local development

This repository is a Laravel API backend. The frontend lives in a separate project.

### 1. Install dependencies

```powershell
composer install
```

### 2. Create your local environment file

If `.env` does not exist yet:

```powershell
Copy-Item .env.example .env
php artisan key:generate
```

Then update the local values below before running the app:

```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=proj2
DB_USERNAME=root
DB_PASSWORD=

SESSION_DOMAIN=localhost
SESSION_SECURE_COOKIE=false
SESSION_SAME_SITE=lax
SANCTUM_STATEFUL_DOMAINS=localhost:5173,127.0.0.1:5173,localhost:3000
FRONTEND_URL=http://localhost:5173
CORS_ALLOWED_ORIGINS=http://localhost:5173,http://127.0.0.1:5173,http://localhost:3000
```

### 3. Run migrations

```powershell
php artisan migrate --force
```

### 4. Create the public storage symlink

```powershell
php artisan storage:link
```

### 5. Start the API

```powershell
php artisan serve --host=127.0.0.1 --port=8000
```

The API will be available at `http://localhost:8000`.

## Railway deployment

### Required Railway variables

Set these variables in Railway:

```env
APP_NAME="Proj2 API"
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:YOUR_GENERATED_KEY
APP_URL=https://your-backend.up.railway.app

LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=info

DB_CONNECTION=pgsql
DB_HOST=YOUR_DB_HOST
DB_PORT=5432
DB_DATABASE=YOUR_DB_NAME
DB_USERNAME=YOUR_DB_USER
DB_PASSWORD=YOUR_DB_PASSWORD
# Or use DB_URL if your Railway database provides it

FILESYSTEM_DISK=public
QUEUE_CONNECTION=sync
CACHE_STORE=file
SESSION_DRIVER=file
SESSION_DOMAIN=
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=none

FRONTEND_URL=https://your-frontend.vercel.app
SANCTUM_STATEFUL_DOMAINS=your-frontend.vercel.app
CORS_ALLOWED_ORIGINS=https://your-frontend.vercel.app
# Optional for Vercel preview deployments:
# CORS_ALLOWED_ORIGIN_PATTERNS=^https://.*\.vercel\.app$
```

### Notes for Vercel frontend integration

- `FRONTEND_URL` is used as the primary allowed CORS origin.
- `CORS_ALLOWED_ORIGINS` supports comma-separated production origins.
- `CORS_ALLOWED_ORIGIN_PATTERNS` can be used for preview deployments such as Vercel preview URLs.
- If you use Sanctum across different domains, keep:
  - `SESSION_SECURE_COOKIE=true`
  - `SESSION_SAME_SITE=none`

### Docker / startup behavior

The Docker image now:

- installs both `pdo_mysql` and `pdo_pgsql`
- creates the public storage link on startup
- runs `php artisan migrate --force`
- does not auto-seed production data on every boot

## Useful local verification commands

```powershell
php artisan config:clear
php artisan route:list
php artisan test
```

## Notes

- Public uploads are served from the `public` disk via `public/storage`.
- Do not commit a real `.env` file.
- Set `APP_KEY` in Railway before first deploy.
