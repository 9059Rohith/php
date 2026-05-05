# Running the E-Commerce Application

## ⚠️ System Requirements

Your application needs:
- **PHP** 7.4 or higher
- **MySQL** 5.7+ or **MariaDB**
- **Apache** or **Nginx** web server (optional if using PHP built-in server)

---

## Option 1: Quick Setup with PHP Built-in Server (Easiest)

### Prerequisites
- Install PHP from https://www.php.net/downloads.php
- Install MySQL from https://dev.mysql.com/downloads/mysql/

### Steps

#### 1. Install PHP for Windows
- Download PHP from https://www.php.net/downloads.php (Non-Thread Safe)
- Extract to `C:\php` (or similar)
- Add to PATH: `C:\php`

#### 2. Install MySQL
- Download from https://dev.mysql.com/downloads/mysql/
- Run installer and note username/password
- Default: username = `root`, password = `root`

#### 3. Create Database
```bash
mysql -u root -p
```
Then in MySQL:
```sql
CREATE DATABASE ecommerce_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ecommerce_db;
SOURCE C:\path\to\ecommerce\schema.sql;
SOURCE C:\path\to\ecommerce\seed.sql;
```

#### 4. Start PHP Built-in Server
```bash
cd C:\Users\BhaviChasvi\Downloads\php\ecommerce
php -S localhost:8000
```

#### 5. Open Browser
- Navigate to: `http://localhost:8000`
- Login: `rajuchaswik@gmail.com` / `Raju@2006`

---

## Option 2: Using XAMPP (Recommended)

XAMPP includes PHP, Apache, and MySQL all in one.

### Steps

#### 1. Download XAMPP
- Visit https://www.apachefriends.org/
- Download for Windows
- Install to `C:\xampp`

#### 2. Start XAMPP Control Panel
- Start Apache
- Start MySQL

#### 3. Copy Project
```bash
# Copy ecommerce folder to htdocs
Copy-Item -Recurse "C:\Users\BhaviChasvi\Downloads\php\ecommerce" "C:\xampp\htdocs\ecommerce"
Copy-Item -Recurse "C:\Users\BhaviChasvi\Downloads\php\shared" "C:\xampp\htdocs\shared"
```

#### 4. Create Database
- Open http://localhost/phpmyadmin
- Create database: `ecommerce_db`
- Import `schema.sql` and `seed.sql`

#### 5. Access Application
- Navigate to: `http://localhost/ecommerce`
- Login: `rajuchaswik@gmail.com` / `Raju@2006`

---

## Option 3: Using Docker (Most Professional)

### Prerequisites
- Install Docker Desktop from https://www.docker.com/products/docker-desktop

### Steps

#### 1. Create docker-compose.yml
```yaml
version: '3.8'
services:
  mysql:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: ecommerce_db
    ports:
      - "3306:3306"
    volumes:
      - ./schema.sql:/docker-entrypoint-initdb.d/schema.sql
      - ./seed.sql:/docker-entrypoint-initdb.d/seed.sql

  php:
    image: php:8.1-apache
    ports:
      - "80:80"
    volumes:
      - ./:/var/www/html
    depends_on:
      - mysql
    environment:
      ECOM_DB_HOST: mysql
      ECOM_DB_NAME: ecommerce_db
      ECOM_DB_USER: root
      ECOM_DB_PASSWORD: root
```

#### 2. Run Docker
```bash
cd C:\Users\BhaviChasvi\Downloads\php
docker-compose up -d
```

#### 3. Access Application
- Navigate to: `http://localhost`
- Login: `rajuchaswik@gmail.com` / `Raju@2006`

---

## Option 4: Using Laragon (Windows Only)

Laragon is popular on Windows for local development.

### Steps
1. Download from https://laragon.org/
2. Install
3. Copy project to `C:\laragon\www\ecommerce`
4. Copy shared folder to `C:\laragon\www\shared`
5. Start Laragon
6. Create database via phpMyAdmin at http://localhost/phpmyadmin
7. Access at http://localhost/ecommerce

---

## Database Setup (All Options)

After setting up your server, create the database:

### Using MySQL Command Line
```bash
mysql -u root -p
```

