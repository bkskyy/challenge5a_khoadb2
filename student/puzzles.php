<?php
require_once '../includes/auth.php';
require_once '../includes/helpers.php';

requireStudent();

$db = Database::getInstance();
$studentId = Auth::getCurrentUserId();
$error = '';
$success = '';

// Handle puzzle answer submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $puzzleId = $_POST['puzzle_id'] ?? null;
    $answer = strtolower(trim(sanitizeInput($_POST['answer'] ?? '')));
    
    if (!$puzzleId || !$answer) {
        $error = 'Please provide an answer';
    } else {
        // Get the puzzle
        $sql = "SELECT answer, file_content_path FROM puzzles WHERE id = ?";
        $puzzle = $db->fetchOne($sql, [$puzzleId]);
        
        if (!$puzzle) {
            $error = 'Puzzle not found';
        } else {
            // Check if answer is correct
            if ($answer === $puzzle['answer']) {
                // Load and display the file content
                if (file_exists($puzzle['file_content_path'])) {
                    $fileContent = file_get_contents($puzzle['file_content_path']);
                    $success = 'Correct answer! Here is the puzzle content:';
                    $_SESSION['puzzle_answer_content'] = $fileContent;
                } else {
                    $error = 'Puzzle file not found';
                }
            } else {
                $error = 'Incorrect answer. Try again!';
            }
        }
    }
}

// Get all puzzles
$sql = "SELECT id, teacher_id, hint, answer, created_at FROM puzzles ORDER BY created_at DESC";
$puzzles = $db->fetchAll($sql);
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
    
    <h1>Solve Puzzles</h1>
    
    <?php if ($puzzles): ?>
        <div class="puzzles-container">
            <?php foreach ($puzzles as $puzzle): ?>
                <div class="puzzle-card">
                    <h2>🧩 Puzzle</h2>
                    
                    <div class="puzzle-info">
                        <strong>Teacher:</strong> 
                        <?php 
                        $teacher = getUserById($puzzle['teacher_id']);
                        echo htmlspecialchars($teacher['fullname'] ?? 'Unknown');
                        ?>
                        <br><strong>Released:</strong> <?php echo formatDate($puzzle['created_at']); ?>
                    </div>
                    
                    <div class="puzzle-content">
                        <strong>Hint:</strong>
                        <p class="hint"><?php echo nl2br(htmlspecialchars($puzzle['hint'] ?: 'No hint provided')); ?></p>
                    </div>
                    
                    <form method="POST" class="puzzle-form">
                        <input type="hidden" name="puzzle_id" value="<?php echo $puzzle['id']; ?>">
                        
                        <div class="form-group">
                            <label for="answer_<?php echo $puzzle['id']; ?>">Your Answer:</label>
                            <input type="text" 
                                   id="answer_<?php echo $puzzle['id']; ?>" 
                                   name="answer" 
                                   placeholder="Type your answer here"
                                   required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Submit Answer</button>
                    </form>
                    
                    <?php if ($success && $_SESSION['puzzle_answer_content'] ?? false): ?>
                        <div class="alert alert-success">
                            <?php echo $success; ?>
                            <div class="puzzle-answer-content">
                                <pre><?php echo htmlspecialchars($_SESSION['puzzle_answer_content']); ?></pre>
                                <?php unset($_SESSION['puzzle_answer_content']); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-error"><?php echo $error; ?></div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <p>No puzzles available yet. Check back later!</p>
        </div>
    <?php endif; ?>
    
    <style>
        .puzzles-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .puzzle-card {
            border: 2px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .puzzle-card h2 {
            margin-top: 0;
            color: #333;
        }
        
        .puzzle-info {
            background: #f5f5f5;
            padding: 10px;
            border-radius: 4px;
            font-size: 13px;
            margin-bottom: 15px;
        }
        
        .puzzle-content {
            margin: 15px 0;
        }
        
        .hint {
            background: #fff9e6;
            padding: 10px;
            border-left: 4px solid #ffab00;
            margin: 10px 0 0 0;
            border-radius: 4px;
            font-style: italic;
        }
        
        .puzzle-form {
            margin-top: 15px;
        }
        
        .puzzle-form .form-group {
            margin-bottom: 10px;
        }
        
        .puzzle-form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .puzzle-form input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .puzzle-form button {
            width: 100%;
        }
        
        .puzzle-answer-content {
            background: #f5f5f5;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 15px;
            margin-top: 15px;
            overflow-x: auto;
        }
        
        .puzzle-answer-content pre {
            margin: 0;
            font-size: 12px;
            font-family: 'Courier New', monospace;
            max-height: 300px;
            overflow-y: auto;
        }
    </style>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>
