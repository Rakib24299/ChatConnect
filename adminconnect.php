<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "messenger_db";

$errorMessage = "";
$successMessage = "";

// Create connection
$conn = @new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    $dbError = "Database connection error: " . $conn->connect_error;
} else {
    // Handle POST Login Submission
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['username'])) {
        $input_username = trim($_POST['username']);
        $input_password = $_POST['password'];

        $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ? OR email = ?");
        if ($stmt) {
            $stmt->bind_param("ss", $input_username, $input_username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                $admin = $result->fetch_assoc();
                
                // Check hashed password or plain text match
                if (password_verify($input_password, $admin['password']) || $input_password === $admin['password']) {
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_username'] = $admin['username'];
                    header("Location: adminpanel.php");
                    exit();
                } else {
                    $errorMessage = "Invalid password. Please check your credentials.";
                }
            } else {
                $errorMessage = "No administrator account found with that username/email.";
            }
            $stmt->close();
        } else {
            $errorMessage = "Database query preparation failed.";
        }
    }

    // Fetch quick stats for overview
    $userCountRes = $conn->query("SELECT COUNT(*) as total FROM users");
    $totalUsers = ($userCountRes && $row = $userCountRes->fetch_assoc()) ? $row['total'] : 0;

    $adsCountRes = $conn->query("SELECT COUNT(*) as total FROM tb_ads");
    $totalAds = ($adsCountRes && $row = $adsCountRes->fetch_assoc()) ? $row['total'] : 0;

    $chatCountRes = $conn->query("SELECT COUNT(*) as total FROM chats");
    $totalChats = ($chatCountRes && $row = $chatCountRes->fetch_assoc()) ? $row['total'] : 0;
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard & Hub - ChatConnect</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            DEFAULT: '#4171e0',
                            50: '#f2f6fe',
                            100: '#e4edfd',
                            200: '#ccdcfa',
                            300: '#a5c3f6',
                            400: '#75a1f0',
                            500: '#4171e0',
                            600: '#3056c7',
                            700: '#2643a2',
                            800: '#233983',
                            900: '#21326c',
                            950: '#141d43',
                        }
                    }
                }
            }
        }
    </script>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-800 dark:bg-slate-950 dark:text-slate-100 min-h-screen flex flex-col transition-colors duration-300">

    <!-- Navbar -->
    <header class="sticky top-0 z-50 backdrop-blur-md bg-white/85 dark:bg-slate-900/85 border-b border-slate-200 dark:border-slate-800 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20">
                <!-- Logo & Mobile Sidebar Toggle -->
                <div class="flex items-center gap-3">
                    <!-- Mobile Sidebar Trigger Button -->
                    <button onclick="toggleAdminSidebar()" class="p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 lg:hidden transition" title="Toggle Sidebar">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="9" y1="3" x2="9" y2="21"></line>
                        </svg>
                    </button>

                    <a href="adminpanel.php" class="flex items-center gap-3 group">
                        <div class="w-10 h-10 rounded-2xl bg-primary text-white flex items-center justify-center shadow-lg shadow-primary/30 group-hover:scale-105 transition-transform duration-200">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                            </svg>
                        </div>
                        <span class="text-2xl font-black tracking-tight text-primary">ChatConnect</span>
                    </a>
                </div>

                <!-- Action Buttons & Dark Mode Toggle -->
                <div class="flex items-center gap-3">
                    <button onclick="toggleDarkMode()" class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 transition" title="Toggle Theme" aria-label="Toggle Theme">
                        <span id="theme-icon-container">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                            </svg>
                        </span>
                    </button>

                    <a href="adminlogin.php" class="px-5 py-2.5 rounded-xl font-semibold text-white bg-primary hover:bg-primary-600 shadow-md shadow-primary/25 hover:shadow-lg hover:shadow-primary/35 hover:-translate-y-0.5 transition duration-200 text-sm">
                        Admin Login
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Layout Container (Sidebar + Main Content) -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full flex-grow flex flex-col lg:flex-row gap-8 items-start">

        <!-- Sidebar Navigation -->
        <aside id="admin-sidebar" class="w-full lg:w-72 shrink-0 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-5 shadow-sm space-y-6 transition-all duration-300">
            
            <!-- Sidebar Header / Admin Tag -->
            <div class="flex items-center gap-3 px-3 py-2 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200/60 dark:border-slate-700/60">
                <div class="w-10 h-10 rounded-xl bg-primary text-white flex items-center justify-center font-bold text-base shadow-sm">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    </svg>
                </div>
                <div class="overflow-hidden">
                    <div class="text-sm font-bold text-slate-900 dark:text-white truncate">Administrator</div>
                    <div class="text-xs text-slate-500 dark:text-slate-400 truncate">Control Navigation</div>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="space-y-1.5">
                <div class="px-3 pb-2 text-[11px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                    Main Menu
                </div>

                <!-- 1. Admin Information -->
                <a href="amininfo.php" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-medium text-slate-700 dark:text-slate-200 hover:text-primary dark:hover:text-primary-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 transition group">
                    <div class="w-9 h-9 rounded-xl bg-primary-50 dark:bg-primary/10 text-primary flex items-center justify-center group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <span class="text-sm font-semibold">Admin Information</span>
                </a>

                <!-- 2. View Members -->
                <a href="view.php" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-medium text-slate-700 dark:text-slate-200 hover:text-primary dark:hover:text-primary-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 transition group">
                    <div class="w-9 h-9 rounded-xl bg-primary-50 dark:bg-primary/10 text-primary flex items-center justify-center group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-semibold">View Members</span>
                </a>

                <!-- 3. Search Member -->
                <a href="search.php" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-medium text-slate-700 dark:text-slate-200 hover:text-primary dark:hover:text-primary-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 transition group">
                    <div class="w-9 h-9 rounded-xl bg-primary-50 dark:bg-primary/10 text-primary flex items-center justify-center group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </div>
                    <span class="text-sm font-semibold">Search Member</span>
                </a>

                <!-- 4. Advertisements -->
                <a href="ads.php" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-medium text-slate-700 dark:text-slate-200 hover:text-primary dark:hover:text-primary-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 transition group">
                    <div class="w-9 h-9 rounded-xl bg-primary-50 dark:bg-primary/10 text-primary flex items-center justify-center group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                            <line x1="8" y1="21" x2="16" y2="21"></line>
                            <line x1="12" y1="17" x2="12" y2="21"></line>
                        </svg>
                    </div>
                    <span class="text-sm font-semibold">Advertisements</span>
                </a>

                <!-- 5. Admin Panel -->
                <a href="adminpanel.php" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-medium text-slate-700 dark:text-slate-200 hover:text-primary dark:hover:text-primary-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 transition group">
                    <div class="w-9 h-9 rounded-xl bg-primary-50 dark:bg-primary/10 text-primary flex items-center justify-center group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                    </div>
                    <span class="text-sm font-semibold">Admin Panel</span>
                </a>
            </nav>

            <!-- Quick Links / Logout -->
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 space-y-2">
                <a href="desh.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl font-medium text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition text-xs">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    </svg>
                    <span>Back to Home</span>
                </a>
                <a href="adminlogin.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl font-semibold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 transition text-xs">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                    <span>Switch Admin</span>
                </a>
            </div>

        </aside>

        <!-- Main Content Area -->
        <main class="flex-grow w-full space-y-8">

            <!-- Error Banner (if any) -->
            <?php if (!empty($errorMessage)): ?>
                <div class="p-5 rounded-3xl bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 text-red-700 dark:text-red-400 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-red-100 dark:bg-red-500/20 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6 text-red-600 dark:text-red-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold">Authentication Issue</h4>
                            <p class="text-sm opacity-90"><?php echo htmlspecialchars($errorMessage); ?></p>
                        </div>
                    </div>
                    <a href="adminlogin.php" class="px-5 py-2 rounded-xl text-sm font-bold text-white bg-red-600 hover:bg-red-700 transition shrink-0">
                        Try Again
                    </a>
                </div>
            <?php endif; ?>

            <!-- Welcome Header Banner -->
            <div class="p-8 rounded-3xl bg-gradient-to-r from-primary to-primary-700 text-white shadow-xl shadow-primary/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                <div>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-white/20 text-white mb-3 backdrop-blur-sm">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                        </svg>
                        Admin Control Center
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight">
                        Welcome to Admin Management
                    </h1>
                    <p class="text-primary-100 text-sm mt-1 max-w-xl">
                        Select any option from the sidebar to manage users, view advertisements, or perform database operations.
                    </p>
                </div>
                <div class="shrink-0 flex items-center gap-3">
                    <a href="adminpanel.php" class="px-6 py-3 rounded-2xl font-bold bg-white text-primary hover:bg-slate-100 shadow-md transition duration-200 text-sm flex items-center gap-2">
                        <span>Open Panel</span>
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Stats Overview Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <!-- Stat 1: Users -->
                <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center gap-5">
                    <div class="w-14 h-14 rounded-2xl bg-primary-50 dark:bg-primary/10 text-primary flex items-center justify-center shrink-0">
                        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">
                            <?php echo number_format($totalUsers); ?>
                        </div>
                        <div class="text-xs sm:text-sm font-medium text-slate-500 dark:text-slate-400">Total Registered Members</div>
                    </div>
                </div>

                <!-- Stat 2: Advertisements -->
                <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center gap-5">
                    <div class="w-14 h-14 rounded-2xl bg-primary-50 dark:bg-primary/10 text-primary flex items-center justify-center shrink-0">
                        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                            <line x1="8" y1="21" x2="16" y2="21"></line>
                            <line x1="12" y1="17" x2="12" y2="21"></line>
                        </svg>
                    </div>
                    <div>
                        <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">
                            <?php echo number_format($totalAds); ?>
                        </div>
                        <div class="text-xs sm:text-sm font-medium text-slate-500 dark:text-slate-400">Total Active Ads</div>
                    </div>
                </div>

                <!-- Stat 3: Total Chats -->
                <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center gap-5">
                    <div class="w-14 h-14 rounded-2xl bg-primary-50 dark:bg-primary/10 text-primary flex items-center justify-center shrink-0">
                        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">
                            <?php echo number_format($totalChats); ?>
                        </div>
                        <div class="text-xs sm:text-sm font-medium text-slate-500 dark:text-slate-400">Messages Stored</div>
                    </div>
                </div>
            </div>

            <!-- Quick Action Cards Grid -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white">Direct Management Shortcuts</h2>
                        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">Click any card or use the sidebar menu to navigate</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Shortcut 1 -->
                    <a href="amininfo.php" class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/60 dark:border-slate-700/60 hover:border-primary/50 hover:bg-white dark:hover:bg-slate-800 transition flex items-center justify-between group">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 dark:text-white text-sm group-hover:text-primary transition-colors">Admin Information</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Manage administrator profiles</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-primary group-hover:translate-x-1 transition" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </a>

                    <!-- Shortcut 2 -->
                    <a href="view.php" class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/60 dark:border-slate-700/60 hover:border-primary/50 hover:bg-white dark:hover:bg-slate-800 transition flex items-center justify-between group">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 dark:text-white text-sm group-hover:text-primary transition-colors">View Members</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Browse user list and registrations</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-primary group-hover:translate-x-1 transition" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </a>

                    <!-- Shortcut 3 -->
                    <a href="search.php" class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/60 dark:border-slate-700/60 hover:border-primary/50 hover:bg-white dark:hover:bg-slate-800 transition flex items-center justify-between group">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 dark:text-white text-sm group-hover:text-primary transition-colors">Search Member</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Find members by ID, name or email</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-primary group-hover:translate-x-1 transition" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </a>

                    <!-- Shortcut 4 -->
                    <a href="ads.php" class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/60 dark:border-slate-700/60 hover:border-primary/50 hover:bg-white dark:hover:bg-slate-800 transition flex items-center justify-between group">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                                    <line x1="8" y1="21" x2="16" y2="21"></line>
                                    <line x1="12" y1="17" x2="12" y2="21"></line>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 dark:text-white text-sm group-hover:text-primary transition-colors">Advertisements</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Manage promotional ad banners</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-primary group-hover:translate-x-1 transition" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </a>
                </div>
            </div>

        </main>

    </div>

    <!-- Footer -->
    <footer class="bg-white/85 dark:bg-slate-900/85 border-t border-slate-200 dark:border-slate-800 transition-colors mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row items-center justify-between h-auto sm:h-20 py-4 sm:py-0 gap-3">
                <!-- Left: Copyright -->
                <div class="text-center sm:text-left">
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
                        &copy; 2024 ChatConnect. All rights reserved.
                    </p>
                </div>

                <!-- Right: Developed by -->
                <div class="text-center sm:text-right">
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
                        Developed by <span class="font-semibold text-slate-700 dark:text-slate-200">Rakib</span>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts for Dark Mode & Mobile Menu -->
    <script>
        const sunSvg = `<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>`;
        
        const moonSvg = `<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>`;

        const barsSvg = `<svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>`;

        const xmarkSvg = `<svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>`;

        function initTheme() {
            const isDark = localStorage.getItem('theme') === 'dark' || 
                (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);
            
            if (isDark) {
                document.documentElement.classList.add('dark');
                updateThemeIcons(true);
            } else {
                document.documentElement.classList.remove('dark');
                updateThemeIcons(false);
            }
        }

        function toggleDarkMode() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            updateThemeIcons(isDark);
        }

        function updateThemeIcons(isDark) {
            const desktopIcon = document.getElementById('theme-icon-container');
            const mobileIcon = document.getElementById('theme-icon-mobile-container');
            
            if (desktopIcon) desktopIcon.innerHTML = isDark ? sunSvg : moonSvg;
            if (mobileIcon) mobileIcon.innerHTML = isDark ? sunSvg : moonSvg;
        }

        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            const menuIconContainer = document.getElementById('menu-icon-container');
            menu.classList.toggle('hidden');
            
            if (menu.classList.contains('hidden')) {
                menuIconContainer.innerHTML = barsSvg;
            } else {
                menuIconContainer.innerHTML = xmarkSvg;
            }
        }

        function toggleAdminSidebar() {
            const sidebar = document.getElementById('admin-sidebar');
            sidebar.classList.toggle('hidden');
        }

        initTheme();
    </script>
</body>

</html>
