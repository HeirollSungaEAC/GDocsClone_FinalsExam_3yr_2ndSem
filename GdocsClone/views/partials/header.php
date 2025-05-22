<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    body {
        font-family: 'Inter', sans-serif;
        background-color: #F9FAFB;
    }
    
    /* Custom Animations */
    .animate-fade-in {
        animation: fadeIn 0.5s ease-out forwards;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    /* Custom Scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    
    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    
    ::-webkit-scrollbar-thumb {
        background: #c7d2fe;
        border-radius: 4px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: #818cf8;
    }
    
    /* Dropdown Menu Animation */
    .dropdown-content {
        visibility: hidden;
        opacity: 0;
        transform: translateY(-10px);
        transition: visibility 0s, opacity 0.2s, transform 0.2s;
    }
    
    .dropdown:hover .dropdown-content {
        visibility: visible;
        opacity: 1;
        transform: translateY(0);
    }
</style>

<header class="bg-white shadow-md">
    <div class="container mx-auto px-4 py-3 flex flex-wrap items-center justify-between">
        <div class="flex items-center">
                <i class="fas fa-file-alt text-indigo-600 text-2xl mr-2"></i>
                <span class="font-bold text-indigo-600 text-2xl tracking-tight">GDocs</span>
            </a>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <nav class="hidden md:flex ml-8 space-x-4">
                    <a href="<?php echo ($_SESSION['role'] === 'admin') ? '/GdocsClone/views/admin/dashboard.php' : '/GdocsClone/views/user/dashboard.php'; ?>" class="text-gray-600 hover:text-indigo-700 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                        <i class="fas fa-home mr-1"></i> Dashboard
                    </a>
                </nav>
            <?php endif; ?>
        </div>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="flex items-center mt-2 md:mt-0">
                <div class="relative dropdown">
                    <button class="flex items-center text-gray-700 hover:text-indigo-600 focus:outline-none">

                        <span class="font-medium text-sm mr-1"><?php echo htmlspecialchars($_SESSION['username'] ?? $_SESSION['role']); ?></span>
                        <i class="fas fa-chevron-down text-xs"></i>
                    </button>
                    
                    <div class="dropdown-content absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10">
                        <div class="px-4 py-2 text-xs text-gray-500 border-b border-gray-100">
                            <span>Signed in as</span>
                            <p class="font-semibold text-gray-700"><?php echo htmlspecialchars($_SESSION['role']); ?></p>
                        </div>
                        
                        <div class="border-t border-gray-100"></div>
                        
                        <a href="/GdocsClone/views/auth/login.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-red-700">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="flex items-center space-x-3">
                <a href="/GdocsClone/views/auth/login.php" class="text-indigo-600 hover:text-indigo-800 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                    Login
                </a>
                <a href="/GdocsClone/views/auth/register.php" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium shadow-sm transition-colors duration-200">
                    Register
                </a>
            </div>
        <?php endif; ?>
    </div>
    
    <?php if (isset($_SESSION['admin_id'])): ?>
        <div class="md:hidden border-t border-gray-200 py-2 px-4">
            <nav class="flex justify-around">
                <a href="/GdocsClone/views/admin/dashboard.php" class="text-gray-600 hover:text-indigo-700 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200 flex flex-col items-center">
                    <i class="fas fa-home mb-1"></i>
                    <span>Dashboard</span>
                </a>
            </nav>
        </div>

        
    <?php endif; ?>
</header>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropdown = document.querySelector('.dropdown');
        if (dropdown) {
            dropdown.addEventListener('click', function(e) {
                const dropdownContent = this.querySelector('.dropdown-content');
                if (dropdownContent.style.visibility === 'visible') {
                    dropdownContent.style.visibility = 'hidden';
                    dropdownContent.style.opacity = '0';
                    dropdownContent.style.transform = 'translateY(-10px)';
                } else {
                    dropdownContent.style.visibility = 'visible';
                    dropdownContent.style.opacity = '1';
                    dropdownContent.style.transform = 'translateY(0)';
                }
            });
        }
    });
</script>