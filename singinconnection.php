<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "messenger_db";

$conn = @new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$isLoggedIn = false;
$user = null;
$totalFriends = 0;
$totalChats = 0;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    $pass = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Check password (supports hash verify or plain match)
        if (password_verify($pass, $row['password']) || $pass === $row['password']) {
            $_SESSION['userId'] = $row['id'];
            $_SESSION['userEmail'] = $row['email'];
            $_SESSION['userName'] = ($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '');
            $user = $row;
            $isLoggedIn = true;
        }
    }
    $stmt->close();
} elseif (isset($_SESSION['userId'])) {
    $userId = $_SESSION['userId'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $row = $result->fetch_assoc()) {
        $user = $row;
        $isLoggedIn = true;
    }
    $stmt->close();
}

if ($isLoggedIn && $user) {
    $userId = $user['id'];
    
    // Count friends
    $sql_count_friends = "SELECT COUNT(*) as totalFriends FROM friends WHERE user_id='$userId'";
    $result_count_friends = $conn->query($sql_count_friends);
    if ($result_count_friends && $row_count = $result_count_friends->fetch_assoc()) {
        $totalFriends = $row_count['totalFriends'];
    }

    // Count user chats
    $userEmail = $user['email'];
    $sql_chats = "SELECT COUNT(*) as totalChats FROM chats WHERE email='$userEmail'";
    $result_chats = $conn->query($sql_chats);
    if ($result_chats && $row_chats = $result_chats->fetch_assoc()) {
        $totalChats = $row_chats['totalChats'];
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isLoggedIn ? 'User Dashboard - ChatConnect' : 'Sign In Failed - ChatConnect'; ?></title>
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
                    <?php if ($isLoggedIn): ?>
                        <button onclick="toggleUserSidebar()" class="p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 lg:hidden transition" title="Toggle Sidebar">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="9" y1="3" x2="9" y2="21"></line>
                            </svg>
                        </button>
                    <?php endif; ?>

                    <a href="singinconnection.php" class="flex items-center gap-3 group">
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

                    <?php if ($isLoggedIn): ?>
                        <a href="logout.php" class="px-5 py-2.5 rounded-xl font-semibold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 border border-red-200 dark:border-red-500/20 transition duration-200 text-sm flex items-center gap-2">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                <polyline points="16 17 21 12 16 7"></polyline>
                                <line x1="21" y1="12" x2="9" y2="12"></line>
                            </svg>
                            <span>Logout</span>
                        </a>
                    <?php else: ?>
                        <a href="singin.php" class="px-5 py-2.5 rounded-xl font-semibold text-white bg-primary hover:bg-primary-600 shadow-md shadow-primary/25 transition duration-200 text-sm">
                            Sign In
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <?php if ($isLoggedIn): ?>

        <!-- Layout Container (Sidebar + Main Content) -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full flex-grow flex flex-col lg:flex-row gap-8 items-start">

            <!-- User Dashboard Sidebar -->
            <aside id="user-sidebar" class="w-full lg:w-72 shrink-0 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-5 shadow-sm space-y-6 transition-all duration-300">
                
                <!-- Profile Summary in Sidebar -->
                <div class="flex items-center gap-3 px-3 py-2.5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200/60 dark:border-slate-700/60">
                    <div class="w-10 h-10 rounded-xl bg-primary text-white flex items-center justify-center font-bold text-base shadow-sm">
                        <?php echo strtoupper(substr($user['first_name'] ?? $user['email'], 0, 1)); ?>
                    </div>
                    <div class="overflow-hidden">
                        <div class="text-sm font-bold text-slate-900 dark:text-white truncate">
                            <?php echo htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')); ?>
                        </div>
                        <div class="text-xs text-emerald-500 font-semibold flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Active User
                        </div>
                    </div>
                </div>

                <!-- Navigation Sidebar Buttons -->
                <nav class="space-y-1.5">
                    <div class="px-3 pb-2 text-[11px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                        Navigation Menu
                    </div>

                    <!-- 1. Messenger Chat -->
                    <a href="chatt.php" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-medium text-slate-700 dark:text-slate-200 hover:text-primary dark:hover:text-primary-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 transition group">
                        <div class="w-9 h-9 rounded-xl bg-primary-50 dark:bg-primary/10 text-primary flex items-center justify-center group-hover:scale-105 transition-transform">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold">Messenger Chat</span>
                    </a>

                    <!-- 2. Discover & Add Friend -->
                    <a href="addfriend.php" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-medium text-slate-700 dark:text-slate-200 hover:text-primary dark:hover:text-primary-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 transition group">
                        <div class="w-9 h-9 rounded-xl bg-primary-50 dark:bg-primary/10 text-primary flex items-center justify-center group-hover:scale-105 transition-transform">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="8.5" cy="7" r="4"></circle>
                                <line x1="20" y1="8" x2="20" y2="14"></line>
                                <line x1="23" y1="11" x2="17" y2="11"></line>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold">Discover & Add Friend</span>
                    </a>

                    <!-- 3. Friends List -->
                    <a href="viewfriend.php" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-medium text-slate-700 dark:text-slate-200 hover:text-primary dark:hover:text-primary-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 transition group">
                        <div class="w-9 h-9 rounded-xl bg-primary-50 dark:bg-primary/10 text-primary flex items-center justify-center group-hover:scale-105 transition-transform">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold">Friends List</span>
                    </a>

                    <!-- 4. Video & Voice Call -->
                    <a href="callingpp.php" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-medium text-slate-700 dark:text-slate-200 hover:text-primary dark:hover:text-primary-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 transition group">
                        <div class="w-9 h-9 rounded-xl bg-primary-50 dark:bg-primary/10 text-primary flex items-center justify-center group-hover:scale-105 transition-transform">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="23 7 16 12 23 17 23 7"></polygon>
                                <rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold">Video & Voice Call</span>
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

                    <!-- 6. Mutual Connections -->
                    <a href="muatual.php" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-medium text-slate-700 dark:text-slate-200 hover:text-primary dark:hover:text-primary-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 transition group">
                        <div class="w-9 h-9 rounded-xl bg-primary-50 dark:bg-primary/10 text-primary flex items-center justify-center group-hover:scale-105 transition-transform">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M17 11v6"></path>
                                <path d="M14 14h6"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold">Mutual Connections</span>
                    </a>
                </nav>

                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 space-y-2">
                    <a href="desh.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl font-medium text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition text-xs">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        </svg>
                        <span>Back to Home</span>
                    </a>
                </div>

            </aside>

            <!-- Main Content Area -->
            <main class="flex-grow w-full space-y-8">
                
                <!-- User Profile Welcome Card -->
                <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-r from-primary via-primary-600 to-primary-700 text-white shadow-xl shadow-primary/25 flex flex-col sm:flex-row sm:items-center justify-between gap-6 relative overflow-hidden">
                    <div class="absolute -right-8 -bottom-8 w-48 h-48 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
                    <div class="relative z-10 flex items-center gap-5">
                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-white text-primary flex items-center justify-center font-black text-2xl sm:text-3xl shadow-lg shrink-0">
                            <?php echo strtoupper(substr($user['first_name'] ?? $user['email'], 0, 1)); ?>
                        </div>
                        <div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-white/20 text-white mb-2 backdrop-blur-sm">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> Online Now
                            </span>
                            <h1 class="text-2xl sm:text-3xl font-black tracking-tight">
                                Welcome, <?php echo htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')); ?>!
                            </h1>
                            <p class="text-primary-100 text-sm mt-0.5"><?php echo htmlspecialchars($user['email']); ?></p>
                        </div>
                    </div>
                    <div class="relative z-10 flex items-center gap-3">
                        <a href="chatt.php" class="px-6 py-3 rounded-2xl font-bold bg-white text-primary hover:bg-slate-100 shadow-md transition duration-200 text-sm flex items-center gap-2">
                            <span>Open Messenger</span>
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Stats Overview -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <!-- Total Friends -->
                    <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-primary-50 dark:bg-primary/10 text-primary flex items-center justify-center shrink-0">
                            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="text-3xl font-black text-slate-900 dark:text-white"><?php echo $totalFriends; ?></div>
                            <div class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Friends</div>
                        </div>
                    </div>

                    <!-- Direct Chat -->
                    <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-primary-50 dark:bg-primary/10 text-primary flex items-center justify-center shrink-0">
                            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="text-3xl font-black text-slate-900 dark:text-white">Active</div>
                            <div class="text-sm font-medium text-slate-500 dark:text-slate-400">Messenger Chat</div>
                        </div>
                    </div>

                    <!-- Audio Video Call -->
                    <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-primary-50 dark:bg-primary/10 text-primary flex items-center justify-center shrink-0">
                            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="23 7 16 12 23 17 23 7"></polygon>
                                <rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect>
                            </svg>
                        </div>
                        <div>
                            <div class="text-3xl font-black text-slate-900 dark:text-white">HD Call</div>
                            <div class="text-sm font-medium text-slate-500 dark:text-slate-400">Voice & Video Calling</div>
                        </div>
                    </div>
                </div>

                <!-- Account Profile & Details Card -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">Account Information</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Personal details & profile settings</p>
                            </div>
                        </div>
                        <span class="text-xs font-semibold px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                            Member ID #<?php echo htmlspecialchars($user['id']); ?>
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
                            <div class="text-xs text-slate-400 font-bold uppercase tracking-wider">Full Name</div>
                            <div class="text-sm font-extrabold text-slate-900 dark:text-white mt-1">
                                <?php echo htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')); ?>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
                            <div class="text-xs text-slate-400 font-bold uppercase tracking-wider">Email Address</div>
                            <div class="text-sm font-extrabold text-slate-900 dark:text-white mt-1 truncate">
                                <?php echo htmlspecialchars($user['email']); ?>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
                            <div class="text-xs text-slate-400 font-bold uppercase tracking-wider">Gender & Birthday</div>
                            <div class="text-sm font-extrabold text-slate-900 dark:text-white mt-1">
                                <?php echo htmlspecialchars(ucfirst($user['gender'] ?? 'Not set')) . ' • ' . htmlspecialchars($user['birthdate'] ?? 'N/A'); ?>
                            </div>
                        </div>
                    </div>
                </div>

            </main>
        </div>

    <?php else: ?>

        <!-- Error View (Not Logged In) -->
        <main class="flex-grow flex items-center justify-center p-6">
            <div class="max-w-md w-full text-center">
                <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-8 sm:p-10 shadow-xl space-y-6">
                    <div class="w-20 h-20 mx-auto rounded-3xl bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 flex items-center justify-center shadow-inner">
                        <svg class="w-10 h-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white">Invalid Credentials</h2>
                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">The email or password you entered is incorrect. Please try again.</p>
                    </div>
                    <div class="pt-2 flex flex-col gap-3">
                        <a href="singin.php" class="w-full py-3.5 px-4 rounded-xl font-bold text-white bg-primary hover:bg-primary-600 shadow-lg shadow-primary/25 transition flex items-center justify-center gap-2">
                            <span>Back to Sign In</span>
                        </a>
                        <a href="singup.php" class="text-sm font-semibold text-primary hover:underline">
                            Don't have an account? Sign Up
                        </a>
                    </div>
                </div>
            </div>
        </main>

    <?php endif; ?>

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

    <!-- Scripts for Theme & Mobile Sidebar -->
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

        function toggleUserSidebar() {
            const sidebar = document.getElementById('user-sidebar');
            if (sidebar) sidebar.classList.toggle('hidden');
        }

        initTheme();
    </script>
</body>

</html>
<?php $conn->close(); ?>
