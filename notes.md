Symptom: Vite manifest not found
Meaning: This error means Vite hasn’t built your frontend assets yet or the vite configuration was updated, so the manifest file (`public/build/manifest.json`) is missing.
Fix:
    1. Install dependencies (if not done yet): `npm install`
    2. Build assets for development: `npm run dev`
       * Or for production: `npm run build`

create controller `php artisan make:controller MyController`
create view `php artisan make:view viewname`
create model `php artisan make:model MyModel`







### 1. Install PHP (Single Threaded)
- Download the latest **Non Thread Safe** PHP zip from [windows.php.net/download](https://windows.php.net/download).
- Extract to `C:\php` or a custom path, we will assume `C:\php`.
- Add `C:\php` to your system PATH.

### 2. Enable SQLite
- In `php.ini`, ensure these lines are present and uncommented:
    ```
    extension=sqlite3
    extension=pdo_sqlite
    ```

### 3. Install Xdebug
- Download the correct Xdebug DLL from [xdebug.org/download](https://xdebug.org/download) (match your PHP version).
- Place the DLL in `C:\php\ext`.
- Add to `php.ini`:
    ```
    zend_extension="C:\php\ext\php_xdebug.dll"
    xdebug.mode=debug
    xdebug.start_with_request=yes
    ```

### 4. Install Composer
- Download and run [Composer-Setup.exe](https://getcomposer.org/download/) (adds Composer to PATH).

### 5. Clone The Project
- Open terminal and run:
    ```
    git clone https://github.com/Nielk1/battlezone.report.git
    cd battlezone.report
    ```

### 6. Install Project Dependencies
- In the project folder, run:
    ```
    composer install
    ```

### 7. Compile/Run Project
- Follow your project’s README or run the main PHP file:
    ```
    php your_main_file.php
    ```

### Notes
- `php artisan migrate` may be needed to prepare the database

### 5. Create a New Laravel Project

- In your terminal, run:
    ```
    composer create-project laravel/laravel example-app
    cd example-app
    ```

### 6. Configure SQLite for Laravel

- In the project folder, create a SQLite database file:
    ```
    touch database.sqlite
    ```
    *(On Windows, use `type nul > database\database.sqlite`)*
    *you may still need to `php artisan migrate`*

- In .env, set:
    ```
    DB_CONNECTION=sqlite
    DB_DATABASE=/absolute/path/to/database/database.sqlite
    ```

### 7. Run Laravel Development Server

- Start the server:
    ```
    php artisan serve
    ```

**Note:** Replace `/absolute/path/to/database/database.sqlite` with your actual path.**Note:** Replace `/absolute/path/to/database/database.sqlite` with your actual path.


VSCode extensions in use (when in doubt use the more popular one):
* PHP Intelephense (for PHP code intelligence)
* PHP Debug (for Xdebug integration)
* Laravel Extension Pack (includes several Laravel-related tools)





Deployment:
Absolutely! Here’s a practical, step-by-step guide for preparing your Laravel site for deployment on a shared host like Dreamhost, with a focus on **pre-baking data** and **database setup**. This is tailored for a typical Laravel app, but let me know if you have custom requirements.

---

## 1. **Pre-bake (Pre-generate) Static Data/Pages**

**Why:**  
Shared hosts often have limited resources and may not support background jobs, queues, or heavy dynamic generation. Pre-baking means generating static files or caching data ahead of time.

**How:**
- **Blade Views:**  
  - Use Laravel’s built-in view caching:  
    ```sh
    php artisan view:cache
    ```
- **Config Cache:**  
  - Cache your config for performance:  
    ```sh
    php artisan config:cache
    ```
- **Route Cache:**  
  - Cache your routes:  
    ```sh
    php artisan route:cache
    ```
- **Static Data:**  
  - If you have data (like articles, team, etc.) that doesn’t change often, consider exporting it as JSON or static HTML and serving it directly, or cache it in Laravel (see below).
- **Pre-rendered Pages:**  
  - For pages that don’t change often, you can use [spatie/laravel-responsecache](https://github.com/spatie/laravel-responsecache) or a similar package to cache full responses.

---

## 2. **Prepare the Database**

### **A. Export Your Local Database**

1. **Export your database as SQL:**
   ```sh
   mysqldump -u youruser -p yourdatabase > backup.sql
   ```

2. **If you use SQLite locally, convert to MySQL-compatible SQL or migrate data manually.**

---

### **B. Import to Dreamhost Database**

**Option 1: Use phpMyAdmin (Dreamhost Panel)**
- Log in to Dreamhost panel.
- Go to MySQL Databases > Manage Databases.
- Find your database and click “phpMyAdmin.”
- Use the Import tab to upload your `backup.sql`.

**Option 2: Use SSH and MySQL CLI**
- SSH into your Dreamhost server.
- Upload your `backup.sql` file (e.g., with `scp` or `sftp`).
- Import:
  ```sh
  mysql -u your_db_user -p -h your_db_host your_db_name < backup.sql
  ```

**Option 3: Use Local SSH Tunnel**
- Set up an SSH tunnel from your local machine:
  ```sh
  ssh -L 3307:your-db-host:3306 your-ssh-user@your-dreamhost-domain
  ```
- Connect to `localhost:3307` with your MySQL client and import as usual.

---

### **C. Update .env for Production**

- Set your Dreamhost DB credentials in .env:
  ```
  DB_CONNECTION=mysql
  DB_HOST=your-db-host.dreamhost.com
  DB_PORT=3306
  DB_DATABASE=your_db_name
  DB_USERNAME=your_db_user
  DB_PASSWORD=your_db_password
  ```

---

### **D. Run Migrations (If Possible)**

If you can run `php artisan` on the server:
```sh
php artisan migrate --force
php artisan db:seed --force
```
If not, make sure your SQL dump includes all tables and data.

---

## 3. **Other Laravel Prep Steps**

- **Composer:**  
  If you can’t run `composer install` on the server, run it locally and upload the vendor directory.
- **Public Directory:**  
  Make sure your Dreamhost web root points to Laravel’s public directory.
- **Storage:**  
  Set correct permissions for storage and cache.
- **Environment:**  
  Set `APP_ENV=production` and `APP_DEBUG=false` in .env.

---

## 4. **Pre-bake/Caching Dynamic Data**

If you have dynamic data that’s expensive to generate:
- Use Laravel’s cache system to cache queries or rendered HTML.
- You can pre-warm the cache locally and upload the cache files, or run a script on the server to generate them.

---

## 5. **Testing**

- Test your site locally with the production .env settings (using the remote DB via SSH tunnel if needed).
- After upload, test all routes and data.

---

## **Summary Checklist**

- [ ] Export local database as SQL.
- [ ] Import SQL to Dreamhost DB (phpMyAdmin, SSH, or tunnel).
- [ ] Update .env with Dreamhost DB credentials.
- [ ] Pre-bake/copy static data as needed.
- [ ] Cache views, config, and routes (`php artisan ...:cache`).
- [ ] Upload code, including vendor if needed.
- [ ] Set correct permissions.
- [ ] Point web root to public.
- [ ] Test site.

---

**Let me know if you want a script for any of these steps, or if you have a specific data structure you want to pre-bake!**





php artisan view:cache
php artisan config:cache
php artisan route:cache
npm run build

cp .env .env.dev
cp .env.prod .env
php artisan config:cache
// builds bootstrap/cache/config.php
cp .env.dev .env

set dreamhost path for site the public subfolder

///
Before you run any migrations, update app/Providers/AppServiceProvider.php::boot() with
public function boot()
{
    Schema::defaultStringLength(191);
}
This will allow migrations to work with DreamHost’s older MySQL database system.
/// ^ not sure if this is needed today


OMIT:
`node_modules` NEVER UPLOAD THIS!
resources/sass
resources/js
resources/css - IDK, just suspect this thing I am using SASS not CSS
resources/views - because we pre-baked, but might be worth sending anyway if we need to rebuild the cache manually on the server, so send it anyway

Not needed:
.git
.github
.vscode
tests
phpunit.xml
*etc (obvious dev files)

Need:
/your-laravel-app
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
│   ├── build/
│   ├── css/
│   ├── js/
│   └── index.php
├── resources/
├── routes/
├── storage/
├── vendor/
├── .env
├── artisan
├── composer.json
├── composer.lock
└── ... (other Laravel files)


Ignore everything above, mostly, things are better than we thought.
Must be done locally and makes files to push from public/build
    npm run build
Can be done on the server:
    php artisan view:cache
    php artisan config:cache
    php artisan route:cache
config cache builds .env to cache
view cache builds views from view blades


