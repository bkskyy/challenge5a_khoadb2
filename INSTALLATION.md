# PROG05 Installation & Deployment Guide

## Complete Step-by-Step Setup

### Prerequisites Check
- PHP 7.4+ with PDO MySQL support
- MySQL 5.7+ or MariaDB
- Apache or Nginx web server
- Write permissions in project directory

---

## Installation Steps

### Step 1: Copy Project Files

Place the PROG05 directory in your web root:
```
Windows: C:\xampp\htdocs\PROG05
Linux:   /var/www/html/PROG05
macOS:   /Library/WebServer/Documents/PROG05
```

### Step 2: Create MySQL Database

**Option A: Command Line**
```bash
mysql -u root -p
mysql> source /path/to/PROG05/database.sql;
```

**Option B: PHPMyAdmin**
1. Open PHPMyAdmin
2. Click "Import" tab
3. Select `database.sql`
4. Click "Import" button

**Option C: MySQL GUI Tool**
1. Open MySQL Workbench or similar
2. Open `database.sql`
3. Execute script

### Step 3: Configure Database Connection

Edit: `includes/db.php`

```php
// Line 13-17: Update these constants
const DB_HOST = 'localhost';        // Your MySQL server
const DB_NAME = 'student_management_system';  // Database name
const DB_USER = 'root';             // MySQL username
const DB_PASS = '';                 // MySQL password
```

### Step 4: Set Directory Permissions

**Linux/macOS:**
```bash
cd /path/to/PROG05

# Make directories writable
chmod 755 uploads
chmod 755 uploads/avatars
chmod 755 uploads/assignments
chmod 755 uploads/puzzles

# Make PHP files readable
chmod 644 *.php
chmod 644 includes/*.php
chmod 644 teacher/*.php
chmod 644 student/*.php
chmod 644 assets/*.css
```

**Windows:**
- Right-click folder → Properties
- Security tab → Edit → check "Modify"
- Apply to this folder, subfolders, and files

### Step 5: Verify Installation

1. Open browser: `http://localhost/PROG05/`
2. You should be redirected to `login.php`
3. If you see login form, setup is successful!

### Step 6: Create Demo Data

Visit: `http://localhost/PROG05/setup.php`

Click "Create Demo Data" to create:
- Teacher account (teacher / password123)
- Student account (student / password123)

---

## Post-Installation Checklist

- [ ] Database imported successfully
- [ ] Database credentials verified in db.php
- [ ] Upload directories have correct permissions
- [ ] Can access login.php without errors
- [ ] Demo accounts created via setup.php
- [ ] Can login with teacher account
- [ ] Can login with student account
- [ ] Dashboard loads correctly
- [ ] Navigation menu working

---

## Troubleshooting

### Database Connection Error
```
Error: "Database Connection Error"
```
**Solutions:**
1. Verify MySQL is running
2. Check database credentials in `includes/db.php`
3. Confirm database name matches `database.sql`
4. Ensure PDO MySQL extension is enabled

**Check PDO:** Create test_pdo.php with:
```php
<?php
echo extension_loaded('pdo_mysql') ? 'PDO MySQL is enabled' : 'PDO MySQL is NOT enabled';
?>
```

### File Upload Not Working
```
Error: "Failed to move uploaded file"
```
**Solutions:**
1. Check directory permissions: `chmod 755 uploads/`
2. Verify disk space available
3. Check PHP `upload_max_filesize` in php.ini
4. Verify temp directory permissions

**Check Permissions:**
```bash
ls -la /path/to/PROG05/uploads/
```
Should show: `drwxr-xr-x`

### Blank White Page
```
Nothing displays, completely blank
```
**Solutions:**
1. Enable PHP error reporting (add to top of index.php):
```php
ini_set('display_errors', 1);
error_reporting(E_ALL);
```
2. Check Apache/Nginx error logs
3. Verify PHP version is 7.4+

### Login Not Working

**Cannot find user:**
- Run setup.php again to create demo accounts
- Check database: `SELECT * FROM users;`

**Wrong password:**
- Password is case-sensitive
- Default: `password123`
- For custom passwords, verify bcrypt hashing

### Avatar or File Not Showing
```
Images/files returning 404
```
**Solutions:**
1. Check file path in database
2. Verify file exists in uploads/
3. Check file permissions (644)
4. Try re-uploading the file

---

## Database Verification

### Check if tables created:
```sql
USE student_management_system;
SHOW TABLES;
```

Should show:
- users
- messages
- assignments
- submissions
- puzzles

### Check database structure:
```sql
DESC users;
DESC messages;
DESC assignments;
DESC submissions;
DESC puzzles;
```

### Create test user:
```sql
INSERT INTO users (username, password, fullname, email, phone, role) 
VALUES ('test', SHA2('password123', 256), 'Test User', 'test@test.com', '555-0000', 'student');
```

---

## Performance Optimization

### Add Indexes (already done in database.sql):
```sql
CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_messages_conversation ON messages(sender_id, receiver_id);
CREATE INDEX idx_assignments_teacher ON assignments(teacher_id);
CREATE INDEX idx_submissions_student ON submissions(student_id);
```

### Enable Caching:
Add to `.htaccess` (Apache):
```apache
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresDefault "access plus 2 days"
    ExpiresByType text/css "access plus 1 year"
    ExpiresByType application/javascript "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
</IfModule>
```