```sql
CREATE DATABASE ecommerce_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ecommerce_db;
SOURCE C:\Users\BhaviChasvi\Downloads\php\ecommerce\schema.sql;
SOURCE C:\Users\BhaviChasvi\Downloads\php\ecommerce\seed.sql;
```

### Verify Installation
```sql
SELECT COUNT(*) as user_count FROM users;
-- Should return 3 (1 admin + 2 customers)

SELECT * FROM users WHERE email = 'rajuchaswik@gmail.com';
-- Should show admin user
```

---

## Test Accounts After Setup

### Admin
- **Email:** rajuchaswik@gmail.com
- **Password:** Raju@2006

### Test Customers
- **Email:** asha@shop.local
- **Email:** ravi@shop.local

---

## Troubleshooting

### Error: "PDO Extension Not Installed"
**Solution:** Enable PHP PDO extension
- Edit `php.ini`
- Uncomment: `extension=pdo_mysql`

### Error: "Database Connection Failed"
**Solution:** 
- Verify MySQL is running
- Check credentials in `includes/bootstrap.php`
- Verify database exists: `SHOW DATABASES;`

### Error: "Class PDO not found"
**Solution:**
- Install PDO extension for PHP
- Or use PDO through PHP configuration

### "404 Not Found"
**Solution:**
- Verify rewrite rules (if using Apache)
- Check file paths match your installation
- Verify `.htaccess` file exists (if needed)

### Error: "CSRF token error"
**Solution:**
- Clear browser cookies
- Ensure session.save_path is writable
- Check PHP session settings

---

## Recommended Setup for Development

1. **Use XAMPP** - Easiest setup for beginners
2. **Or use Docker** - Best for reproducible environments
3. **Or use Laragon** - Popular on Windows

---

## Next Steps

1. **Choose your setup method** (XAMPP recommended for quick start)
2. **Install required software**
3. **Create database** using provided SQL files
4. **Test login** with admin credentials
5. **Test registration** to create new users
6. **Verify all features** work

---

## Environment Variables (Optional)

You can configure database connection via environment variables:

```bash
# In Windows, add to System Environment Variables:
ECOM_DB_DRIVER=mysql
ECOM_DB_HOST=127.0.0.1
ECOM_DB_PORT=3306
ECOM_DB_NAME=ecommerce_db
ECOM_DB_USER=root
ECOM_DB_PASSWORD=
```

Or create a `.env` file:
```
ECOM_DB_DRIVER=mysql
ECOM_DB_HOST=127.0.0.1
ECOM_DB_PORT=3306
ECOM_DB_NAME=ecommerce_db
ECOM_DB_USER=root
ECOM_DB_PASSWORD=
```

---

## File Structure Required

```
C:\xampp\htdocs\          (or your web root)
├── ecommerce/            (your e-commerce app)
│   ├── schema.sql
│   ├── seed.sql
│   ├── index.php
│   ├── admin/
│   ├── customer/
│   └── includes/
├── shared/               (MUST be here)
│   └── core.php
```

**Note:** The `shared` folder must be in the same parent directory as `ecommerce` folder.

---

## Quick Start Commands

### For XAMPP Users
```bash
# 1. Copy files
Copy-Item -Recurse "C:\Users\BhaviChasvi\Downloads\php\ecommerce" "C:\xampp\htdocs\ecommerce"
Copy-Item -Recurse "C:\Users\BhaviChasvi\Downloads\php\shared" "C:\xampp\htdocs\shared"

# 2. Start XAMPP
C:\xampp\xampp-control.exe

# 3. Create database (via phpmyadmin)
# Navigate to http://localhost/phpmyadmin
# Create database ecommerce_db
# Import schema.sql and seed.sql

# 4. Access app
# http://localhost/ecommerce
```

### For Docker Users
```bash
cd C:\Users\BhaviChasvi\Downloads\php
docker-compose up -d
# http://localhost
```

### For PHP Built-in Server
```bash
cd C:\Users\BhaviChasvi\Downloads\php\ecommerce
php -S localhost:8000
# http://localhost:8000
```

---

**Ready to run? Pick one option above and follow the steps!** 🚀
