<?php
require_once '../includes/auth.php';
require_once '../includes/helpers.php';

requireTeacher();

$db = Database::getInstance();
$teacherId = Auth::getCurrentUserId();
$error = '';
$success = '';

// Handle puzzle upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload') {
    if (!isset($_FILES['puzzle_file'])) {
        $error = 'No file selected';
    } else {
        $hint = sanitizeInput($_POST['hint'] ?? '');
        $uploadDir = '../uploads/puzzles';
        
        // Upload the .txt file
        $result = uploadFile($_FILES['puzzle_file'], $uploadDir, ['txt']);
        
        if ($result['success']) {
            // Extract filename without extension as answer (lowercase)
            $answer = getFilenameWithoutExtension($result['filename']);
            
            $sql = "INSERT INTO puzzles (teacher_id, hint, file_content_path, answer) VALUES (?, ?, ?, ?)";
            $db->insert($sql, [$teacherId, $hint, $result['path'], $answer]);
            
            $success = 'Puzzle created successfully! Answer: ' . $answer;
        } else {
            $error = $result['message'];
        }
    }
}

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $puzzleId = $_POST['puzzle_id'] ?? null;
    
    if (!$puzzleId) {
        $error = 'Invalid puzzle';
    } else {
        $sql = "SELECT file_content_path FROM puzzles WHERE id = ? AND teacher_id = ?";
        $puzzle = $db->fetchOne($sql, [$puzzleId, $teacherId]);
        
        if ($puzzle) {
            // Delete file
            if (file_exists($puzzle['file_content_path'])) {
                unlink($puzzle['file_content_path']);
            }
            
            // Delete from database
            $sql = "DELETE FROM puzzles WHERE id = ?";
            $db->delete($sql, [$puzzleId]);
            
            $success = 'Puzzle deleted successfully';
        } else {
            $error = 'Puzzle not found';
        }
    }
}

// Get all puzzles
$sql = "SELECT id, hint, file_content_path, answer, created_at FROM puzzles WHERE teacher_id = ? ORDER BY created_at DESC";
$puzzles = $db->fetchAll($sql, [$teacherId]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Puzzles - Student Management System</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <?php $root_path = '../'; $page_title = 'Puzzles'; include '../includes/header.php'; ?>
    
    <h1>Manage Puzzles</h1>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <!-- Upload Form -->
    <div class="card">
        <h2>Create New Puzzle</h2>
        <form method="POST" enctype="multipart/form-data" class="form">
            <input type="hidden" name="action" value="upload">
            
            <div class="form-group">
                <label for="hint">Hint:</label>
                <textarea id="hint" name="hint" rows="3" placeholder="Give students a hint for solving the puzzle"></textarea>
            </div>
            
            <div class="form-group">
                <label for="puzzle_file">Select Puzzle File (.txt):</label>
                <input type="file" id="puzzle_file" name="puzzle_file" accept=".txt" required>
                <small style="display: block; margin-top: 10px;">
                    <strong>Important:</strong> Upload a .txt file. The filename (without extension, lowercase) will be the answer students need to submit.
                    <br>Example: If you upload "secretanswer.txt", students must type "secretanswer" to solve the puzzle.
                </small>
            </div>
            
            <button type="submit" class="btn btn-primary">Create Puzzle</button>
        </form>
    </div>
    
    <!-- Puzzles List -->
    <div class="card" style="margin-top: 30px;">
        <h2>Your Puzzles</h2>
        
        <?php if ($puzzles): ?>
            <div class="puzzles-list">
                <?php foreach ($puzzles as $puzzle): ?>
                    <div class="puzzle-item">
                        <h3>🧩 Puzzle (Answer: <code><?php echo htmlspecialchars($puzzle['answer']); ?></code>)</h3>
                        
                        <div class="puzzle-content">
                            <div>
                                <strong>Hint:</strong> 
                                <p><?php echo htmlspecialchars($puzzle['hint'] ?: 'No hint provided'); ?></p>
                            </div>
                            
                            <div>
                                <strong>File:</strong>
                                <p><?php echo htmlspecialchars(basename($puzzle['file_content_path'])); ?></p>
                            </div>
                            
                            <div>
                                <strong>Created:</strong>
                                <p><?php echo formatDate($puzzle['created_at']); ?></p>
                            </div>
                        </div>
                        
                        <div class="puzzle-actions">
                            <a href="<?php echo $puzzle['file_content_path']; ?>" class="btn btn-small" download>Download File</a>
                            <button class="btn btn-small" onclick="viewContent('<?php echo htmlspecialchars($puzzle['file_content_path']); ?>')">View Content</button>
                            
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="puzzle_id" value="<?php echo $puzzle['id']; ?>">
                                <button type="submit" class="btn btn-small btn-danger" onclick="return confirm('Delete puzzle?');">Delete</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No puzzles yet. Create your first puzzle!</p>
        <?php endif; ?>
    </div>
    
    <style>
        .puzzle-item {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            background: #fafafa;
        }
        
        .puzzle-item h3 {
            margin-top: 0;
        }
        
        .puzzle-content {
            margin: 15px 0;
        }
        
        .puzzle-content div {
            margin-bottom: 10px;
        }
        
        .puzzle-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 15px;
        }
        
        code {
            background: #f0f0f0;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: monospace;
        }
    </style>
    
    <script>
        function viewContent(filePath) {
            fetch(filePath)
                .then(response => response.text())
                .then(content => {
                    alert('Puzzle Content:\n\n' + content);
                })
                .catch(error => {
                    alert('Error reading file: ' + error);
                });
        }
    </script>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>
