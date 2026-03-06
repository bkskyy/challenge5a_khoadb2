# 📋 PROG05 - Complete File Reference Guide

## All Files & Their Purposes

### 🔵 Core Entry Points (Root Directory)

#### `index.php` (Redirector)
- **Purpose:** Smart redirector based on login status
- **Redirects:** To `login.php` if not logged in, `dashboard.php` if logged in
- **Lines:** 10

#### `login.php` (Authentication)
- **Purpose:** Login and registration page
- **Features:** Tab-based form, demo credentials display
- **Key Elements:** Login form, Registration form with validation
- **Lines:** 190

#### `logout.php` (Session Terminator)
- **Purpose:** Destroy session and redirect to login
- **Lines:** 5

#### `dashboard.php` (Main Hub)
- **Purpose:** Role-based main dashboard
- **Teacher View:** Students, Assignments, Puzzles cards
- **Student View:** Assignments, Puzzles, Profile cards
- **Lines:** 85

#### `profile.php` (User Profile Management)
- **Purpose:** Edit profile and manage avatar
- **Features:** Email/phone updates, Avatar upload, Avatar from URL
- **Key Functions:** uploadFile(), downloadFileFromUrl()
- **Lines:** 165

#### `users.php` (User Directory)
- **Purpose:** Display all users with messaging link
- **Features:** User cards, role badges, avatars
- **Lines:** 85

#### `messages.php` (Messaging System)
- **Purpose:** Two-way messaging between users
- **Features:** Conversation list, Chat area, Edit/Delete messages
- **Layout:** 25% users list, 75% chat area
- **Lines:** 300

#### `setup.php` (Installation Script)
- **Purpose:** One-time setup to create demo accounts
- **Creates:** Teacher (teacher/password123) and Student (student/password123)
- **Security:** Should be deleted after use
- **Lines:** 120

---

### 🟠 Core Logic Files (includes/ Directory)

#### `includes/db.php` (Database Layer)
- **Purpose:** Database connection and query execution
- **Class:** Database (Singleton pattern)
- **Methods:**
  - `getInstance()` - Get singleton instance
  - `query($sql, $params)` - Execute prepared statement
  - `fetchOne($sql, $params)` - Get single row
  - `fetchAll($sql, $params)` - Get multiple rows
  - `insert($sql, $params)` - Insert and return ID
  - `update($sql, $params)` - Update records
  - `delete($sql, $params)` - Delete records
- **Security:** PDO Prepared statements, error handling
- **Lines:** 120

#### `includes/auth.php` (Authentication)
- **Purpose:** User authentication and authorization
- **Class:** Auth
- **Methods:**
  - `login($username, $password)` - Authenticate user
  - `register($username, $password, ...)` - Create new user
  - `logout()` - Destroy session
  - `isLoggedIn()` - Check if user auth'd
  - `hasRole($role)` - Check role
  - `getCurrentUserId()` - Get user ID
  - `getCurrentUser()` - Get user data
- **Functions:**
  - `requireLogin()` - Force authentication
  - `requireTeacher()` - Force teacher role
  - `requireStudent()` - Force student role
- **Lines:** 150

#### `includes/helpers.php` (Utility Functions)
- **Purpose:** Reusable helper functions throughout app
- **File Functions:**
  - `uploadFile()` - Handle file uploads with validation
  - `downloadFileFromUrl()` - Download & save from URL
  - `getFileExtension()` - Extract file extension
  - `getFilenameWithoutExtension()` - Extract filename for puzzles
  - `fileExists()` - Check file exists
  - `deleteFile()` - Delete file
- **Validation Functions:**
  - `sanitizeInput()` - Clean user input
  - `validateEmail()` - Check email format
- **Utility Functions:**
  - `generateToken()` - Create random token
  - `formatDate()` - Format dates for display
  - `timeAgo()` - Relative time display
  - `redirect()` - Send redirect header
- **Database Functions:**
  - `getUserById()` - Get user information
  - `getAllUsers()` - Get all users list
  - `getUnreadMessageCount()` - Count unread messages
- **Lines:** 200

#### `includes/header.php` (Navigation Template)
- **Purpose:** Navigation bar and page header
- **Elements:**
  - Logo/brand link
  - Role-based navigation menu
  - Responsive navbar
  - Logout button
- **Used By:** All pages via `include`
- **Lines:** 65

#### `includes/footer.php` (Page Footer)
- **Purpose:** Close containers and display footer
- **Elements:** Copyright, closing tags
- **Used By:** All pages via `include`
- **Lines:** 5

---

### 🟢 Teacher Module (teacher/ Directory)

#### `teacher/manage_students.php` (Student CRUD)
- **Purpose:** Add, Edit, Delete student accounts
- **Features:**
  - Form to add new students
  - Table listing all students
  - Edit modal dialog
  - Delete confirmation
- **Database Operations:** INSERT, UPDATE, DELETE on users
- **Lines:** 220

