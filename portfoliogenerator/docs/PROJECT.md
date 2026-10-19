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

The application is deployed from the `main` branch to Railway. Railway PostgreSQL is attached to the app service, and Railway's private variables hold production configuration and credentials. Pushes to `main` trigger automatic deployments. The included start script runs migrations and serves Laravel on Railway's assigned port.

Uploaded profile images use Laravel's public local disk. On hosts with ephemeral disks, use the profile image URL field or configure persistent object storage before relying on uploaded files.

- **Database platform:** Railway PostgreSQL.
- **Hosting platform:** Railway, auto-deployed from `main`.
- **Published URL:** [https://portfolio-production-5550.up.railway.app](https://portfolio-production-5550.up.railway.app).

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