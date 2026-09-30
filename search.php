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

$firstName = $lastName = $email = $gender = $birthdate = $userId = "";
$searched = false;
$found = false;
$message = "";

if (isset($_POST['search']) || isset($_GET['email'])) {
    $search_email = trim($_POST['email'] ?? $_GET['email'] ?? '');
    
    if (!empty($search_email)) {
        $searched = true;
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $search_email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $userId = $row['id'] ?? 'N/A';
            $firstName = $row['first_name'] ?? '';
            $lastName = $row['last_name'] ?? '';
            $email = $row['email'] ?? '';
            $gender = $row['gender'] ?? 'Not specified';
            $birthdate = $row['birthdate'] ?? 'Not specified';
            $found = true;
        } else {
            $message = "No registered user found with the email \"" . htmlspecialchars($search_email) . "\".";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Member - Admin Panel</title>
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

                <!-- Actions -->
                <div class="flex items-center gap-3">
                    <button onclick="toggleDarkMode()" class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 transition" title="Toggle Theme" aria-label="Toggle Theme">
                        <span id="theme-icon-container">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                            </svg>
                        </span>
                    </button>
                    <a href="adminlogin.php" class="px-5 py-2.5 rounded-xl font-semibold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 border border-red-200 dark:border-red-500/20 transition text-sm">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Layout Container (Sidebar + Main Content) -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full flex-grow flex flex-col lg:flex-row gap-8 items-start">

        <!-- Sidebar Navigation -->
        <aside id="admin-sidebar" class="w-full lg:w-72 shrink-0 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-5 shadow-sm space-y-6 transition-all duration-300">
            
            <div class="flex items-center gap-3 px-3 py-2.5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200/60 dark:border-slate-700/60">
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
                    Admin Modules
                </div>

                <a href="adminpanel.php" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-medium text-slate-700 dark:text-slate-200 hover:text-primary dark:hover:text-primary-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 transition group">
                    <div class="w-9 h-9 rounded-xl bg-primary-50 dark:bg-primary/10 text-primary flex items-center justify-center group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                    </div>
                    <span class="text-sm font-semibold">Dashboard</span>
                </a>

                <a href="amininfo.php" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-medium text-slate-700 dark:text-slate-200 hover:text-primary dark:hover:text-primary-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 transition group">
                    <div class="w-9 h-9 rounded-xl bg-primary-50 dark:bg-primary/10 text-primary flex items-center justify-center group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <span class="text-sm font-semibold">Admin Information</span>
                </a>

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

                <a href="search.php" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-semibold text-primary bg-primary-50 dark:bg-primary/10 transition group">
                    <div class="w-9 h-9 rounded-xl bg-primary text-white flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </div>
                    <span class="text-sm font-semibold">Search Member</span>
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

            <!-- Title & Subtitle -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        Search <span class="text-primary">Member</span>
                    </h1>
                    <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm mt-0.5">
                        Lookup user account details, verify credentials and review profile status.
                    </p>
                </div>
                <a href="view.php" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-xs sm:text-sm hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                    </svg>
                    <span>View All Members</span>
                </a>
            </div>

            <!-- Search Card -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm">
                <form action="search.php" method="POST" class="space-y-4">
                    <label for="email" class="block text-sm font-bold text-slate-700 dark:text-slate-200">
                        Search Member by Registered Email
                    </label>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="relative flex-grow">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                            </div>
                            <input type="email" id="email" name="email" required placeholder="Enter member email (e.g. user@example.com)" value="<?php echo isset($search_email) ? htmlspecialchars($search_email) : ''; ?>" class="w-full pl-11 pr-4 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white dark:focus:bg-slate-900 transition text-sm">
                        </div>
                        <button type="submit" name="search" class="px-6 py-3.5 rounded-2xl bg-primary hover:bg-primary-600 text-white font-bold text-sm shadow-md shadow-primary/25 hover:shadow-lg hover:shadow-primary/30 active:scale-[0.98] transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                            <span>Search</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Search Result Section -->
            <?php if ($searched && $found): ?>
                <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6 animate-fade-in">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-green-500/10 text-green-600 dark:text-green-400 flex items-center justify-center">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-extrabold text-slate-900 dark:text-white">Member Found</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Account verified in system database</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-green-50 text-green-600 dark:bg-green-500/10 dark:text-green-400 border border-green-200 dark:border-green-500/20">
                            <span class="w-2 h-2 rounded-full bg-green-500"></span>
                            Active
                        </span>
                    </div>

                    <!-- Member Profile Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="16" rx="2"></rect>
                                    <line x1="7" y1="8" x2="17" y2="8"></line>
                                    <line x1="7" y1="12" x2="17" y2="12"></line>
                                    <line x1="7" y1="16" x2="13" y2="16"></line>
                                </svg>
                            </div>
                            <div>
                                <div class="text-xs text-slate-400 font-bold uppercase tracking-wider">User ID</div>
                                <div class="text-sm font-extrabold text-slate-900 dark:text-white">#<?php echo htmlspecialchars($userId); ?></div>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </div>
                            <div>
                                <div class="text-xs text-slate-400 font-bold uppercase tracking-wider">Full Name</div>
                                <div class="text-sm font-extrabold text-slate-900 dark:text-white"><?php echo htmlspecialchars($firstName . ' ' . $lastName); ?></div>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                            </div>
                            <div class="overflow-hidden">
                                <div class="text-xs text-slate-400 font-bold uppercase tracking-wider">Email Address</div>
                                <div class="text-sm font-extrabold text-slate-900 dark:text-white truncate"><?php echo htmlspecialchars($email); ?></div>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <path d="M12 6v6l4 2"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="text-xs text-slate-400 font-bold uppercase tracking-wider">Gender & Birthdate</div>
                                <div class="text-sm font-extrabold text-slate-900 dark:text-white"><?php echo htmlspecialchars(ucfirst($gender) . ' • ' . $birthdate); ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 flex flex-wrap gap-3">
                        <a href="view.php?search=<?php echo urlencode($email); ?>" class="px-5 py-2.5 rounded-xl bg-primary text-white font-semibold text-xs hover:bg-primary-600 transition flex items-center gap-2">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <span>Open In Members Directory</span>
                        </a>
                        <a href="search.php" class="px-5 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-xs hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                            Reset Search
                        </a>
                    </div>
                </div>
            <?php elseif ($searched && !$found): ?>
                <div class="bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 rounded-3xl p-6 sm:p-8 text-center space-y-3">
                    <div class="w-12 h-12 rounded-2xl bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-400 flex items-center justify-center mx-auto">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-red-900 dark:text-red-200">No Member Found</h3>
                    <p class="text-xs sm:text-sm text-red-700 dark:text-red-400 max-w-md mx-auto">
                        <?php echo $message; ?>
                    </p>
                    <div class="pt-2">
                        <a href="search.php" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 font-semibold text-xs border border-red-200 dark:border-red-500/30 hover:bg-red-50 transition">
                            Try Another Email
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <!-- Initial State Placeholder -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-10 text-center space-y-4">
                    <div class="w-16 h-16 rounded-3xl bg-primary/10 text-primary flex items-center justify-center mx-auto">
                        <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Search Account Database</h3>
                        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 max-w-md mx-auto mt-1">
                            Type any user's registered email address above to view their comprehensive profile record.
                        </p>
                    </div>
                </div>
            <?php endif; ?>

        </main>
    </div>

    <!-- Footer -->
    <footer class="mt-auto border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
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

    <!-- Scripts for Theme & Mobile Menu -->
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
<?php $conn->close(); ?>
