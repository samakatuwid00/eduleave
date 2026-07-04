# EduLeave

EduLeave is a web-based employee leave-card management system. It gives employees a self-service view of their leave records and gives administrators tools for account approval, employee management, and maintenance of teaching and non-teaching leave cards.

## Features

- Employee registration, email verification, login, and password recovery
- Administrator approval or rejection of newly registered accounts
- Separate administrator and employee dashboards
- Employee profiles containing personnel and employment information
- Dedicated leave-card formats for teaching and non-teaching personnel
- Add, edit, and delete individual leave-card entries
- Downloadable Excel templates and `.xlsx` bulk imports
- Queued approval, rejection, verification, and password-reset emails
- Cloudflare Turnstile support for bot protection

## Technology stack

- PHP 8.2+
- Laravel 12
- MySQL
- Blade, Tailwind CSS, Alpine.js, and Vite
- Pest / PHPUnit
- SimpleXLSX and SimpleXLSXGen for spreadsheet import and export

## Local setup

### Prerequisites

Install PHP 8.2 or newer, Composer, Node.js with npm, and MySQL. Ensure the PHP extensions required by Laravel and MySQL are enabled.

### Installation

1. Install the dependencies:

   ```bash
   composer install
   npm install
   ```

2. Create a local environment file and application key:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   On PowerShell, use `Copy-Item .env.example .env` instead of `cp`.

3. Configure the database, application URL, mail server, and optional Turnstile credentials in `.env`. At minimum, review:

   ```dotenv
   APP_URL=http://localhost:8000

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=eduleave
   DB_USERNAME=root
   DB_PASSWORD=

   MAIL_MAILER=log

   TURNSTILE_ENABLED=false
   ```

   `MAIL_MAILER=log` is convenient locally because emails are written to the application log rather than delivered. Never commit real database, mail, or Turnstile credentials.

4. Create the database tables:

   ```bash
   php artisan migrate
   ```

5. Start the development services:

   ```bash
   composer run dev
   ```

   This starts the Laravel server, Vite, the scheduler, the default queue listener, and the application log viewer. Open `http://localhost:8000`.

## Accounts and access

New employee accounts begin with a `pending` status. After email verification, an administrator must approve the account before the employee can access the full dashboard. Rejected and pending registrations are available from the administrator dashboard.

The default database seeder creates a basic test user, not an administrator account. For development, an existing user can be promoted with Laravel Tinker:

```bash
php artisan tinker
```

```php
$user = App\Models\User::where('email', 'admin@example.com')->firstOrFail();
$user->forceFill([
    'usertype' => 'admin',
    'status' => 'approved',
    'email_verified_at' => now(),
])->save();
```

## Leave-card imports

Administrators can open an employee's leave card, download the template for that employee's personnel type, fill it in, and upload it as an `.xlsx` file. Teaching and non-teaching templates use different columns. The importer validates the template headers and numeric fields before saving any rows, and uploads are limited to 10 MB.

## Transactional email and queues

Mail jobs use the database-backed `mail` queue. The scheduler processes at most five mail jobs per minute:

```bash
php artisan schedule:work
```

`composer run dev` already runs the scheduler locally. If the site is started another way, keep the command above running in a separate terminal.

In production, configure a cron job to execute the scheduler every minute:

```cron
* * * * * cd /path/to/eduleave && php artisan schedule:run >> /dev/null 2>&1
```

Do not run a separate worker for the `mail` queue, because doing so bypasses the five-messages-per-minute delivery limit. The default queue may be handled by a normal queue worker:

```bash
php artisan queue:work --queue=default
```

## Testing

Run the automated test suite with:

```bash
php artisan test
```

The tests cover authentication, profiles, employee approval data, normalized leave-card behavior, Excel imports, dashboards, and transactional email safety.

## Production checklist

- Set `APP_ENV=production` and `APP_DEBUG=false`.
- Use unique production credentials and keep them outside version control.
- Run `php artisan migrate --force` during deployment.
- Build frontend assets with `npm run build`.
- Point the web server document root to `public/`.
- Run the Laravel scheduler every minute.
- Run a worker for the `default` queue and supervise long-running processes.
- Ensure `storage/` and `bootstrap/cache/` are writable by the web server.
- Configure HTTPS, the production mail provider, and Cloudflare Turnstile keys.

## License

EduLeave is built on the Laravel framework, which is licensed under the [MIT License](https://opensource.org/licenses/MIT).
