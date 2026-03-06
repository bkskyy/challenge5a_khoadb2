<?php
require_once '../includes/auth.php';
require_once '../includes/helpers.php';

requireTeacher();

$db = Database::getInstance();
$teacherId = Auth::getCurrentUserId();
$error = '';
$success = '';

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload') {
    if (!isset($_FILES['assignment_file'])) {
        $error = 'No file selected';
    } else {
        $description = sanitizeInput($_POST['description'] ?? '');
        $uploadDir = '../uploads/assignments';
        
        $result = uploadFile($_FILES['assignment_file'], $uploadDir);
        
        if ($result['success']) {
            $sql = "INSERT INTO assignments (teacher_id, description, file_path) VALUES (?, ?, ?)";
            $db->insert($sql, [$teacherId, $description, $result['path']]);
            
            $success = 'Assignment uploaded successfully';
        } else {
            $error = $result['message'];
        }
    }
}

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $assignmentId = $_POST['assignment_id'] ?? null;
    
    if (!$assignmentId) {
        $error = 'Invalid assignment';
    } else {
        $sql = "SELECT file_path FROM assignments WHERE id = ? AND teacher_id = ?";
        $assignment = $db->fetchOne($sql, [$assignmentId, $teacherId]);
        
        if ($assignment) {
            // Delete file
            if (file_exists($assignment['file_path'])) {
                unlink($assignment['file_path']);
            }
            
            // Delete from database
            $sql = "DELETE FROM assignments WHERE id = ?";
            $db->delete($sql, [$assignmentId]);
            
            $success = 'Assignment deleted successfully';
        } else {
            $error = 'Assignment not found';
        }
    }
}

// Get all assignments
$sql = "SELECT id, description, file_path, created_at FROM assignments WHERE teacher_id = ? ORDER BY created_at DESC";
$assignments = $db->fetchAll($sql, [$teacherId]);

// Count submissions for each assignment
$submissionCounts = [];
$sql = "SELECT assignment_id, COUNT(*) as count FROM submissions GROUP BY assignment_id";
$results = $db->fetchAll($sql);
foreach ($results as $result) {
    $submissionCounts[$result['assignment_id']] = $result['count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignments - Student Management System</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <?php $root_path = '../'; $page_title = 'Assignments'; include '../includes/header.php'; ?>
    
    <h1>Manage Assignments</h1>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <!-- Upload Form -->
    <div class="card">
        <h2>Upload New Assignment</h2>
        <form method="POST" enctype="multipart/form-data" class="form">
            <input type="hidden" name="action" value="upload">
            
            <div class="form-group">
                <label for="description">Description/Title:</label>
                <textarea id="description" name="description" rows="3"></textarea>
            </div>
            
            <div class="form-group">
                <label for="assignment_file">Select File:</label>
                <input type="file" id="assignment_file" name="assignment_file" required>
                <small>Upload any file type (PDF, DOCX, ZIP, etc.)</small>
            </div>
            
            <button type="submit" class="btn btn-primary">Upload Assignment</button>
        </form>
    </div>
    
    <!-- Assignments List -->
    <div class="card" style="margin-top: 30px;">
        <h2>Your Assignments</h2>
        
        <?php if ($assignments): ?>
            <div class="assignments-list">
                <?php foreach ($assignments as $assignment): ?>
                    <div class="assignment-item">
                        <div class="assignment-header">
                            <h3>
                                📎 <?php echo htmlspecialchars($assignment['description'] ?: 'Untitled Assignment'); ?>
                            </h3>
                            <span class="badge"><?php echo ($submissionCounts[$assignment['id']] ?? 0); ?> submissions</span>
                        </div>
                        
                        <div class="assignment-meta">
                            <p><strong>File:</strong> <?php echo htmlspecialchars(basename($assignment['file_path'])); ?></p>
                            <p><strong>Uploaded:</strong> <?php echo formatDate($assignment['created_at']); ?></p>
                        </div>
                        
                        <div class="assignment-actions">
                            <a href="<?php echo $assignment['file_path']; ?>" class="btn btn-small" download>Download</a>
                            <?php 
                            $sql = "SELECT id, student_id, submitted_at FROM submissions WHERE assignment_id = ?";
                            $submissions = $db->fetchAll($sql, [$assignment['id']]);
                            ?>
                            <?php if ($submissions): ?>
                                <button class="btn btn-small" onclick="viewSubmissions(<?php echo $assignment['id']; ?>, <?php echo htmlspecialchars(json_encode($submissions)); ?>)">View Submissions</button>
                            <?php else: ?>
                                <span style="color: #999;">No submissions yet</span>
                            <?php endif; ?>
                            
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="assignment_id" value="<?php echo $assignment['id']; ?>">
                                <button type="submit" class="btn btn-small btn-danger" onclick="return confirm('Delete assignment?');">Delete</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No assignments yet. Upload your first assignment!</p>
        <?php endif; ?>
    </div>
    
    <style>
        .assignment-item {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            background: #fafafa;
        }
        
        .assignment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .assignment-header h3 {
            margin: 0;
            flex: 1;
        }
        
        .badge {
            background: #2196F3;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            white-space: nowrap;
        }
        
        .assignment-meta {
            color: #666;
            margin-bottom: 15px;
        }
        
        .assignment-meta p {
            margin: 5px 0;
        }
        
        .assignment-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .assignments-list {
            margin-top: 15px;
        }
    </style>
    
    <script>
        function viewSubmissions(assignmentId, submissions) {
            alert('Submissions for this assignment:\n\n' + 
                  submissions.map(s => `Student ID: ${s.student_id}, Submitted: ${s.submitted_at}`).join('\n'));
        }
    </script>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>
