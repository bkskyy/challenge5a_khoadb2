<?php
require_once '../includes/auth.php';
require_once '../includes/helpers.php';

requireTeacher();

$db = Database::getInstance();
$error = '';
$success = '';

// Handle add student
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $fullname = sanitizeInput($_POST['fullname'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $phone = sanitizeInput($_POST['phone'] ?? '');
    
    if (!$username || !$password || !$fullname || !$email) {
        $error = 'Please fill in all required fields';
    } elseif (!validateEmail($email)) {
        $error = 'Invalid email address';
    } else {
        $result = $auth->register($username, $password, $fullname, $email, $phone, 'student');
        if ($result['success']) {
            $success = 'Student added successfully';
        } else {
            $error = $result['message'];
        }
    }
}

// Handle edit student
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $studentId = $_POST['student_id'] ?? null;
    $email = sanitizeInput($_POST['email'] ?? '');
    $phone = sanitizeInput($_POST['phone'] ?? '');
    
    if (!$studentId) {
        $error = 'Invalid student';
    } elseif (!validateEmail($email)) {
        $error = 'Invalid email address';
    } else {
        $sql = "SELECT id FROM users WHERE email = ? AND id != ?";
        if ($db->fetchOne($sql, [$email, $studentId])) {
            $error = 'Email already taken';
        } else {
            $sql = "UPDATE users SET email = ?, phone = ? WHERE id = ? AND role = 'student'";
            $db->update($sql, [$email, $phone, $studentId]);
            $success = 'Student updated successfully';
        }
    }
}

// Handle delete student
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $studentId = $_POST['student_id'] ?? null;
    
    if (!$studentId) {
        $error = 'Invalid student';
    } else {
        $sql = "DELETE FROM users WHERE id = ? AND role = 'student'";
        $db->delete($sql, [$studentId]);
        $success = 'Student deleted successfully';
    }
}

// Get all students
$sql = "SELECT id, username, fullname, email, phone, created_at FROM users WHERE role = 'student' ORDER BY fullname ASC";
$students = $db->fetchAll($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students - Student Management System</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <?php $root_path = '../'; $page_title = 'Manage Students'; include '../includes/header.php'; ?>
    
    <h1>Manage Students</h1>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <!-- Add Student Form -->
    <div class="card">
        <h2>Add New Student</h2>
        <form method="POST" class="form">
            <input type="hidden" name="action" value="add">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="fullname">Full Name:</label>
                    <input type="text" id="fullname" name="fullname" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="phone">Phone (optional):</label>
                <input type="tel" id="phone" name="phone">
            </div>
            
            <button type="submit" class="btn btn-primary">Add Student</button>
        </form>
    </div>
    
    <!-- Students List -->
    <div class="card" style="margin-top: 30px;">
        <h2>Students List</h2>
        
        <?php if ($students): ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td><?php echo $student['id']; ?></td>
                                <td><?php echo htmlspecialchars($student['username']); ?></td>
                                <td><?php echo htmlspecialchars($student['fullname']); ?></td>
                                <td><?php echo htmlspecialchars($student['email']); ?></td>
                                <td><?php echo htmlspecialchars($student['phone'] ?? '-'); ?></td>
                                <td><?php echo formatDate($student['created_at'], 'd M Y'); ?></td>
                                <td>
                                    <button class="btn btn-small" onclick="editStudent(<?php echo htmlspecialchars(json_encode($student)); ?>)">Edit</button>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">
                                        <button type="submit" class="btn btn-small btn-danger" onclick="return confirm('Delete student?');">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No students found</p>
        <?php endif; ?>
    </div>
    
    <div id="editModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="modal-close" onclick="closeModal()">&times;</span>
            <h2>Edit Student</h2>
            <form method="POST" class="form">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" id="editStudentId" name="student_id">
                
                <div class="form-group">
                    <label>Username:</label>
                    <input type="text" id="editUsername" disabled>
                </div>
                
                <div class="form-group">
                    <label>Full Name:</label>
                    <input type="text" id="editFullname" disabled>
                </div>
                
                <div class="form-group">
                    <label for="editEmail">Email:</label>
                    <input type="email" id="editEmail" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="editPhone">Phone:</label>
                    <input type="tel" id="editPhone" name="phone">
                </div>
                
                <button type="submit" class="btn btn-primary">Update Student</button>
            </form>
        </div>
    </div>
    
    <script>
        function editStudent(student) {
            document.getElementById('editStudentId').value = student.id;
            document.getElementById('editUsername').value = student.username;
            document.getElementById('editFullname').value = student.fullname;
            document.getElementById('editEmail').value = student.email;
            document.getElementById('editPhone').value = student.phone || '';
            document.getElementById('editModal').style.display = 'flex';
        }
        
        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }
        
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }
    </script>
    
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            align-items: center;
            justify-content: center;
        }
        
        .modal-content {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            position: relative;
        }
        
        .modal-close {
            position: absolute;
            right: 15px;
            top: 15px;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>
