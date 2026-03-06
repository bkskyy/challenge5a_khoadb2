# PROG05 - Quick Start Guide

## 🚀 Get Started in 5 Minutes

### Step 1: Database Setup

```bash
# Option A: Command Line
mysql -u root -p < database.sql

# Option B: PHPMyAdmin
- Import database.sql file via PHPMyAdmin interface
```

### Step 2: Configure Database

Edit `includes/db.php`:
```php
const DB_HOST = 'localhost';      // Your MySQL host
const DB_NAME = 'student_management_system';
const DB_USER = 'root';           // Your MySQL username
const DB_PASS = '';               // Your MySQL password
```

### Step 3: Set Permissions

```bash
# Make upload directories writable
chmod 755 uploads/
chmod 755 uploads/avatars/
chmod 755 uploads/assignments/
chmod 755 uploads/puzzles/
```

### Step 4: Create Demo Data

Visit: `http://localhost/PROG05/setup.php`
- Click "Create Demo Data" button
- This creates demo teacher and student accounts

### Step 5: Login!

Visit: `http://localhost/PROG05/login.php`

**Demo Credentials:**
- Teacher: `teacher` / `password123`
- Student: `student` / `password123`

---

## 📚 Quick Tour

### For Teachers

1. **Dashboard** → Overview of system
2. **Manage Students** → Add/Edit/Delete students
3. **Assignments** → Upload files for students
4. **Puzzles** → Create puzzle games
   - Upload a `.txt` file
   - Filename (lowercase, no ext) = answer
   - Example: Upload `hello.txt` → answer: `hello`

### For Students

1. **Dashboard** → Quick access to features
2. **My Profile** → Update email/phone/avatar
   - Upload image file
   - Or provide image URL
3. **Assignments** → Download from teachers
4. **Puzzles** → Solve puzzle challenges
5. **Messages** → Chat with others

---

## 🔐 Key Security Features

✅ **SQL Injection Protection** - Prepared statements with PDO
✅ **Password Security** - Bcrypt hashing (PASSWORD_BCRYPT)
✅ **Session Management** - Secure session-based auth
✅ **Input Validation** - Server-side validation on all inputs
✅ **XSS Protection** - htmlspecialchars() on all output

---

## 📁 Directory Structure

```
PROG05/
├── index.php               # Entry point (redirects to login/dashboard)
├── login.php               # Login & registration
├── logout.php              # Logout
├── dashboard.php           # Main dashboard
├── profile.php             # User profile & avatar
├── users.php               # All users listing
├── messages.php            # Messaging system
│
├── teacher/                # Teacher modules
│   ├── manage_students.php # Add/Edit/Delete students
│   ├── assignments.php     # Upload & manage assignments
│   └── puzzles.php         # Create puzzles
│
├── student/                # Student modules
│   ├── assignments.php     # View assignments
│   └── puzzles.php         # Solve puzzles
│
├── includes/               # Core files
│   ├── db.php              # Database connection (PDO)
│   ├── auth.php            # Authentication & auth checks
│   ├── helpers.php         # Helper functions
│   ├── header.php          # Navigation template
│   └── footer.php          # Footer template
│
├── assets/
│   └── style.css           # Complete CSS styling
│
├── uploads/                # User uploads
│   ├── avatars/            # User profile pictures
│   ├── assignments/        # Assignment files
│   └── puzzles/            # Puzzle content files
│
├── database.sql            # Database schema
├── setup.php               # One-time setup script
├── README.md               # Complete documentation
└── QUICKSTART.md           # This file
```

---

## ⚙️ Configuration

### Default Settings

All configurable settings are in `includes/db.php`:

```php
// Database connection
const DB_HOST = 'localhost';
const DB_NAME = 'student_management_system';
const DB_USER = 'root';
const DB_PASS = '';

// PDO options (auto-configured for security)
PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
PDO::ATTR_EMULATE_PREPARES => false,  // Prevent SQL injection
```

---

## 🧪 Testing

### Test as Teacher

1. Login with: `teacher` / `password123`
2. Add a new student
3. Upload an assignment
4. Create a puzzle:
   - Create file named `secret.txt`
   - Upload it
   - Answer will be `secret`

### Test as Student

1. Login with: `student` / `password123`
2. Update profile (email/phone)
3. Upload avatar (file or URL)
4. Download assignment from teacher
5. Solve puzzle (type `secret` if using example above)
6. Send message to teacher

---

## 🐛 Troubleshooting

| Problem | Solution |
|---------|----------|
| White blank page | Check error_log or PHP error reporting |
| Database error | Verify credentials in `includes/db.php` |
| Can't upload files | Check `uploads/` directory permissions (755) |
| Can't login | Check username/password (case-sensitive) |
| Avatar not showing | Verify file exists in `uploads/avatars/` |
| Puzzle answer wrong | Remember: lowercase, no file extension |

---

## 📝 Important Notes for PROG05

✅ **Pure PHP** - No frameworks (Laravel, CodeIgniter, etc.)
✅ **Procedural** - Object-oriented where beneficial, procedural functions for helpers
✅ **SQL Injection** - Prevented using PDO prepared statements
✅ **Students Cannot Edit**: Username and fullname locked in edit profile
✅ **Avatar Methods**: Both file upload and URL download implemented
✅ **Puzzle Logic**: Filename = answer, content shown on correct submission
✅ **Clean UI**: HTML/CSS form-based interface

---

## 🎯 Features Checklist

### Database ✅
- [x] users table with roles
- [x] messages table with edit tracking
- [x] assignments table with file paths
- [x] submissions table for tracking
- [x] puzzles table with filename-based answers

### Security ✅
- [x] PDO prepared statements
- [x] Password hashing (bcrypt)
- [x] Session-based authentication
- [x] Input validation and sanitization
- [x] XSS protection (htmlspecialchars)

### Teacher Features ✅
- [x] Create/Read/Update/Delete students
- [x] Upload assignments
- [x] Create puzzles with file content

### Student Features ✅
- [x] Edit email/phone/avatar
- [x] Avatar upload (file)
- [x] Avatar upload (URL)
- [x] Download assignments
- [x] Solve puzzles
- [x] Cannot edit username/fullname

### Messaging ✅
- [x] User list
- [x] Send messages
- [x] Edit own messages
- [x] Delete own messages

### UI/UX ✅
- [x] Clean navigation bar
- [x] Responsive design
- [x] Dashboard with cards
- [x] Professional styling

---

## 📞 Support

For detailed information, see `README.md`

For setup issues, see database.sql and includes/db.php

---

**Happy coding! 🎉**
