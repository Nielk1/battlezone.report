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
