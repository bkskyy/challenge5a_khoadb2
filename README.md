# Student Management System - Complete Documentation

## Overview

A pure PHP Student Management System built with procedural programming, MySQL database, and clean HTML/CSS UI. No frameworks (like Laravel) are used as per requirements.

## Features

### 1. **User Management**
- User registration and login system
- Two roles: Teacher and Student
- Profile management with avatar support

### 2. **Teacher Module**
- **Manage Students**: Add, Edit, Delete student accounts
- **Assignments**: Upload files for students to download
- **Puzzle Creator**: Create puzzle games with file-based answers

### 3. **Student Module**
- **Profile Edit**: Update email, phone, and avatar
- **Avatar Upload**: Two methods:
  - Upload local file
  - Provide image URL
- **Assignments**: View and download assignments
- **Puzzles**: Solve puzzle games

### 4. **Messaging System**
- User list with all system users
- Send messages to any user
- Edit/Delete own messages

### 5. **Security Features**
- Password hashing using bcrypt
- PDO prepared statements to prevent SQL Injection
- Session-based authentication
- User input validation and sanitization

---

## Installation & Setup

### Prerequisites
- PHP 7.4+ (with PDO MySQL extension)
- MySQL 5.7+
- Apache or Nginx web server

### Step 1: Create Database

```bash
# Connect to MySQL
mysql -u root -p

# Create database and tables
source /path/to/database.sql
```

Or manually in PHPMyAdmin:
- Import the `database.sql` file

### Step 2: Configure Database Connection

Edit `includes/db.php` and update:
```php
const DB_HOST = 'localhost';
const DB_NAME = 'student_management_system';
const DB_USER = 'root';
const DB_PASS = '';
```

### Step 3: Create Upload Directories

The following directories must be writable:
```
uploads/avatars/
uploads/assignments/
uploads/puzzles/
```

Permissions:
```bash
chmod 755 uploads/
chmod 755 uploads/avatars/
chmod 755 uploads/assignments/
chmod 755 uploads/puzzles/
```

### Step 4: Access the Application

```
http://localhost/PROG05/login.php
```

---

## Demo Credentials

### Teacher Account
- **Username**: teacher
- **Password**: password123

### Student Account
- **Username**: student
- **Password**: password123

---

## File Structure

```
PROG05/
├── includes/
│   ├── db.php              # Database connection (PDO)
│   ├── auth.php            # Authentication & authorization
│   ├── helpers.php         # Helper functions
│   ├── header.php          # Navigation & layout
│   └── footer.php          # Footer layout
├── assets/
│   └── style.css           # Complete styling
├── admin/                  # Teacher administration
│   ├── manage_students.php # CRUD students
│   ├── assignments.php     # Upload & manage assignments
│   └── puzzles.php         # Puzzle creator
├── student/                # Student pages
│   ├── assignments.php     # View/download assignments
│   └── puzzles.php         # Solve puzzles
├── uploads/
│   ├── avatars/            # User avatars
│   ├── assignments/        # Assignment files
│   └── puzzles/            # Puzzle content files
├── login.php               # Login & registration
├── logout.php              # Logout handler
├── dashboard.php           # Main dashboard
├── profile.php             # User profile
├── users.php               # User listing
├── messages.php            # Messaging system
├── database.sql            # SQL schema
└── README.md               # This file
```

---

## Database Schema

### users
```sql
id, username, password, fullname, email, phone, avatar, role, created_at, updated_at
```

### messages
```sql
id, sender_id, receiver_id, content, is_edited, created_at, updated_at
```

### assignments
```sql
id, teacher_id, description, file_path, created_at, updated_at
```

### submissions
```sql
id, assignment_id, student_id, file_path, submitted_at, updated_at
```

### puzzles
```sql
id, teacher_id, hint, file_content_path, answer, created_at, updated_at
```

---

## How to Use

### Teacher Workflow

#### 1. Create Student Account
1. Login to teacher account
2. Go to "Manage Students"
3. Fill in student details and submit
4. Student can now login

#### 2. Upload Assignment
1. Go to "Assignments"
2. Add description
3. Select and upload file
4. Students can download it

#### 3. Create Puzzle
1. Go to "Puzzles"
2. Create a `.txt` file with puzzle content
3. Upload the file (filename without extension becomes the answer)
4. Example: Upload `solution.txt` → answer is `solution`
5. Add hint (optional)

### Student Workflow