#### `teacher/assignments.php` (Assignment Management)
- **Purpose:** Upload and manage assignment files
- **Features:**
  - File upload form
  - Assignment cards with submission count
  - Download assignment links
  - Delete buttons
- **Database:** INSERT/SELECT/DELETE from assignments
- **File Operations:** Upload to uploads/assignments/
- **Lines:** 180

#### `teacher/puzzles.php` (Puzzle Creator)
- **Purpose:** Create puzzle games
- **Key Logic:**
  - Upload .txt file
  - Extract filename (lowercase, no extension) as answer
  - Store hint in database
  - Store file path in database
- **Example:** Upload "solution.txt" → answer = "solution"
- **Database:** INSERT/SELECT/DELETE from puzzles
- **Lines:** 180

---

### 🔵 Student Module (student/ Directory)

#### `student/assignments.php` (View Assignments)
- **Purpose:** Display available assignments
- **Features:**
  - Grid layout of assignment cards
  - Teacher name for each assignment
  - Download buttons
  - Submission status badges
- **Database:** SELECT from assignments
- **Lines:** 110

#### `student/puzzles.php` (Puzzle Game)
- **Purpose:** Play and solve puzzles
- **Key Logic:**
  1. Student submits answer
  2. Compare to stored answer (both lowercase)
  3. If correct: use file_get_contents() to read puzzle content
  4. Display content to student
  5. If wrong: show error message
- **Features:**
  - Hint display
  - Answer input form
  - Content display on correct answer
- **Database:** SELECT from puzzles
- **File Operations:** file_get_contents() on puzzle files
- **Lines:** 160

---

### 🎨 Assets (assets/ Directory)

#### `assets/style.css` (Stylesheet)
- **Purpose:** Complete application styling
- **Sections:**
  - Global styles & reset
  - CSS variables (colors, shadows)
  - Navbar styling
  - Container & layout
  - Forms & inputs
  - Buttons & states
  - Alerts & notifications
  - Cards & tables
  - Auth pages
  - Profile page
  - Users page
  - Footer
  - Responsive media queries
  - Utility classes
- **Features:**
  - Grid-based layouts
  - Flexbox components
  - Smooth transitions
  - responsive breakpoints
- **Lines:** 600+

---

### 📊 Database (Root Directory)

#### `database.sql` (Database Schema)
- **Purpose:** SQL script to create entire database structure
- **Tables Created:**

1. **users** (7 columns)
   - id, username, password, fullname
   - email, phone, avatar, role
   - Indexes: role

2. **messages** (6 columns)
   - id, sender_id, receiver_id, content
   - is_edited, created_at, updated_at
   - Indexes: sender/receiver, created_at

3. **assignments** (5 columns)
   - id, teacher_id, description, file_path
   - created_at, updated_at
   - Indexes: teacher_id

4. **submissions** (6 columns)
   - id, assignment_id, student_id, file_path
   - submitted_at, updated_at
   - Unique: assignment_id + student_id

5. **puzzles** (6 columns)
   - id, teacher_id, hint, file_content_path
   - answer, created_at, updated_at
   - Indexes: teacher_id

- **Lines:** 80

---

### 📚 Documentation (Root Directory)

#### `README.md` (Main Documentation)
- **Sections:**
  - Overview of system
  - Feature list
  - Installation steps
  - Database schema details
  - API reference
  - Best practices
  - Support information
- **Length:** 400+ lines

#### `QUICKSTART.md` (5-Minute Setup)
- **Contains:**
  - Quick setup in 5 steps
  - Demo credentials
  - Quick tour for teachers/students
  - Troubleshooting common issues
  - Directory structure
- **Target:** Someone who wants fast setup
- **Length:** 200+ lines

#### `INSTALLATION.md` (Detailed Installation)
- **Contains:**
  - Step-by-step setup
  - Prerequisites check
  - Database creation options
  - Permission configuration
  - Troubleshooting section
  - Environment-specific setup
  - Docker deployment
  - Backup & recovery
  - Security hardening
- **Target:** Deployment professionals
- **Length:** 500+ lines

#### `PROJECT_OVERVIEW.md` (This Project)
- **Contains:**
  - Executive summary
  - Requirements checklist
  - Feature list with statuses
  - Complete file structure
  - Security implementation
  - UI/UX features
  - Getting started
  - Code quality notes
  - Testing procedures
- **Target:** Project stakeholders
- **Length:** 400+ lines

#### `FILE_REFERENCE.md` (File Guide)
- **Purpose:** Detailed documentation of all files
- **Contains:** This current file
- **Target:** Developers understanding codebase

---

### 📁 Upload Directories (Created at Runtime)

#### `uploads/avatars/`
- **Purpose:** Store user profile pictures
- **Contents:** JPG, PNG, GIF files
- **Naming:** `uniqid_timestamp.extension`
- **Size:** Varies (image files)

