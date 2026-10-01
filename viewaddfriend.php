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
<html lang="en">

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
                <!-- Logo -->
                <a href="singinconnection.php" class="flex items-center gap-2">
                    <div class="w-9 h-9 bg-blue-600 text-white flex items-center justify-center rounded-none font-bold">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-blue-600 dark:text-blue-400">
                        ChatConnect
                    </span>
                </a>

                <!-- Actions -->
                <div class="flex items-center gap-2">
                    <button onclick="toggleDarkMode()" class="p-2 border border-slate-300 dark:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-none" title="Toggle Theme">
                        <span id="theme-icon-container">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                            </svg>
                        </span>
                    </button>
                    <a href="singinconnection.php" class="px-3 py-1.5 font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 border border-slate-300 dark:border-slate-600 text-xs sm:text-sm flex items-center gap-1.5 rounded-none">
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
    <main class="flex-grow py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Page Header -->
            <div class="text-center max-w-xl mx-auto mb-6">
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                    Search Community Members
                </h1>
                <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm mt-1">
                    Enter a name or email to find and connect with friends.
                </p>
            </div>

            <!-- Search Bar Form -->
            <form action="viewaddfriend.php" method="POST" class="mb-6">
                <div class="p-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-none flex flex-col sm:flex-row items-center gap-2">
                    <input type="text" name="searchTerm" value="<?php echo htmlspecialchars($searchTerm ?? ''); ?>" placeholder="Search by name or email address..." required class="w-full px-3 py-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-white placeholder-slate-400 border border-slate-300 dark:border-slate-600 rounded-none text-xs sm:text-sm focus:outline-none focus:border-blue-600">
                    <button type="submit" name="search" class="w-full sm:w-auto px-6 py-2 font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-none text-xs sm:text-sm shrink-0">
                        Search
                    </button>
                </div>
            </form>

            <!-- Results Area -->
            <?php if ($searched): ?>
                <?php if (!empty($searchResults)): ?>
                    <div class="space-y-3">
                        <h2 class="text-sm font-bold text-slate-700 dark:text-slate-300">
                            Search Results (<?php echo count($searchResults); ?>):
                        </h2>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <?php foreach ($searchResults as $userRow): ?>
                                <?php 
                                    $targetId = $userRow['id'];
                                    $fullName = trim(($userRow['first_name'] ?? '') . ' ' . ($userRow['last_name'] ?? ''));
                                    if (empty($fullName)) $fullName = $userRow['email'];
                                    $isFriend = in_array($targetId, $existingFriends);
                                ?>
                                <div class="p-3 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-none flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-3 overflow-hidden">
                                        <div class="w-10 h-10 bg-blue-100 dark:bg-slate-700 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold text-sm rounded-none shrink-0">
                                            <?php echo strtoupper(substr($userRow['first_name'] ?? $userRow['email'], 0, 1)); ?>
                                        </div>
                                        <div class="overflow-hidden">
                                            <h3 class="font-bold text-slate-900 dark:text-white text-xs sm:text-sm truncate">
                                                <?php echo htmlspecialchars($fullName); ?>
                                            </h3>
                                            <p class="text-xs text-slate-400 truncate"><?php echo htmlspecialchars($userRow['email']); ?></p>
                                        </div>
                                    </div>

                                    <div class="shrink-0">
                                        <?php if ($isFriend): ?>
                                            <span class="px-2.5 py-1 text-xs font-bold text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-none">
                                                Friends
                                            </span>
                                        <?php else: ?>
                                            <a href="addfriendprocess.php?friendId=<?php echo $targetId; ?>" class="px-3 py-1 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-none">
                                                Add Friend
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="p-8 text-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-none">
                        <p class="text-slate-500 text-xs sm:text-sm">No member found matching "<strong><?php echo htmlspecialchars($searchTerm); ?></strong>".</p>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

        </div>
    </main>

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
