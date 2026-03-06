<?php
require_once 'includes/auth.php';
require_once 'includes/helpers.php';

requireLogin();

$db = Database::getInstance();
$currentUser = Auth::getCurrentUser();
$currentUserId = Auth::getCurrentUserId();

$selectedUserId = $_GET['user_id'] ?? null;
$selectedUser = null;
$messages = [];
$error = '';
$success = '';

// Handle message send
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action === 'send') {
        $receiverId = $_POST['receiver_id'] ?? null;
        $content = sanitizeInput($_POST['content'] ?? '');
        
        if (!$receiverId || !$content) {
            $error = 'Please select a user and type a message';
        } else {
            $sql = "INSERT INTO messages (sender_id, receiver_id, content) VALUES (?, ?, ?)";
            $db->insert($sql, [$currentUserId, $receiverId, $content]);
            $success = 'Message sent successfully';
            $selectedUserId = $receiverId;
        }
    }
    elseif ($action === 'edit') {
        $messageId = $_POST['message_id'] ?? null;
        $content = sanitizeInput($_POST['content'] ?? '');
        
        if (!$messageId || !$content) {
            $error = 'Invalid message or content';
        } else {
            // Check if current user is the sender
            $sql = "SELECT sender_id FROM messages WHERE id = ?";
            $msg = $db->fetchOne($sql, [$messageId]);
            
            if ($msg && $msg['sender_id'] == $currentUserId) {
                $sql = "UPDATE messages SET content = ?, is_edited = 1 WHERE id = ?";
                $db->update($sql, [$content, $messageId]);
                $success = 'Message updated successfully';
            } else {
                $error = 'You can only edit your own messages';
            }
        }
    }
    elseif ($action === 'delete') {
        $messageId = $_POST['message_id'] ?? null;
        
        if (!$messageId) {
            $error = 'Invalid message';
        } else {
            // Check if current user is the sender
            $sql = "SELECT sender_id FROM messages WHERE id = ?";
            $msg = $db->fetchOne($sql, [$messageId]);
            
            if ($msg && $msg['sender_id'] == $currentUserId) {
                $sql = "DELETE FROM messages WHERE id = ?";
                $db->delete($sql, [$messageId]);
                $success = 'Message deleted successfully';
            } else {
                $error = 'You can only delete your own messages';
            }
        }
    }
}

// Get selected user details
if ($selectedUserId) {
    $sql = "SELECT id, username, fullname, avatar, role FROM users WHERE id = ? AND id != ?";
    $selectedUser = $db->fetchOne($sql, [$selectedUserId, $currentUserId]);
    
    if ($selectedUser) {
        // Get conversation messages
        $sql = "SELECT * FROM messages 
                WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)
                ORDER BY created_at ASC";
        $messages = $db->fetchAll($sql, [$currentUserId, $selectedUserId, $selectedUserId, $currentUserId]);
    }
}