#### 1. Edit Profile
1. Go to "My Profile"
2. Update email and phone
3. Upload avatar via:
   - Local file upload
   - Image URL

#### 2. Download Assignment
1. Go to "Assignments"
2. Click download button
3. View and work on assignment

#### 3. Solve Puzzle
1. Go to "Puzzles"
2. Read hint
3. Type answer (case-insensitive, lowercase)
4. If correct, view the puzzle content file

#### 4. Send Message
1. Go to "Messages" or "Users"
2. Select recipient
3. Type and send message
4. Edit or delete your messages

---

## Security Implementation

### SQL Injection Prevention
All database queries use prepared statements with PDO:
```php
$sql = "SELECT * FROM users WHERE username = ?";
$user = $db->fetchOne($sql, [$username]);
```

### Password Security
- Passwords hashed with bcrypt (PASSWORD_BCRYPT)
- Verification using `password_verify()`

### Session Management
- Session-based authentication
- User role checking before page access
- Automatic redirect to login if not authenticated

### Input Validation
- `sanitizeInput()` for text inputs
- `validateEmail()` for email verification
- File type validation for uploads

---

## Important Notes for PROG05

### Constraints Implemented
✅ Pure PHP (Procedural) - No frameworks
✅ Login system with session management
✅ Students cannot change username or fullname
✅ Two avatar upload methods (file + URL)
✅ Puzzle answer = filename (lowercase, no extension)
✅ Puzzle content displayed on correct answer
✅ SQL Injection prevention with prepared statements

### Key Features
- Clean and organized form-based UI
- Responsive design using CSS Grid
- Proper error handling and validation
- Message edit/delete functionality
- User listing and direct messaging

---

## Troubleshooting

### Database Connection Error
- Check `includes/db.php` credentials
- Ensure MySQL is running
- Verify database exists and tables are created

### File Upload Issues
- Check directory permissions (755)
- Verify PHP `upload_max_filesize` setting
- Ensure disk space is available

### Session Issues
- Clear browser cookies
- Check PHP `session.save_path` directory
- Verify write permissions for session directory

### Avatar Not Loading
- Check file path is correct
- Ensure image file exists in `uploads/avatars/`
- Try clearing browser cache

---

## API Reference

### Database Functions

```php
// Get singleton instance
$db = Database::getInstance();

// Execute query
$db->query($sql, $params);

// Fetch single row
$db->fetchOne($sql, $params);

// Fetch all rows
$db->fetchAll($sql, $params);

// Insert
$db->insert($sql, $params);

// Update
$db->update($sql, $params);

// Delete
$db->delete($sql, $params);
```

### Auth Functions

```php
// Login
$auth->login($username, $password);

// Register
$auth->register($username, $password, $fullname, $email, $phone, $role);

// Logout
$auth->logout();

// Check if logged in
Auth::isLoggedIn();

// Check role
Auth::hasRole('teacher');

// Get current user ID
Auth::getCurrentUserId();

// Get current user info
Auth::getCurrentUser();
```

### Helper Functions

```php
// File upload
uploadFile($file, $uploadDir, $allowedExtensions);

// Download from URL
downloadFileFromUrl($url, $savePath);

// Sanitize input
sanitizeInput($input);

// Validate email
validateEmail($email);

// Format date
formatDate($date, $format);

// Get user by ID
getUserById($userId);

// Get all users
getAllUsers($excludeId);
```

---

## Best Practices Used

1. **PDO Prepared Statements** - Prevents SQL Injection
2. **Password Hashing** - Bcrypt with PHP functions
3. **Session Management** - Secure cookie-based sessions
4. **Input Validation** - Server-side validation
5. **Output Escaping** - `htmlspecialchars()` for XSS prevention
6. **Modular Code** - Separate includes for db, auth, helpers
7. **Error Handling** - Try-catch blocks and error messages
8. **Responsive Design** - Mobile-friendly CSS Grid layout

---

## Limitations & Future Enhancements

### Current Limitations
- Single-file puzzle answers only
- No assignment submission tracking
- No grades/scoring system
- No backup/restore functionality

### Possible Enhancements
- Assignment submission upload
- Grading system for assignments
- Notifications system
- Message read receipts
- File preview functionality
- User search feature

---

## License

This is a student project for educational purposes.

## Author

Created as part of PROG05 Requirements

---

## Support

For issues or questions:
1. Check the Troubleshooting section
2. Review database queries in error messages
3. Ensure all files are in correct locations
4. Verify file permissions
