<?php
require_once '../includes/auth.php';
require_once '../includes/helpers.php';

requireStudent();

$db = Database::getInstance();
$studentId = Auth::getCurrentUserId();

// Get all assignments from teachers
$sql = "SELECT id, teacher_id, description, file_path, created_at FROM assignments ORDER BY created_at DESC";
$assignments = $db->fetchAll($sql);

// Get which assignments current student has submitted
$submittedAssignmentIds = [];
$sql = "SELECT assignment_id FROM submissions WHERE student_id = ?";
$results = $db->fetchAll($sql, [$studentId]);
foreach ($results as $result) {
    $submittedAssignmentIds[] = $result['assignment_id'];
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
    
    <h1>Assignments</h1>
    
    <?php if ($assignments): ?>
        <div class="assignments-list">
            <?php foreach ($assignments as $assignment): ?>
                <div class="assignment-card">
                    <div class="assignment-header">
                        <h3>📎 <?php echo htmlspecialchars($assignment['description'] ?: 'Untitled Assignment'); ?></h3>
                        <?php if (in_array($assignment['id'], $submittedAssignmentIds)): ?>
                            <span class="badge badge-success">✓ Submitted</span>
                        <?php else: ?>
                            <span class="badge badge-info">Pending</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="assignment-info">
                        <p><strong>Teacher:</strong> 
                            <?php 
                            $teacher = getUserById($assignment['teacher_id']);
                            echo htmlspecialchars($teacher['fullname'] ?? 'Unknown');
                            ?>
                        </p>
                        <p><strong>Released:</strong> <?php echo formatDate($assignment['created_at']); ?></p>
                    </div>
                    
                    <div class="assignment-actions">
                        <a href="<?php echo $assignment['file_path']; ?>" class="btn btn-primary" download>Download Assignment</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <p>No assignments available yet. Check back later!</p>
        </div>
    <?php endif; ?>
    
    <style>
        .assignments-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .assignment-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        
        .assignment-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .assignment-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
            gap: 10px;
        }
        
        .assignment-header h3 {
            margin: 0;
            flex: 1;
        }
        
        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            white-space: nowrap;
            font-weight: bold;
        }
        
        .badge-success {
            background: #4caf50;
            color: white;
        }
        
        .badge-info {
            background: #2196f3;
            color: white;
        }
        
        .assignment-info {
            margin: 12px 0;
            font-size: 14px;
            color: #555;
        }
        
        .assignment-info p {
            margin: 5px 0;
        }
        
        .assignment-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
    </style>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>