// Get list of users for conversation
$sql = "SELECT id, username, fullname, avatar FROM users WHERE id != ? ORDER BY fullname ASC";
$conversationUsers = $db->fetchAll($sql, [$currentUserId]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Student Management System</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .messages-container {
            display: flex;
            height: calc(100vh - 200px);
            gap: 20px;
            margin: 20px 0;
        }
        
        .users-list {
            width: 25%;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow-y: auto;
        }
        
        .user-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .user-item:hover {
            background-color: #f5f5f5;
        }
        
        .user-item.active {
            background-color: #e3f2fd;
            border-left: 4px solid #2196F3;
        }
        
        .chat-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #fff;
        }
        
        .chat-header {
            padding: 15px;
            border-bottom: 1px solid #eee;
            background: #f9f9f9;
        }
        
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }
        
        .message {
            display: flex;
            margin-bottom: 15px;
            align-items: flex-start;
        }
        
        .message.sent {
            justify-content: flex-end;
        }
        
        .message-content {
            max-width: 60%;
            padding: 10px 15px;
            border-radius: 8px;
            word-wrap: break-word;
        }
        
        .message.received .message-content {
            background-color: #e3f2fd;
            border: 1px solid #90caf9;
        }
        
        .message.sent .message-content {
            background-color: #c8e6c9;
            border: 1px solid #81c784;
        }
        
        .message-meta {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        
        .message-actions {
            display: flex;
            gap: 5px;
            margin-top: 5px;
        }
        
        .message-actions button {
            font-size: 11px;
            padding: 4px 8px;
        }
        
        .chat-input {
            padding: 15px;
            border-top: 1px solid #eee;
            display: flex;
            gap: 10px;
        }
        
        .chat-input textarea {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            resize: vertical;
            min-height: 50px;
        }
        
        .chat-input button {
            padding: 10px 20px;
        }
        
        .no-chat {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #999;
            text-align: center;
        }
        
        @media (max-width: 768px) {
            .messages-container {
                flex-direction: column;
                height: auto;
            }
            
            .users-list {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php $root_path = ''; $page_title = 'Messages'; include 'includes/header.php'; ?>
    
    <h1>Messages</h1>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <div class="messages-container">
        <!-- Users List -->
        <div class="users-list">
            <h3 style="padding: 15px; border-bottom: 1px solid #eee;">Conversations</h3>
            <?php foreach ($conversationUsers as $user): ?>
                <div class="user-item <?php echo ($selectedUserId == $user['id']) ? 'active' : ''; ?>" 
                     onclick="window.location.href='?user_id=<?php echo $user['id']; ?>'">
                    <strong><?php echo htmlspecialchars($user['fullname']); ?></strong>
                    <p style="margin: 5px 0; font-size: 12px; color: #666;">@<?php echo htmlspecialchars($user['username']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Chat Area -->
        <div class="chat-area">
            <?php if ($selectedUser): ?>
                <!-- Chat Header -->
                <div class="chat-header">
                    <h3><?php echo htmlspecialchars($selectedUser['fullname']); ?></h3>
                    <p>@<?php echo htmlspecialchars($selectedUser['username']); ?> (<?php echo ucfirst($selectedUser['role']); ?>)</p>
                </div>
                
                <!-- Messages -->
                <div class="chat-messages">
                    <?php if ($messages): ?>
                        <?php foreach ($messages as $message): ?>
                            <div class="message <?php echo ($message['sender_id'] == $currentUserId) ? 'sent' : 'received'; ?>">
                                <div>
                                    <div class="message-content">
                                        <?php echo htmlspecialchars($message['content']); ?>
                                        <?php if ($message['is_edited']): ?>
                                            <span style="font-size: 11px; color: #999;"> (edited)</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="message-meta">
                                        <?php echo formatDate($message['created_at'], 'd M H:i'); ?>
                                    </div>
                                    
                                    <?php if ($message['sender_id'] == $currentUserId): ?>
                                        <div class="message-actions">
                                            <button class="btn btn-small" onclick="editMessage(<?php echo $message['id']; ?>, '<?php echo htmlspecialchars($message['content']); ?>')">Edit</button>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                                <button type="submit" class="btn btn-small" onclick="return confirm('Delete message?');">Delete</button>
                                            </form>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-chat">
                            <p>No messages yet. Start a conversation!</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Input Area -->
                <form method="POST" class="chat-input">
                    <input type="hidden" name="action" value="send">
                    <input type="hidden" name="receiver_id" value="<?php echo $selectedUser['id']; ?>">
                    <textarea name="content" placeholder="Type your message..." required></textarea>
                    <button type="submit" class="btn btn-primary">Send</button>
                </form>
            <?php else: ?>
                <div class="no-chat">
                    <p>Select a user to start messaging</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        function editMessage(messageId, currentContent) {
            const newContent = prompt('Edit message:', currentContent);
            if (newContent !== null && newContent.trim()) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="message_id" value="${messageId}">
                    <input type="hidden" name="content" value="${newContent}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        // Auto-scroll to bottom
        const chatMessages = document.querySelector('.chat-messages');
        if (chatMessages) {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    </script>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>
