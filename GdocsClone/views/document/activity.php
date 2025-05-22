<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../models/ActivityLog.php';
require_once __DIR__ . '/../../models/Document.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['document_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$document_id = intval($_GET['document_id']);
$activityLogModel = new ActivityLog($pdo);
$documentModel = new Document($pdo);

// Get document info
$document = $documentModel->getById($document_id);
$logs = $activityLogModel->getByDocument($document_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Log - <?php echo htmlspecialchars($document['title']); ?> - GDocs Clone</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen">
    <?php include __DIR__ . '/../partials/header.php'; ?>
    
    <main class="container mx-auto px-4 py-8 max-w-4xl">
        <!-- Document Header -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800 mb-2">Activity Log</h2>
                    <p class="text-gray-600 flex items-center">
                        <i class="fas fa-file-alt text-indigo-600 mr-2"></i>
                        <?php echo htmlspecialchars($document['title'] ?: 'Untitled Document'); ?>
                    </p>
                </div>
                <div class="flex items-center space-x-3 mt-4 md:mt-0">
                    <a href="editor.php?id=<?php echo $document_id; ?>" 
                       class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow-md transition-all duration-200 transform hover:scale-105 flex items-center">
                        <i class="fas fa-edit mr-2"></i> Back to Document
                    </a>
                    <a href="messages.php?document_id=<?php echo $document_id; ?>" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-md transition-all duration-200 transform hover:scale-105 flex items-center">
                        <i class="fas fa-comments mr-2"></i> Open Chat
                    </a>
                </div>
            </div>
        </div>

        <!-- Activity Timeline -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                <i class="fas fa-history text-indigo-600 mr-3"></i>
                Document History
            </h3>
            
            <?php if ($logs): ?>
                <div class="relative">
                    <!-- Timeline line -->
                    <div class="absolute left-8 top-0 bottom-0 w-px bg-gray-200"></div>
                    
                    <div class="space-y-6">
                        <?php foreach ($logs as $index => $log): ?>
                            <div class="relative flex items-start group">
                                <!-- Timeline dot -->
                                <div class="flex-shrink-0 w-4 h-4 bg-indigo-600 rounded-full border-4 border-white shadow-md z-10 group-hover:bg-indigo-700 transition-colors duration-200"></div>
                                
                                <!-- Activity content -->
                                <div class="ml-6 bg-gray-50 rounded-lg p-4 flex-grow shadow-sm hover:shadow-md transition-shadow duration-200">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center">
                                            <div class="bg-indigo-100 text-indigo-700 h-8 w-8 rounded-full flex items-center justify-center mr-3">
                                                <span class="font-medium text-sm"><?php echo strtoupper(substr($log['username'], 0, 1)); ?></span>
                                            </div>
                                            <span class="font-semibold text-gray-800"><?php echo htmlspecialchars($log['username']); ?></span>
                                        </div>
                                        <time class="text-sm text-gray-500 flex items-center">
                                            <i class="far fa-clock mr-1"></i>
                                            <?php echo date('M j, Y g:i A', strtotime($log['created_at'])); ?>
                                        </time>
                                    </div>
                                    
                                    <p class="text-gray-700 flex items-center">
                                        <?php 
                                        $action = htmlspecialchars($log['action']);
                                        $icon = 'fas fa-edit';
                                        
                                        if (strpos($action, 'created') !== false) {
                                            $icon = 'fas fa-plus-circle text-green-600';
                                        } elseif (strpos($action, 'deleted') !== false) {
                                            $icon = 'fas fa-trash text-red-600';
                                        } elseif (strpos($action, 'shared') !== false) {
                                            $icon = 'fas fa-share-alt text-blue-600';
                                        } elseif (strpos($action, 'title') !== false) {
                                            $icon = 'fas fa-heading text-purple-600';
                                        } else {
                                            $icon = 'fas fa-edit text-indigo-600';
                                        }
                                        ?>
                                        <i class="<?php echo $icon; ?> mr-2"></i>
                                        <?php echo $action; ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-12">
                    <i class="fas fa-history text-gray-300 text-6xl mb-4"></i>
                    <h4 class="text-xl font-medium text-gray-500 mb-2">No Activity Yet</h4>
                    <p class="text-gray-400">Document activity will appear here as changes are made.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Quick Stats -->
        <?php if ($logs): ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                <div class="bg-white rounded-xl shadow-lg p-6 text-center">
                    <i class="fas fa-edit text-indigo-600 text-3xl mb-3"></i>
                    <h4 class="text-2xl font-bold text-gray-800"><?php echo count($logs); ?></h4>
                    <p class="text-gray-600">Total Activities</p>
                </div>
                
                <div class="bg-white rounded-xl shadow-lg p-6 text-center">
                    <i class="fas fa-users text-green-600 text-3xl mb-3"></i>
                    <h4 class="text-2xl font-bold text-gray-800"><?php echo count(array_unique(array_column($logs, 'username'))); ?></h4>
                    <p class="text-gray-600">Contributors</p>
                </div>
                
                <div class="bg-white rounded-xl shadow-lg p-6 text-center">
                    <i class="fas fa-calendar text-blue-600 text-3xl mb-3"></i>
                    <h4 class="text-2xl font-bold text-gray-800"><?php echo date('M j', strtotime($logs[0]['created_at'])); ?></h4>
                    <p class="text-gray-600">Last Activity</p>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animate timeline items
        const timelineItems = document.querySelectorAll('.space-y-6 > div');
        timelineItems.forEach((item, index) => {
            item.style.opacity = '0';
            item.style.transform = 'translateX(-20px)';
            item.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            
            setTimeout(() => {
                item.style.opacity = '1';
                item.style.transform = 'translateX(0)';
            }, 100 + (index * 100));
        });
        
        // Animate stats cards
        const statsCards = document.querySelectorAll('.grid > div');
        statsCards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 300 + (index * 100));
        });
    });
    </script>
</body>
</html>