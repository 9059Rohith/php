# Password Hash Reference

## Admin Credentials
- **Email**: rajuchaswik@gmail.com
- **Password**: Raju@2006
- **Hash**: `$2b$10$7/BQ5crKO9xCL56pwDKY6OeUDcOkQO.oYQV6FE4g9ti5Tcln70lRu`

## Other Seeded Credentials
- **Staff Email**: staff@student.local
- **Student Email**: student@student.local
- **Password (both)**: password (or equivalent)
- **Hash**: `$2y$10$wH2cM8UjQhQbQ8e1gF9bQeY8i2t8QW4pY1sJ7z0G7n4Q0r9YfFJ7V6`

## How to Update Passwords in Database

### Method 1: Using MySQL Command Line
```bash
mysql -u root -p student_db
```

```sql
-- Update admin password to Raju@2006
UPDATE users SET password_hash = '$2b$10$7/BQ5crKO9xCL56pwDKY6OeUDcOkQO.oYQV6FE4g9ti5Tcln70lRu' 
WHERE email = 'rajuchaswik@gmail.com';

-- Generate new password hash (replace USER_EMAIL and HASH)
UPDATE users SET password_hash = 'HASH' WHERE email = 'USER_EMAIL';
```

### Method 2: Generate New Password Hash

#### Using PHP
```php
<?php
$password = "YourPassword";
$hash = password_hash($password, PASSWORD_DEFAULT);
echo $hash;
?>
```

#### Using Python
```python
import bcrypt

password = "YourPassword"
salt = bcrypt.gensalt(rounds=10)
hashed = bcrypt.hashpw(password.encode(), salt)
print(hashed.decode())
```

#### Using Online Generator
Visit: https://bcrypt-generator.com/
- Enter password
- Set cost to 10
- Copy hash
- Use in UPDATE query above

## Common Password Hashes for Testing

Use these pre-generated hashes if you need to set passwords quickly:

```sql
-- Password: password
UPDATE users SET password_hash = '$2y$10$wH2cM8UjQhQbQ8e1gF9bQeY8i2t8QW4pY1sJ7z0G7n4Q0r9YfFJ7V6' WHERE email = 'user@example.com';

-- Password: test123
UPDATE users SET password_hash = '$2b$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36DRcT36' WHERE email = 'user@example.com';

-- Password: admin123
UPDATE users SET password_hash = '$2b$10$7/BQ5crKO9xCL56pwDKY6OeUDcOkQO.oYQV6FE4g9ti5Tcln70lRu' WHERE email = 'user@example.com';
```

## Reset User Password
```sql
-- Reset password for any user
UPDATE users SET password_hash = '$2b$10$7/BQ5crKO9xCL56pwDKY6OeUDcOkQO.oYQV6FE4g9ti5Tcln70lRu' 
WHERE email = 'user@example.com';

-- This sets password to: Raju@2006
```

## Verify Password Hash Works
```php
<?php
// This PHP code verifies if a password matches a hash
$password = "Raju@2006";
$hash = '$2b$10$7/BQ5crKO9xCL56pwDKY6OeUDcOkQO.oYQV6FE4g9ti5Tcln70lRu';

if (password_verify($password, $hash)) {
    echo "Password is correct!";
} else {
    echo "Password is incorrect!";
}
?>
```

## Notes
- Bcrypt hashes starting with `$2a$`, `$2b$`, or `$2y$` are all compatible with PHP's `password_verify()`
- New hashes generated with `password_hash(..., PASSWORD_DEFAULT)` will use `$2y$` prefix
- Always use bcrypt (password_hash) for new passwords, never store plain text
- The cost factor of 10 is appropriate for most applications
