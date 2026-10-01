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
<html lang="en">

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

                <!-- Actions -->
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
                <a href="adminpanel.php" class="flex items-center gap-2.5 px-3 py-2 font-medium text-slate-700 dark:text-slate-200 hover:text-blue-600 hover:bg-slate-100 dark:hover:bg-slate-700 border border-transparent hover:border-slate-300 dark:hover:border-slate-600 rounded-none text-sm">
                    <svg class="w-4 h-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
                <a href="search.php" class="flex items-center gap-2.5 px-3 py-2 font-bold text-blue-600 bg-blue-50 dark:bg-slate-700 dark:text-blue-400 border border-blue-200 dark:border-slate-600 rounded-none text-sm">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <span>Search Member</span>
                </a>

            </nav>

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

            <!-- Title & Subtitle -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-300 dark:border-slate-700 pb-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                        Search Member
                    </h1>
                    <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm mt-0.5">
                        Lookup user account details, verify credentials and review profile status.
                    </p>
                </div>
                <a href="view.php" class="inline-flex items-center gap-1.5 px-3 py-2 bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-200 text-xs font-semibold rounded-none border border-slate-300 dark:border-slate-600">
                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                    </svg>
                    <span>View All Members</span>
                </a>
            </div>

            <!-- Search Card -->
            <div class="bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-none p-5">
                <form action="search.php" method="POST" class="space-y-3">
                    <label for="email" class="block text-sm font-bold text-slate-700 dark:text-slate-200">
                        Search Member by Registered Email:
                    </label>
                    <div class="flex flex-col sm:flex-row gap-2">
                        <div class="relative flex-grow">
                            <input type="email" id="email" name="email" required placeholder="Enter member email (e.g. user@example.com)" value="<?php echo isset($search_email) ? htmlspecialchars($search_email) : ''; ?>" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white rounded-none focus:outline-none focus:border-blue-500 text-sm">
                        </div>
                        <button type="submit" name="search" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-none flex items-center justify-center gap-1.5">
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
                <div class="bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-none p-5 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-700">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300 flex items-center justify-center rounded-none font-bold">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">Member Found</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Account verified in system database</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 border border-green-300 dark:border-green-700 rounded-none">
                            Active
                        </span>
                    </div>

                    <!-- Member Profile Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div class="p-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-none flex items-center gap-3">
                            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center rounded-none shrink-0">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="16"></rect>
                                    <line x1="7" y1="8" x2="17" y2="8"></line>
                                    <line x1="7" y1="12" x2="17" y2="12"></line>
                                    <line x1="7" y1="16" x2="13" y2="16"></line>
                                </svg>
                            </div>
                            <div>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider">User ID</div>
                                <div class="text-sm font-bold text-slate-900 dark:text-white">#<?php echo htmlspecialchars($userId); ?></div>
                            </div>
                        </div>

                        <div class="p-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-none flex items-center gap-3">
                            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center rounded-none shrink-0">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </div>
                            <div>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider">Full Name</div>
                                <div class="text-sm font-bold text-slate-900 dark:text-white"><?php echo htmlspecialchars($firstName . ' ' . $lastName); ?></div>
                            </div>
                        </div>

                        <div class="p-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-none flex items-center gap-3">
                            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center rounded-none shrink-0">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                            </div>
                            <div class="overflow-hidden">
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider">Email Address</div>
                                <div class="text-sm font-bold text-slate-900 dark:text-white truncate"><?php echo htmlspecialchars($email); ?></div>
                            </div>
                        </div>

                        <div class="p-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-none flex items-center gap-3">
                            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center rounded-none shrink-0">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <path d="M12 6v6l4 2"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider">Gender & Birthdate</div>
                                <div class="text-sm font-bold text-slate-900 dark:text-white"><?php echo htmlspecialchars(ucfirst($gender) . ' • ' . $birthdate); ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-200 dark:border-slate-700 flex flex-wrap gap-2">
                        <a href="view.php?search=<?php echo urlencode($email); ?>" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-none flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <span>Open In Members Directory</span>
                        </a>
                        <a href="search.php" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-200 font-semibold text-xs rounded-none border border-slate-300 dark:border-slate-600">
                            Reset Search
                        </a>
                    </div>
                </div>
            <?php elseif ($searched && !$found): ?>
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-300 dark:border-red-700 rounded-none p-5 text-center space-y-2">
                    <div class="w-10 h-10 bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400 flex items-center justify-center mx-auto rounded-none">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-red-900 dark:text-red-200">No Member Found</h3>
                    <p class="text-xs text-red-700 dark:text-red-400 max-w-md mx-auto">
                        <?php echo $message; ?>
                    </p>
                    <div class="pt-1">
                        <a href="search.php" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-semibold text-xs border border-red-300 dark:border-red-700 hover:bg-red-100 rounded-none">
                            Try Another Email
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <!-- Initial State Placeholder -->
                <div class="bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-none p-8 text-center space-y-2">
                    <div class="w-12 h-12 bg-blue-50 dark:bg-slate-700 text-blue-600 dark:text-blue-400 flex items-center justify-center mx-auto rounded-none border border-slate-200 dark:border-slate-600">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Search Account Database</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 max-w-md mx-auto mt-0.5">
                            Type any user's registered email address above to view their profile details.
                        </p>
                    </div>
                </div>
            <?php endif; ?>

        </main>
    </div>

    <!-- Footer -->
    <footer class="mt-auto border-t border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-2">
                <!-- Left: Copyright -->
                <div class="text-center sm:text-left">
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400">
                        &copy; 2024 ChatConnect. All rights reserved.
                    </p>
                </div>

                <!-- Right: Developed by -->
                <div class="text-center sm:text-right">
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400">
                        Developed by <span class="font-bold text-slate-800 dark:text-slate-200">Rakib</span>
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
