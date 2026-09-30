<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "messenger_db";

// Connect to DB
$conn = @new mysqli($servername, $username, $password, $dbname);

$totalUsers = 0;
$totalAds = 0;
$totalChats = 0;
$totalFriends = 0;
$recentUsers = [];

if ($conn && !$conn->connect_error) {
    // Count total users
    $userCountRes = $conn->query("SELECT COUNT(*) as total FROM users");
    if ($userCountRes && $row = $userCountRes->fetch_assoc()) {
        $totalUsers = $row['total'];
    }

    // Count total ads
    $adsCountRes = $conn->query("SELECT COUNT(*) as total FROM tb_ads");
    if ($adsCountRes && $row = $adsCountRes->fetch_assoc()) {
        $totalAds = $row['total'];
    }

    // Count total chats
    $chatCountRes = $conn->query("SELECT COUNT(*) as total FROM chats");
    if ($chatCountRes && $row = $chatCountRes->fetch_assoc()) {
        $totalChats = $row['total'];
    }

    // Count total friends
    $friendsCountRes = $conn->query("SELECT COUNT(*) as total FROM friends");
    if ($friendsCountRes && $row = $friendsCountRes->fetch_assoc()) {
        $totalFriends = $row['total'];
    }

    // Fetch 5 most recent registered users
    $recentUsersRes = $conn->query("SELECT * FROM users ORDER BY id DESC LIMIT 5");
    if ($recentUsersRes && $recentUsersRes->num_rows > 0) {
        while ($u = $recentUsersRes->fetch_assoc()) {
            $recentUsers[] = $u;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel Dashboard - ChatConnect</title>
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
                <!-- Logo & Mobile Sidebar Trigger -->
                <div class="flex items-center gap-3">
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

                    <a href="adminlogin.php" class="px-5 py-2.5 rounded-xl font-semibold text-slate-700 dark:text-slate-200 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 transition text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Layout Container (Sidebar + Main Content) -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full flex-grow flex flex-col lg:flex-row gap-8 items-start">

        <!-- Sidebar Navigation -->
        <aside id="admin-sidebar" class="w-full lg:w-72 shrink-0 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-5 shadow-sm space-y-6 transition-all duration-300">
            
            <!-- Admin Profile Tag -->
            <div class="flex items-center gap-3 px-3 py-2.5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200/60 dark:border-slate-700/60">
                <div class="w-10 h-10 rounded-xl bg-primary text-white flex items-center justify-center font-bold text-base shadow-sm">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    </svg>
                </div>
                <div class="overflow-hidden">
                    <div class="text-sm font-bold text-slate-900 dark:text-white truncate">Administrator</div>
                    <div class="text-xs text-emerald-600 dark:text-emerald-400 font-medium flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block animate-pulse"></span>
                        Active Session
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="space-y-1.5">
                <div class="px-3 pb-2 text-[11px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                    Admin Modules
                </div>

                <!-- 1. Dashboard / Overview -->
                <a href="adminpanel.php" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-semibold text-primary bg-primary-50 dark:bg-primary/10 transition group">
                    <div class="w-9 h-9 rounded-xl bg-primary text-white flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                    </div>
                    <span class="text-sm">Dashboard</span>
                </a>

                <!-- 2. Admin Information -->
                <a href="amininfo.php" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-medium text-slate-700 dark:text-slate-200 hover:text-primary dark:hover:text-primary-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 transition group">
                    <div class="w-9 h-9 rounded-xl bg-primary-50 dark:bg-primary/10 text-primary flex items-center justify-center group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <span class="text-sm font-semibold">Admin Information</span>
                </a>

                <!-- 3. View Members -->
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

                <!-- 4. Search Member -->
                <a href="search.php" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-medium text-slate-700 dark:text-slate-200 hover:text-primary dark:hover:text-primary-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 transition group">
                    <div class="w-9 h-9 rounded-xl bg-primary-50 dark:bg-primary/10 text-primary flex items-center justify-center group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </div>
                    <span class="text-sm font-semibold">Search Member</span>
                </a>

                <!-- 5. Advertisements -->
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
            </nav>

            <!-- Quick Links / Actions -->
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
                    <span>Logout Admin</span>
                </a>
            </div>

        </aside>

        <!-- Main Content Area -->
        <main class="flex-grow w-full space-y-8">

            <!-- Banner Header -->
            <div class="p-8 rounded-3xl bg-gradient-to-r from-primary via-primary-600 to-primary-700 text-white shadow-xl shadow-primary/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                <div>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-white/20 text-white mb-3 backdrop-blur-sm">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                        </svg>
                        Main Administration Console
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight">
                        Admin Dashboard Overview
                    </h1>
                    <p class="text-primary-100 text-sm mt-1 max-w-xl">
                        Monitor registered members, manage active advertising campaigns, and review community chats in real-time.
                    </p>
                </div>
                <div class="shrink-0 flex items-center gap-3">
                    <a href="view.php" class="px-6 py-3 rounded-2xl font-bold bg-white text-primary hover:bg-slate-100 shadow-md transition duration-200 text-sm flex items-center gap-2">
                        <span>View Members</span>
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- 4 Metric Stat Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <!-- Stat 1: Registered Users -->
                <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Members</span>
                        <div class="w-10 h-10 rounded-2xl bg-primary-50 dark:bg-primary/10 text-primary flex items-center justify-center">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="text-3xl font-black text-slate-900 dark:text-white">
                            <?php echo number_format($totalUsers); ?>
                        </div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">Total Registered Users</div>
                    </div>
                </div>

                <!-- Stat 2: Active Ads -->
                <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Campaigns</span>
                        <div class="w-10 h-10 rounded-2xl bg-primary-50 dark:bg-primary/10 text-primary flex items-center justify-center">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                                <line x1="8" y1="21" x2="16" y2="21"></line>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="text-3xl font-black text-slate-900 dark:text-white">
                            <?php echo number_format($totalAds); ?>
                        </div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">Total Active Ads</div>
                    </div>
                </div>

                <!-- Stat 3: Total Messages -->
                <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Messages</span>
                        <div class="w-10 h-10 rounded-2xl bg-primary-50 dark:bg-primary/10 text-primary flex items-center justify-center">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="text-3xl font-black text-slate-900 dark:text-white">
                            <?php echo number_format($totalChats); ?>
                        </div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">Chats In Database</div>
                    </div>
                </div>

                <!-- Stat 4: Total Friendships -->
                <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Connections</span>
                        <div class="w-10 h-10 rounded-2xl bg-primary-50 dark:bg-primary/10 text-primary flex items-center justify-center">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="8.5" cy="7" r="4"></circle>
                                <polyline points="17 11 19 13 23 9"></polyline>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="text-3xl font-black text-slate-900 dark:text-white">
                            <?php echo number_format($totalFriends); ?>
                        </div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">Friend Connections</div>
                    </div>
                </div>
            </div>

            <!-- Recent Members Table -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white">Recently Registered Members</h2>
                        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">Latest accounts registered in ChatConnect</p>
                    </div>
                    <a href="view.php" class="inline-flex items-center gap-2 text-xs sm:text-sm font-bold text-primary hover:underline">
                        <span>View Complete Member List</span>
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </a>
                </div>

                <div class="overflow-x-auto rounded-2xl border border-slate-200/60 dark:border-slate-800">
                    <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                        <thead class="bg-slate-50 dark:bg-slate-800/60 text-xs uppercase font-extrabold text-slate-400 tracking-wider">
                            <tr>
                                <th scope="col" class="px-5 py-4">ID</th>
                                <th scope="col" class="px-5 py-4">Full Name</th>
                                <th scope="col" class="px-5 py-4">Email Address</th>
                                <th scope="col" class="px-5 py-4">Gender</th>
                                <th scope="col" class="px-5 py-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                            <?php if (!empty($recentUsers)): ?>
                                <?php foreach ($recentUsers as $user): ?>
                                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                                        <td class="px-5 py-4 font-bold text-slate-900 dark:text-white">
                                            #<?php echo htmlspecialchars($user['id']); ?>
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-primary/10 text-primary font-bold flex items-center justify-center text-xs">
                                                    <?php echo strtoupper(substr($user['first_name'] ?? 'U', 0, 1)); ?>
                                                </div>
                                                <span class="font-bold text-slate-800 dark:text-white">
                                                    <?php echo htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')); ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 text-slate-500 dark:text-slate-400">
                                            <?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?>
                                        </td>
                                        <td class="px-5 py-4">
                                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                                <?php echo htmlspecialchars(ucfirst($user['gender'] ?? 'N/A')); ?>
                                            </span>
                                        </td>
                                        <td class="px-5 py-4 text-right">
                                            <a href="search.php?query=<?php echo urlencode($user['email'] ?? ''); ?>" class="inline-flex items-center gap-1.5 text-xs font-bold text-primary hover:text-primary-600 transition">
                                                <span>Inspect</span>
                                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <circle cx="11" cy="11" r="8"></circle>
                                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="px-5 py-8 text-center text-slate-400">
                                        No registered members found in database.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
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