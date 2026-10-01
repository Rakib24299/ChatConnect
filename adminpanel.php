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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - ChatConnect</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            DEFAULT: '#2563eb',
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-slate-100 text-slate-800 dark:bg-slate-900 dark:text-slate-100 min-h-screen flex flex-col font-sans">

    <!-- Navbar -->
    <header class="bg-white dark:bg-slate-800 border-b border-slate-300 dark:border-slate-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo & Mobile Sidebar Trigger -->
                <div class="flex items-center gap-3">
                    <button onclick="toggleAdminSidebar()" class="p-2 border border-slate-300 dark:border-slate-600 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 lg:hidden rounded-none" title="Toggle Sidebar">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18"></rect>
                            <line x1="9" y1="3" x2="9" y2="21"></line>
                        </svg>
                    </button>

                    <a href="adminpanel.php" class="flex items-center gap-2">
                        <div class="w-9 h-9 bg-blue-600 text-white flex items-center justify-center rounded-none font-bold">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-blue-600 dark:text-blue-400">ChatConnect</span>
                    </a>
                </div>

                <!-- Action Buttons & Dark Mode Toggle -->
                <div class="flex items-center gap-2">
                    <button onclick="toggleDarkMode()" class="p-2 border border-slate-300 dark:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-none" title="Toggle Theme">
                        <span id="theme-icon-container">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                            </svg>
                        </span>
                    </button>

                    <a href="adminlogin.php" class="px-3 py-1.5 font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 border border-red-300 dark:border-red-600 text-xs sm:text-sm flex items-center gap-1.5 rounded-none">
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
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 w-full flex-grow flex flex-col lg:flex-row gap-6 items-start">

        <!-- Sidebar Navigation -->
        <aside id="admin-sidebar" class="w-full lg:w-64 shrink-0 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-none p-4 space-y-4">
            
            <!-- Admin Profile Tag -->
            <div class="flex items-center gap-3 p-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-none">
                <div class="w-9 h-9 bg-blue-600 text-white flex items-center justify-center font-bold text-sm rounded-none shrink-0">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    </svg>
                </div>
                <div class="overflow-hidden">
                    <div class="text-sm font-bold text-slate-900 dark:text-white truncate">Administrator</div>
                    <div class="text-xs text-green-600 dark:text-green-400 font-medium">
                        Active Session
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="space-y-1">
                <div class="px-2 py-1 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700 mb-2">
                    Admin Menu
                </div>

                <!-- 1. Dashboard -->
                <a href="adminpanel.php" class="flex items-center gap-2.5 px-3 py-2 font-bold text-blue-600 bg-blue-50 dark:bg-slate-700 dark:text-blue-400 border border-blue-200 dark:border-slate-600 rounded-none text-sm">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <!-- 2. Admin Information -->
                <a href="amininfo.php" class="flex items-center gap-2.5 px-3 py-2 font-medium text-slate-700 dark:text-slate-200 hover:text-blue-600 hover:bg-slate-100 dark:hover:bg-slate-700 border border-transparent hover:border-slate-300 dark:hover:border-slate-600 rounded-none text-sm">
                    <svg class="w-4 h-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    <span>Admin Information</span>
                </a>

                <!-- 3. View Members -->
                <a href="view.php" class="flex items-center gap-2.5 px-3 py-2 font-medium text-slate-700 dark:text-slate-200 hover:text-blue-600 hover:bg-slate-100 dark:hover:bg-slate-700 border border-transparent hover:border-slate-300 dark:hover:border-slate-600 rounded-none text-sm">
                    <svg class="w-4 h-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                    </svg>
                    <span>View Members</span>
                </a>

                <!-- 4. Search Member -->
                <a href="search.php" class="flex items-center gap-2.5 px-3 py-2 font-medium text-slate-700 dark:text-slate-200 hover:text-blue-600 hover:bg-slate-100 dark:hover:bg-slate-700 border border-transparent hover:border-slate-300 dark:hover:border-slate-600 rounded-none text-sm">
                    <svg class="w-4 h-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <span>Search Member</span>
                </a>

            </nav>

            <!-- Quick Links -->
            <div class="pt-3 border-t border-slate-200 dark:border-slate-700 space-y-1">
                <a href="desh.php" class="flex items-center gap-2 px-3 py-1.5 font-medium text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white rounded-none text-xs">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    </svg>
                    <span>Back to Home</span>
                </a>
            </div>

        </aside>

        <!-- Main Content Area -->
        <main class="flex-grow w-full space-y-6">

            <!-- Banner Header -->
            <div class="p-6 bg-blue-600 text-white border border-blue-700 rounded-none flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold">
                        Admin Dashboard Overview
                    </h1>
                    <p class="text-blue-100 text-xs sm:text-sm mt-1">
                        Manage members, database records, and monitor system stats.
                    </p>
                </div>
                <div>
                    <a href="view.php" class="inline-block px-4 py-2 font-bold bg-white text-blue-600 hover:bg-slate-100 text-xs sm:text-sm rounded-none">
                        View Members
                    </a>
                </div>
            </div>

            <!-- 4 Metric Stat Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Stat 1: Registered Users -->
                <div class="p-4 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-none">
                    <span class="text-xs font-bold uppercase text-slate-500 dark:text-slate-400">Total Users</span>
                    <div class="text-2xl font-bold text-slate-900 dark:text-white mt-1">
                        <?php echo number_format($totalUsers); ?>
                    </div>
                </div>

                <!-- Stat 2: Active Ads -->
                <div class="p-4 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-none">
                    <span class="text-xs font-bold uppercase text-slate-500 dark:text-slate-400">Active Ads</span>
                    <div class="text-2xl font-bold text-slate-900 dark:text-white mt-1">
                        <?php echo number_format($totalAds); ?>
                    </div>
                </div>

                <!-- Stat 3: Total Messages -->
                <div class="p-4 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-none">
                    <span class="text-xs font-bold uppercase text-slate-500 dark:text-slate-400">Total Chats</span>
                    <div class="text-2xl font-bold text-slate-900 dark:text-white mt-1">
                        <?php echo number_format($totalChats); ?>
                    </div>
                </div>

                <!-- Stat 4: Total Friendships -->
                <div class="p-4 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-none">
                    <span class="text-xs font-bold uppercase text-slate-500 dark:text-slate-400">Friendships</span>
                    <div class="text-2xl font-bold text-slate-900 dark:text-white mt-1">
                        <?php echo number_format($totalFriends); ?>
                    </div>
                </div>
            </div>

            <!-- Recent Members Table -->
            <div class="bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-none p-5">
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-200 dark:border-slate-700">
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Recently Registered Members</h2>
                    <a href="view.php" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline">
                        View All
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs sm:text-sm text-slate-600 dark:text-slate-300 border-collapse border border-slate-200 dark:border-slate-700">
                        <thead class="bg-slate-50 dark:bg-slate-900 text-xs font-bold text-slate-700 dark:text-slate-300 border-b border-slate-200 dark:border-slate-700">
                            <tr>
                                <th scope="col" class="p-3 border border-slate-200 dark:border-slate-700">ID</th>
                                <th scope="col" class="p-3 border border-slate-200 dark:border-slate-700">Full Name</th>
                                <th scope="col" class="p-3 border border-slate-200 dark:border-slate-700">Email</th>
                                <th scope="col" class="p-3 border border-slate-200 dark:border-slate-700">Gender</th>
                                <th scope="col" class="p-3 border border-slate-200 dark:border-slate-700 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                            <?php if (!empty($recentUsers)): ?>
                                <?php foreach ($recentUsers as $user): ?>
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700">
                                        <td class="p-3 border border-slate-200 dark:border-slate-700 font-bold text-slate-900 dark:text-white">
                                            #<?php echo htmlspecialchars($user['id']); ?>
                                        </td>
                                        <td class="p-3 border border-slate-200 dark:border-slate-700 font-medium text-slate-800 dark:text-white">
                                            <?php echo htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')); ?>
                                        </td>
                                        <td class="p-3 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400">
                                            <?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?>
                                        </td>
                                        <td class="p-3 border border-slate-200 dark:border-slate-700">
                                            <?php echo htmlspecialchars(ucfirst($user['gender'] ?? 'N/A')); ?>
                                        </td>
                                        <td class="p-3 border border-slate-200 dark:border-slate-700 text-center">
                                            <a href="search.php?query=<?php echo urlencode($user['email'] ?? ''); ?>" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline">
                                                Inspect
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="p-4 text-center text-slate-400">
                                        No registered members found.
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
    <footer class="bg-white dark:bg-slate-800 border-t border-slate-300 dark:border-slate-700 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row items-center justify-between h-auto sm:h-16 py-3 sm:py-0 gap-2">
                <!-- Left: Copyright -->
                <div class="text-center sm:text-left">
                    <p class="text-sm text-slate-600 dark:text-slate-400">
                        &copy; 2024 ChatConnect. All rights reserved.
                    </p>
                </div>

                <!-- Right: Developed by -->
                <div class="text-center sm:text-right">
                    <p class="text-sm text-slate-600 dark:text-slate-400">
                        Developed by <span class="font-semibold text-slate-800 dark:text-slate-200">Rakib</span>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts for Dark Mode & Mobile Menu -->
    <script>
        const sunSvg = `<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>`;
        
        const moonSvg = `<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>`;

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
            if (desktopIcon) desktopIcon.innerHTML = isDark ? sunSvg : moonSvg;
        }

        function toggleAdminSidebar() {
            const sidebar = document.getElementById('admin-sidebar');
            sidebar.classList.toggle('hidden');
        }

        initTheme();
    </script>
</body>

</html>