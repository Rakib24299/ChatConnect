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
        // Check password (supports verify or plain text match)
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
                <!-- Logo -->
                <a href="desh.php" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-2xl bg-primary text-white flex items-center justify-center shadow-lg shadow-primary/30 group-hover:scale-105 transition-transform duration-200">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                        </svg>
                    </div>
                    <span class="text-2xl font-black tracking-tight text-primary">
                        ChatConnect
                    </span>
                </a>

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

    <!-- Main Content -->
    <main class="flex-grow py-10 lg:py-14">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <?php if ($isLoggedIn): ?>
                
                <!-- User Profile Welcome Card -->
                <div class="p-8 sm:p-10 rounded-3xl bg-gradient-to-r from-primary via-primary-600 to-primary-700 text-white shadow-xl shadow-primary/25 mb-10 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                    <div class="flex items-center gap-5">
                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-white text-primary flex items-center justify-center font-black text-2xl sm:text-3xl shadow-lg">
                            <?php echo strtoupper(substr($user['first_name'] ?? $user['email'], 0, 1)); ?>
                        </div>
                        <div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-white/20 text-white mb-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> Online Now
                            </span>
                            <h1 class="text-2xl sm:text-3xl font-black tracking-tight">
                                Welcome, <?php echo htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')); ?>!
                            </h1>
                            <p class="text-primary-100 text-sm mt-0.5"><?php echo htmlspecialchars($user['email']); ?></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="chatt.php" class="px-6 py-3 rounded-2xl font-bold bg-white text-primary hover:bg-slate-100 shadow-md transition duration-200 text-sm flex items-center gap-2">
                            <span>Open Messenger Chat</span>
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Stats Overview -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-10">
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

                <!-- Navigation Module Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Option 1: Chat -->
                    <a href="chatt.php" class="p-7 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 hover:border-primary/50 hover:shadow-xl hover:shadow-primary/5 transition duration-300 group flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-primary-50 dark:bg-primary/10 text-primary flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1 group-hover:text-primary transition-colors">
                                Messenger Chat
                            </h3>
                            <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm">
                                Send instant messages, photos, and chat in groups with your friends.
                            </p>
                        </div>
                        <div class="mt-6 flex items-center gap-2 text-xs font-bold text-primary">
                            <span>Open Chat</span>
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </div>
                    </a>

                    <!-- Option 2: Add Friend -->
                    <a href="addfriend.php" class="p-7 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 hover:border-primary/50 hover:shadow-xl hover:shadow-primary/5 transition duration-300 group flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-primary-50 dark:bg-primary/10 text-primary flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="8.5" cy="7" r="4"></circle>
                                    <line x1="20" y1="8" x2="20" y2="14"></line>
                                    <line x1="23" y1="11" x2="17" y2="11"></line>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1 group-hover:text-primary transition-colors">
                                Discover & Add Friends
                            </h3>
                            <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm">
                                Find other registered users in the network and send friend requests.
                            </p>
                        </div>
                        <div class="mt-6 flex items-center gap-2 text-xs font-bold text-primary">
                            <span>Add New Friends</span>
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </div>
                    </a>

                    <!-- Option 3: View Friends -->
                    <a href="viewfriend.php" class="p-7 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 hover:border-primary/50 hover:shadow-xl hover:shadow-primary/5 transition duration-300 group flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-primary-50 dark:bg-primary/10 text-primary flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1 group-hover:text-primary transition-colors">
                                Friends List
                            </h3>
                            <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm">
                                View your connected friends and see mutual relationships.
                            </p>
                        </div>
                        <div class="mt-6 flex items-center gap-2 text-xs font-bold text-primary">
                            <span>View All Friends</span>
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </div>
                    </a>

                    <!-- Option 4: Video & Voice Calling -->
                    <a href="callingpp.php" class="p-7 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 hover:border-primary/50 hover:shadow-xl hover:shadow-primary/5 transition duration-300 group flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-primary-50 dark:bg-primary/10 text-primary flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polygon points="23 7 16 12 23 17 23 7"></polygon>
                                    <rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1 group-hover:text-primary transition-colors">
                                Video & Voice Call
                            </h3>
                            <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm">
                                Start high-quality interactive audio and video calls.
                            </p>
                        </div>
                        <div class="mt-6 flex items-center gap-2 text-xs font-bold text-primary">
                            <span>Start Calling</span>
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </div>
                    </a>

                    <!-- Option 5: Advertisements -->
                    <a href="ads.php" class="p-7 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 hover:border-primary/50 hover:shadow-xl hover:shadow-primary/5 transition duration-300 group flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-primary-50 dark:bg-primary/10 text-primary flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                                    <line x1="8" y1="21" x2="16" y2="21"></line>
                                    <line x1="12" y1="17" x2="12" y2="21"></line>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1 group-hover:text-primary transition-colors">
                                Advertisements
                            </h3>
                            <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm">
                                Explore and create promotional campaign banners.
                            </p>
                        </div>
                        <div class="mt-6 flex items-center gap-2 text-xs font-bold text-primary">
                            <span>View Ads</span>
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </div>
                    </a>

                    <!-- Option 6: Mutual Friends -->
                    <a href="muatual.php" class="p-7 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 hover:border-primary/50 hover:shadow-xl hover:shadow-primary/5 transition duration-300 group flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-primary-50 dark:bg-primary/10 text-primary flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M17 11v6"></path>
                                    <path d="M14 14h6"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1 group-hover:text-primary transition-colors">
                                Mutual Connections
                            </h3>
                            <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm">
                                Find mutual connections shared among your friends network.
                            </p>
                        </div>
                        <div class="mt-6 flex items-center gap-2 text-xs font-bold text-primary">
                            <span>Check Mutuals</span>
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </div>
                    </a>
                </div>

            <?php else: ?>
                <!-- Error View -->
                <div class="max-w-md mx-auto text-center">
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
            <?php endif; ?>

        </div>
    </main>

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

    <!-- Scripts for Theme -->
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

        initTheme();
    </script>
</body>

</html>
<?php $conn->close(); ?>
