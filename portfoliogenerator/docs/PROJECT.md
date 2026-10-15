# Folioform Portfolio Template Generator

## Project Description

Folioform lets a visitor enter personal and professional details, save them as a portfolio, choose one of three designs, preview the result, and return to edit or delete it. Each portfolio has a public UUID link and a separate private management token. Public pages are read-only; keep the management URL private because it grants edit and delete access.

## Technologies

- Laravel 12 and PHP 8.2+
- Blade templates, responsive CSS, and vanilla JavaScript
- SQLite for local development; PostgreSQL for hosted deployment
- Neon as the managed PostgreSQL provider
- Railway as the prepared application hosting provider

## Pages and Templates

- Home: project introduction and create action
- Create/edit: personal details, profile image, education, skills, projects, experience, and links
- Template selection: exactly Simple, Modern, or Creative
- Preview: generated portfolio with edit and manage actions
- Manage: public link, template changes, editing, and permanent deletion

The three preview layouts are implemented in `resources/views/portfolios/show.blade.php`; the responsive design is in `public/css/portfolio.css`.

## Database Structure

The `portfolios` table stores one record per portfolio:

| Column | Stored data |
| --- | --- |
| `uuid` | Unique public portfolio link identifier |
| `management_token` | Separate private capability URL for editing and deletion |
| `full_name`, `email`, `contact_number`, `address`, `about_me` | Personal and contact information |
| `profile_image` | Uploaded image path or public image URL |
| `education`, `skills`, `projects`, `experience`, `links` | Structured JSON collections |
| `template` | One of `simple`, `modern`, or `creative` |
| `created_at`, `updated_at` | Record timestamps |

The schema is created by the portfolio table migration and extended with a separate management token migration.

## Local Setup

Requirements: PHP 8.2+, Composer, and the PHP SQLite extension.

```powershell
composer install
if (-not (Test-Path .env)) { Copy-Item .env.example .env }
if (-not (Select-String -Path .env -Pattern '^APP_KEY=.+$' -Quiet)) { php artisan key:generate }
if (-not (Test-Path database/database.sqlite)) { New-Item -ItemType File -Path database/database.sqlite | Out-Null }
php artisan migrate
php artisan storage:link
php artisan serve
```

Open `http://127.0.0.1:8000`. The included stylesheet and JavaScript are served from `public/`, so Node.js is not required to run the app.

## Online Database and Deployment

The repository is prepared for Neon PostgreSQL and Railway hosting. The actual Neon database and Railway deployment must be created under the project owner's accounts; credentials and a live URL are deliberately not committed.

1. Create a PostgreSQL project in Neon and copy its pooled or direct connection URL with SSL enabled.
2. Create a Railway project from this source repository and deploy using the included `railway.json` configuration.
3. In Railway's service variables, set `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` to the assigned HTTPS domain, and `APP_KEY` to a newly generated production key.
4. Set `DB_CONNECTION=pgsql` and `DB_URL` to the Neon connection URL. Keep the URL private; never add it to source control.
5. Generate a Railway public domain. The start script runs migrations and serves Laravel on Railway's assigned port. Verify the `/up` health check and create a test portfolio.

Generate a production key locally with `php artisan key:generate --show`, then enter the output directly in Railway's private variable settings. Do not paste it into project documentation or commit it. The production process runs `php artisan migrate --force` on startup.

Uploaded profile images use Laravel's public local disk. On hosts with ephemeral disks, use the profile image URL field or configure persistent object storage before relying on uploaded files.

- **Database platform:** Neon PostgreSQL, pending project owner provisioning.
- **Hosting platform:** Railway, pending project owner deployment.
- **Published URL:** Pending Railway domain creation.

## Screenshots

Browser captures of the working local system are included below. Capture updated screenshots again after any final visual changes or deployment branding:

- Home: `docs/screenshots/home.png`
- Create/edit form: `docs/screenshots/editor.png`
- Template chooser: `docs/screenshots/templates.png`
- Simple template: `docs/screenshots/template-simple.png`
- Modern template: `docs/screenshots/template-modern.png`
- Creative template: `docs/screenshots/template-creative.png`
- Manage page: `docs/screenshots/manage.png`
- Mobile template previews: `docs/screenshots/template-simple-mobile.png`, `docs/screenshots/template-modern-mobile.png`, `docs/screenshots/template-creative-mobile.png`

## Verification

Run `php artisan test` to exercise homepage rendering and the create, save, template selection, preview, edit, and delete flow.