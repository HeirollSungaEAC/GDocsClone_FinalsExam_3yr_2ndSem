<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../models/Document.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$documentModel = new Document($pdo);
$owned = $documentModel->getByUser($_SESSION['user_id']);
$shared = $documentModel->getSharedWith($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - GDocs Clone</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen">
    <?php include __DIR__ . '/../partials/header.php'; ?>
    
    <main class="container mx-auto px-4 py-8 max-w-6xl">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <h2 class="text-3xl font-bold text-indigo-700 mb-4 md:mb-0">Welcome to your Dashboard</h2>
            <a href="../document/editor.php" class="flex items-center justify-center bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg shadow-md transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50">
                <i class="fas fa-plus mr-2"></i> Create New Document
            </a>
        </div>
    </main>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add fade-in animation to the document items
        const items = document.querySelectorAll('.divide-y > div');
        items.forEach((item, index) => {
            item.style.opacity = '0';
            item.style.transform = 'translateY(20px)';
            item.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            
            setTimeout(() => {
                item.style.opacity = '1';
                item.style.transform = 'translateY(0)';
            }, 100 + (index * 50)); // Staggered animation
        });
    });
    </script>
</body>
</html>        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- My Documents Section -->
            <section class="bg-white rounded-xl shadow-lg p-6 transition-all duration-300 hover:shadow-xl">
                <h3 class="text-2xl font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-file-alt text-indigo-600 mr-3"></i>My Documents
                </h3>
                
                <div class="divide-y divide-gray-100">
                    <?php if (empty($owned)): ?>
                        <p class="py-4 text-gray-500 italic">You haven't created any documents yet.</p>
                    <?php else: ?>
                        <?php foreach ($owned as $doc): ?>
                            <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between group">
                                <div class="flex-grow mb-2 sm:mb-0">
                                    <a href="../document/editor.php?id=<?php echo $doc['id']; ?>" class="text-lg font-medium text-indigo-700 hover:text-indigo-900 transition-colors duration-200 flex items-center">
                                        <i class="fas fa-file-lines mr-2 text-indigo-500"></i>
                                        <?php echo htmlspecialchars($doc['title'] ?: 'Untitled Document'); ?>
                                    </a>
                                    <p class="text-sm text-gray-500 mt-1">
                                        <i class="far fa-clock mr-1"></i> <?php echo date('M j, Y g:i A', strtotime($doc['updated_at'])); ?>
                                    </p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="../document/editor.php?id=<?php echo $doc['id']; ?>" class="text-gray-600 hover:text-indigo-700 bg-gray-100 hover:bg-gray-200 p-2 rounded-full transition-colors duration-200" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="../document/activity.php?document_id=<?php echo $doc['id']; ?>" class="text-gray-600 hover:text-indigo-700 bg-gray-100 hover:bg-gray-200 p-2 rounded-full transition-colors duration-200" title="View Activity">
                                        <i class="fas fa-history"></i>
                                    </a>
                                    <form method="POST" action="../../controllers/DocumentController.php" class="inline" onsubmit="return confirm('Are you sure you want to delete this document?');">
                                        <input type="hidden" name="delete_id" value="<?php echo $doc['id']; ?>">
                                        <button type="submit" class="text-gray-600 hover:text-red-700 bg-gray-100 hover:bg-red-100 p-2 rounded-full transition-colors duration-200" title="Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Shared With Me Section -->
            <section class="bg-white rounded-xl shadow-lg p-6 transition-all duration-300 hover:shadow-xl">
                <h3 class="text-2xl font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-share-alt text-indigo-600 mr-3"></i>Shared with Me
                </h3>
                
                <div class="divide-y divide-gray-100">
                    <?php if (empty($shared)): ?>
                        <p class="py-4 text-gray-500 italic">No documents have been shared with you yet.</p>
                    <?php else: ?>
                        <?php foreach ($shared as $doc): ?>
                            <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between group">
                                <div class="flex-grow mb-2 sm:mb-0">
                                    <a href="../document/editor.php?id=<?php echo $doc['id']; ?>" class="text-lg font-medium text-indigo-700 hover:text-indigo-900 transition-colors duration-200 flex items-center">
                                        <i class="fas fa-file-lines mr-2 text-indigo-500"></i>
                                        <?php echo htmlspecialchars($doc['title'] ?: 'Untitled Document'); ?>
                                    </a>
                                    <p class="text-sm text-gray-500 mt-1">
                                        <i class="far fa-clock mr-1"></i> <?php echo date('M j, Y g:i A', strtotime($doc['updated_at'])); ?>
                                    </p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="../document/editor.php?id=<?php echo $doc['id']; ?>" class="text-gray-600 hover:text-indigo-700 bg-gray-100 hover:bg-gray-200 p-2 rounded-full transition-colors duration-200" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="../document/activity.php?document_id=<?php echo $doc['id']; ?>" class="text-gray-600 hover:text-indigo-700 bg-gray-100 hover:bg-gray-200 p-2 rounded-full transition-colors duration-200" title="View Activity">
                                        <i class="fas fa-history"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        </div>