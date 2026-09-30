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

$userId = $_SESSION['userId'] ?? 0;
$searchResults = [];
$searched = false;

if (isset($_POST['search']) || isset($_GET['q'])) {
    $searchTerm = trim($_POST['searchTerm'] ?? $_GET['q'] ?? '');
    $searched = true;

    if (!empty($searchTerm)) {
        $searchTermSql = "%$searchTerm%";
        $stmt = $conn->prepare("SELECT * FROM users WHERE (email LIKE ? OR first_name LIKE ? OR last_name LIKE ?) AND id != ?");
        $stmt->bind_param("sssi", $searchTermSql, $searchTermSql, $searchTermSql, $userId);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res) {
            while ($r = $res->fetch_assoc()) {
                $searchResults[] = $r;
            }
        }
        $stmt->close();
    }
}

// Fetch current user friends
$existingFriends = [];
if ($userId > 0) {
    $fRes = $conn->query("SELECT friend_id FROM friends WHERE user_id = '$userId'");
    if ($fRes) {
        while ($f = $fRes->fetch_assoc()) {
            $existingFriends[] = $f['friend_id'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Friends - ChatConnect</title>
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
                <a href="singinconnection.php" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-2xl bg-primary text-white flex items-center justify-center shadow-lg shadow-primary/30 group-hover:scale-105 transition-transform duration-200">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                        </svg>
                    </div>
                    <span class="text-2xl font-black tracking-tight text-primary">
                        ChatConnect
                    </span>
                </a>

                <!-- Actions -->
                <div class="flex items-center gap-3">
                    <button onclick="toggleDarkMode()" class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 transition" title="Toggle Theme" aria-label="Toggle Theme">
                        <span id="theme-icon-container">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                            </svg>
                        </span>
                    </button>
                    <a href="singinconnection.php" class="px-4 py-2.5 rounded-xl font-semibold text-slate-700 dark:text-slate-200 hover:text-primary dark:hover:text-white bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 transition duration-200 text-xs sm:text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        <span>Back to Dashboard</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow py-10 lg:py-14">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Page Header -->
            <div class="text-center max-w-xl mx-auto mb-10">
                <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    Search <span class="text-primary">Community</span> Members
                </h1>
                <p class="text-slate-500 dark:text-slate-400 text-sm mt-2">
                    Enter a name or email to discover friends and add them to your ChatConnect network.
                </p>
            </div>

            <!-- Search Bar Form -->
            <form action="viewaddfriend.php" method="POST" class="mb-10">
                <div class="p-2 sm:p-3 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xl shadow-slate-200/50 dark:shadow-none flex flex-col sm:flex-row items-center gap-3">
                    <div class="relative flex-grow w-full">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                        </div>
                        <input type="text" name="searchTerm" value="<?php echo htmlspecialchars($searchTerm ?? ''); ?>" placeholder="Search by name or email address..." required class="w-full pl-12 pr-4 py-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary border-0 text-sm">
                    </div>
                    <button type="submit" name="search" class="w-full sm:w-auto px-8 py-3.5 rounded-2xl font-bold text-white bg-primary hover:bg-primary-600 shadow-md shadow-primary/25 transition duration-200 flex items-center justify-center gap-2 shrink-0">
                        <span>Search</span>
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </button>
                </div>
            </form>

            <!-- Results Area -->
            <?php if ($searched): ?>
                <?php if (!empty($searchResults)): ?>
                    <div class="space-y-4">
                        <h2 class="text-base font-bold text-slate-700 dark:text-slate-300">
                            Found <?php echo count($searchResults); ?> result(s):
                        </h2>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <?php foreach ($searchResults as $userRow): ?>
                                <?php 
                                    $targetId = $userRow['id'];
                                    $fullName = trim(($userRow['first_name'] ?? '') . ' ' . ($userRow['last_name'] ?? ''));
                                    if (empty($fullName)) $fullName = $userRow['email'];
                                    $isFriend = in_array($targetId, $existingFriends);
                                ?>
                                <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-4 overflow-hidden">
                                        <div class="w-12 h-12 rounded-2xl bg-primary-50 dark:bg-primary/10 text-primary flex items-center justify-center font-bold text-lg shrink-0">
                                            <?php echo strtoupper(substr($userRow['first_name'] ?? $userRow['email'], 0, 1)); ?>
                                        </div>
                                        <div class="overflow-hidden">
                                            <h3 class="font-bold text-slate-900 dark:text-white text-sm truncate">
                                                <?php echo htmlspecialchars($fullName); ?>
                                            </h3>
                                            <p class="text-xs text-slate-400 truncate"><?php echo htmlspecialchars($userRow['email']); ?></p>
                                            <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400">
                                                Gender: <?php echo htmlspecialchars(ucfirst($userRow['gender'] ?? 'N/A')); ?>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="shrink-0">
                                        <?php if ($isFriend): ?>
                                            <span class="px-3 py-1.5 rounded-xl text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10">
                                                Connected
                                            </span>
                                        <?php else: ?>
                                            <a href="addfriendprocess.php?friendId=<?php echo $targetId; ?>" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-primary hover:bg-primary-600 shadow-sm transition flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                                </svg>
                                                <span>Add</span>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="p-12 text-center bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800">
                        <p class="text-slate-400 text-sm">No member found matching "<strong><?php echo htmlspecialchars($searchTerm); ?></strong>".</p>
                    </div>
                <?php endif; ?>
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
