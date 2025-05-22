<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['document_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$document_id = intval($_GET['document_id']);

// Get document title
$stmt = $pdo->prepare("SELECT title FROM documents WHERE id = ?");
$stmt->execute([$document_id]);
$document = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Chat - <?php echo htmlspecialchars($document['title']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="../../public/js/messaging.js"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <?php include __DIR__ . '/../partials/header.php'; ?>
    
    <main class="container mx-auto px-4 py-8 max-w-4xl">
        <!-- Chat Header -->
        <div class="bg-white rounded-t-xl shadow-lg p-6 mb-1">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Document Chat</h2>
                    <p class="text-gray-600 flex items-center">
                        <i class="fas fa-file-alt text-indigo-600 mr-2"></i>
                        <?php echo htmlspecialchars($document['title']); ?>
                    </p>
                </div>
                <a href="editor.php?id=<?php echo $document_id; ?>" 
                   class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow-md transition-all duration-200 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Document
                </a>
            </div>
        </div>

        <!-- Messages Container -->
        <div class="bg-white shadow-lg rounded-b-xl">
            <div id="messages" class="h-[500px] overflow-y-auto p-6 space-y-4"></div>

            <!-- Message Input Form -->
            <form id="messageForm" class="p-4 border-t border-gray-100">
                <input type="hidden" name="document_id" value="<?php echo $document_id; ?>">
                <div class="flex gap-2">
                    <input type="text" 
                           name="message" 
                           id="messageInput" 
                           placeholder="Type your message..." 
                           autocomplete="off" 
                           required
                           class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg shadow-md transition-all duration-200 flex items-center">
                        <i class="fas fa-paper-plane mr-2"></i> Send
                    </button>
                </div>
            </form>
        </div>
    </main>

    <style>
    #messages::-webkit-scrollbar {
        width: 6px;
    }
    #messages::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    #messages::-webkit-scrollbar-thumb {
        background: #c7d2fe;
        border-radius: 10px;
    }
    #messages::-webkit-scrollbar-thumb:hover {
        background: #818cf8;
    }

    /* Message animations */
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .message-item {
        animation: slideIn 0.3s ease-out forwards;
    }
    </style>

    <script>
    // Enhanced message display
    function createMessageElement(msg) {
        const isCurrentUser = msg.user_id == <?php echo $_SESSION['user_id']; ?>;
        const div = document.createElement('div');
        div.className = `message-item flex ${isCurrentUser ? 'justify-end' : 'justify-start'}`;
        
        div.innerHTML = `
            <div class="${isCurrentUser ? 'bg-indigo-100 text-indigo-900' : 'bg-gray-100 text-gray-900'} 
                        rounded-lg px-4 py-2 max-w-[70%] shadow-sm">
                <div class="flex items-center gap-2 mb-1">
                    <span class="font-semibold text-sm">${msg.username}</span>
                    <span class="text-xs text-gray-500">${new Date(msg.created_at).toLocaleTimeString()}</span>
                </div>
                <p>${msg.message}</p>
            </div>
        `;
        return div;
    }

    // Override the default message display
    const originalFetchMessages = window.fetchMessages;
    window.fetchMessages = function() {
        fetch(`../../controllers/MessageController.php?document_id=${document_id}`)
            .then(res => res.json())
            .then(data => {
                messagesDiv.innerHTML = '';
                data.forEach(msg => {
                    messagesDiv.appendChild(createMessageElement(msg));
                });
                messagesDiv.scrollTop = messagesDiv.scrollHeight;
            });
    };
    </script>
</body>
</html>