---

## Security Hardening

### 1. Remove setup.php after installation
```bash
rm setup.php
```

### 2. Create .htaccess to prevent directory listing
```apache
Options -Indexes
```

### 3. Protect includes/ directory
```apache
<Directory "/path/to/PROG05/includes">
    Deny from all
</Directory>
```

### 4. Set proper file permissions
```bash
# Read-only for PHP files
chmod 644 *.php includes/*.php teacher/*.php student/*.php

# Writable only for uploads
chmod 755 uploads/
chmod 777 uploads/avatars/
chmod 777 uploads/assignments/
chmod 777 uploads/puzzles/
```

### 5. Disable PHP execution in uploads
Create `.htaccess` in uploads/avatars/ and other upload directories:
```apache
php_flag engine off
AddType text/plain .php .phtml .php3 .php4 .php5 .phtml
```

---

## Environment-Specific Setup

### Local Development (XAMPP/WAMP/MAMP)

1. Import database.sql via PHPMyAdmin
2. Update credentials (usually root/empty)
3. Access: `http://localhost/PROG05`

### Staging/Production Server

1. Create new MySQL user:
```sql
CREATE USER 'prog05'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON student_management_system.* TO 'prog05'@'localhost';
FLUSH PRIVILEGES;
```

2. Update `includes/db.php`:
```php
const DB_USER = 'prog05';
const DB_PASS = 'strong_password_here';
```

3. Set restrictive permissions:
```bash
chmod 600 includes/db.php  # Only owner can read
chmod 755 uploads/
```

4. Enable HTTPS in web server config
5. Disable error display (remove display_errors)

### Docker Deployment

Create `docker-compose.yml`:
```yaml
version: '3'
services:
  mysql:
    image: mysql:5.7
    environment:
      MYSQL_DATABASE: student_management_system
      MYSQL_ROOT_PASSWORD: root
    ports:
      - "3306:3306"
  
  web:
    image: php:7.4-apache
    volumes:
      - .:/var/www/html
    ports:
      - "80:80"
    depends_on:
      - mysql
```

---

## Backup & Recovery

### Backup Database:
```bash
mysqldump -u root -p student_management_system > backup.sql
```

### Backup Files:
```bash
tar -czf PROG05_backup.tar.gz PROG05/
```

### Restore Database:
```bash
mysql -u root -p student_management_system < backup.sql
```

---

## Monitoring & Maintenance

### Regular Tasks:

1. **Weekly:** Clean old session files
```bash
find /tmp -name "sess_*" -type f -atime +7 -delete
```

2. **Monthly:** 
   - Review error logs
   - Check disk usage (uploads/)
   - Verify database integrity

3. **Quarterly:**
   - Database optimization
   - Security audit
   - Backup verification

### Database Maintenance:
```sql
-- Optimize tables
OPTIMIZE TABLE users;
OPTIMIZE TABLE messages;
OPTIMIZE TABLE assignments;
OPTIMIZE TABLE puzzles;

-- Repair tables (if needed)
REPAIR TABLE users;
```

---

## Migration from Other Systems

### From Basic PHP to This System:

1. Export existing users:
```sql
-- Export from old system
SELECT username, password, fullname, email, phone 
FROM old_users 
INTO OUTFILE '/tmp/users.csv';
```

2. Import into new system:
```sql
-- Load users (remember to hash passwords!)
LOAD DATA INFILE '/tmp/users.csv' 
INTO TABLE users 
FIELDS TERMINATED BY ','
(username, password, fullname, email, phone);
```

---

## Rollback Procedure

If something goes wrong:

1. **Restore from backup:**
```bash
# Restore database
mysql -u root -p < backup.sql

# Restore files
tar -xzf PROG05_backup.tar.gz
```

2. **Revert last change:**
```bash
git revert HEAD  # If using version control
```

3. **Clear cache:**
```bash
rm -rf uploads/*        # WARNING: Deletes all uploads
php -r 'session_start(); session_destroy();'
```

---

## Going Live Checklist

- [ ] Database backed up
- [ ] All files uploaded
- [ ] Permissions set correctly
- [ ] Database credentials updated
- [ ] SSL certificate installed
- [ ] Error logging configured
- [ ] Monitoring set up
- [ ] Backup schedule established
- [ ] Admin account created
- [ ] Demo accounts deleted
- [ ] setup.php removed
- [ ] .htaccess configured
- [ ] Firewall rules set

---

## Support & Resources

- **Errors:** Check error_log / browser console
- **Database:** Use phpMyAdmin for debugging
- **Permissions:** Use `stat` command to verify
- **PHP:** Run `phpinfo()` to check settings
- **MySQL:** Use `SHOW PROCESSLIST;` to debug

---

## Quick Commands Reference

```bash
# Check PHP version
php -v

# Check PHP extension
php -m | grep pdo

# List files recursively
find PROG05 -type f

# Check file permissions
stat filename

# Change directory permissions
chmod 755 dirname

# Check MySQL connection
mysql -u root -p -h localhost

# View Apache error log
tail -f /var/log/apache2/error.log

# View PHP error log
tail -f /var/log/php-fpm.log
```

---

**Installation complete! You're ready to go! 🎉**