#### `uploads/assignments/`
- **Purpose:** Store teacher assignment files
- **Contents:** Any file type (PDF, DOCX, ZIP, etc.)
- **Naming:** `uniqid_timestamp.extension`
- **Size:** Variable

#### `uploads/puzzles/`
- **Purpose:** Store puzzle content files (.txt)
- **Contents:** Text files with puzzle content
- **Naming:** `uniqid_timestamp.txt`
- **Size:** Usually small (text)

---

## 📊 File Statistics

```
Total Files Created: 23
├── PHP Files: 16
│   ├── Root: 8 (index, login, logout, dashboard, profile, users, messages, setup)
│   ├── includes/: 5 (db, auth, helpers, header, footer)
│   ├── teacher/: 3 (manage_students, assignments, puzzles)
│   └── student/: 2 (assignments, puzzles)
├── CSS Files: 1 (style.css)
├── SQL Files: 1 (database.sql)
└── Documentation: 5 (README, QUICKSTART, INSTALLATION, PROJECT_OVERVIEW, FILE_REFERENCE)

Total Lines of Code: 3,000+
├── PHP Code: 2,200+
├── CSS Code: 600+
└── SQL Code: 80+
└── Documentation: 2,000+ lines

File Size Summary:
├── PHP Files: ~70 KB
├── CSS Files: ~15 KB
├── SQL Files: ~3 KB
└── Documentation: ~50 KB
```

---

## 🔗 File Dependencies

### Dependency Chain

```
login.php
├── includes/db.php
├── includes/auth.php
└── assets/style.css

dashboard.php
├── includes/auth.php ✓ (requires login check)
├── includes/helpers.php
├── includes/header.php
├── assets/style.css
└── includes/footer.php

teacher/manage_students.php
├── includes/auth.php (requires teacher role)
├── includes/helpers.php
├── includes/db.php
├── includes/header.php
└── assets/style.css

student/puzzles.php
├── includes/auth.php (requires student role)
├── includes/helpers.php
├── includes/db.php
├── Requires: uploads/puzzles/*.txt (puzzle files)
└── file_get_contents() for puzzle content
```

---

## 🎯 Which File to Edit For...

| Need | File(s) | Section |
|------|---------|---------|
| Change database credentials | `includes/db.php` | Lines 13-17 |
| Add new user role | `includes/auth.php` | Add role check |
| Add new user feature | Create in `includes/helpers.php` | New function |
| Change styling | `assets/style.css` | Appropriate section |
| Modify registration form | `login.php` | Register tab (~line 120) |
| Add teacher feature | `teacher/*.php` | Appropriate file |
| Add student feature | `student/*.php` | Appropriate file |
| Fix bug in auth | `includes/auth.php` | Appropriate method |
| Add new page | Create in root or appropriate folder | Copy header/footer pattern |
| Change navbar | `includes/header.php` | Nav menu section |
| Add helper function | `includes/helpers.php` | End of file |

---

## 📋 Code Organization Best Practices Used

### Single Responsibility Principle
- `db.php` - Only database operations
- `auth.php` - Only authentication/authorization
- `helpers.php` - Only utility functions

### Don't Repeat Yourself (DRY)
- Common functions in `helpers.php`
- Reusable templates in `includes/header.php` and `includes/footer.php`
- Shared CSS in `assets/style.css`

### Configuration Centralization
- Database config: `includes/db.php`
- Constants: Could add `includes/config.php`

### Security First
- All SQL queries prepared in `db.php`
- All input sanitized in `helpers.php`
- All output escaped in templates

---

## 🚀 Quick Navigation

### To Add a New Feature
1. Create file in appropriate directory
2. Include `includes/header.php` at top
3. Include `includes/footer.php` at bottom
4. Use functions from `includes/helpers.php`
5. Use `$db` from `includes/db.php` for queries
6. Add navigation link in `includes/header.php`

### To Fix a Bug
1. Identify which file(s) involved
2. Check database queries in `includes/db.php`
3. Check business logic in that module
4. Check user input in that form
5. Check user output in that template

### To Deploy
1. Update `includes/db.php` credentials
2. Run `database.sql` to create tables
3. Set permissions on `uploads/` directories
4. Visit `setup.php` to create demo accounts
5. Delete `setup.php` for security
6. Access `index.php` or `login.php`

---

## ✅ Verification Checklist

- [x] All 5 database tables created with schema
- [x] Authentication system with session management
- [x] Teacher module with full CRUD for students
- [x] Teacher can upload assignments
- [x] Teacher can create puzzles with filename = answer
- [x] Student can edit profile (except username/fullname)
- [x] Student can upload avatar and use URL
- [x] Student can download assignments
- [x] Student can solve puzzles and see content
- [x] Messaging system with edit/delete
- [x] User listing and directory
- [x] CSS styling for clean UI
- [x] SQL Injection prevention
- [x] Complete documentation
- [x] Demo setup script

---

**All files created and organized for maximum clarity and maintainability!** 🎉
