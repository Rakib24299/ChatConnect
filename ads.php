<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$database = "messenger_db";

$conn = @new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$alertMessage = "";
$alertType = "";

// Insert new advertisement
if (isset($_POST["submit"])) {
    $person_name = trim($_POST["person_name"]);
    $email = trim($_POST["email"]);
    $name = trim($_POST["name"]);
    $price = trim($_POST["price"]);
    $fees = trim($_POST["fees"]);
    $date = date("Y-m-d");
    $image = "";

    if ($_FILES["image"]["error"] == 4) {
        $alertMessage = "Please select an image for your advertisement.";
        $alertType = "error";
    } else {
        $fileName = $_FILES["image"]["name"];
        $fileSize = $_FILES["image"]["size"];
        $tmpName = $_FILES["image"]["tmp_name"];

        $validImageExtension = ['jpg', 'jpeg', 'png', 'webp'];
        $imageExtension = explode('.', $fileName);
        $imageExtension = strtolower(end($imageExtension));
        
        if (!in_array($imageExtension, $validImageExtension)) {
            $alertMessage = "Invalid image extension. Only JPG, JPEG, PNG, WEBP allowed.";
            $alertType = "error";
        } elseif ($fileSize > 2000000) {
            $alertMessage = "Image size is too large (Max 2MB allowed).";
            $alertType = "error";
        } else {
            if (!is_dir('img')) {
                mkdir('img', 0777, true);
            }
            $newImageName = uniqid() . '.' . $imageExtension;
            move_uploaded_file($tmpName, 'img/' . $newImageName);
            $image = $newImageName;

            $stmt = $conn->prepare("INSERT INTO tb_ads (person_name, email, name, price, fees, Date, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("sssssss", $person_name, $email, $name, $price, $fees, $date, $image);
                $stmt->execute();
                $stmt->close();
                $alertMessage = "Advertisement successfully published!";
                $alertType = "success";
            }
        }
    }
}

// Fetch ads
$rows = $conn->query("SELECT * FROM tb_ads ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advertisements Hub - ChatConnect</title>
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
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Alert Notification (if any) -->
            <?php if (!empty($alertMessage)): ?>
                <div class="mb-8 p-4 rounded-2xl <?php echo $alertType === 'success' ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20' : 'bg-red-50 dark:bg-red-500/10 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-500/20'; ?> text-sm font-semibold flex items-center gap-3">
                    <?php if ($alertType === 'success'): ?>
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    <?php else: ?>
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                        </svg>
                    <?php endif; ?>
                    <span><?php echo htmlspecialchars($alertMessage); ?></span>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- Left Column: Create Ad Form -->
                <div class="lg:col-span-5 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm">
                    <div class="mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-primary-50 dark:bg-primary/10 text-primary flex items-center justify-center mb-3">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                                <line x1="8" y1="21" x2="16" y2="21"></line>
                                <line x1="12" y1="17" x2="12" y2="21"></line>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white">Post New Advertisement</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Publish your product banner or promotional campaign.</p>
                    </div>

                    <form action="ads.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">
                                Advertiser Name
                            </label>
                            <input type="text" name="person_name" required placeholder="Your full name" class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary text-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">
                                Contact Email
                            </label>
                            <input type="email" name="email" required placeholder="name@example.com" class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary text-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">
                                Ad Title / Product
                            </label>
                            <input type="text" name="name" required placeholder="e.g. Premium Messenger Banner" class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary text-sm">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">
                                    Price ($)
                                </label>
                                <input type="text" name="price" required placeholder="e.g. 50" class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">
                                    Fees ($)
                                </label>
                                <input type="text" name="fees" required placeholder="e.g. 5" class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary text-sm">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">
                                Campaign Banner Image
                            </label>
                            <input type="file" name="image" accept=".jpg, .jpeg, .png, .webp" required class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary hover:file:bg-primary-100 dark:file:bg-primary/20 dark:file:text-primary-300">
                        </div>

                        <div class="pt-2">
                            <button type="submit" name="submit" class="w-full py-3.5 px-4 rounded-xl font-bold text-white bg-primary hover:bg-primary-600 shadow-md shadow-primary/25 transition duration-200 flex items-center justify-center gap-2">
                                <span>Publish Advertisement</span>
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Right Column: Active Advertisements Gallery -->
                <div class="lg:col-span-7 space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Active Advertisements</h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Current live promotional campaigns on ChatConnect</p>
                        </div>
                    </div>

                    <?php if ($rows && $rows->num_rows > 0): ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <?php while ($ad = $rows->fetch_assoc()): ?>
                                <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl overflow-hidden shadow-sm hover:shadow-md transition flex flex-col justify-between">
                                    
                                    <!-- Image Preview -->
                                    <div class="h-44 w-full bg-slate-100 dark:bg-slate-800 overflow-hidden relative">
                                        <?php if (!empty($ad['image']) && file_exists('img/' . $ad['image'])): ?>
                                            <img src="img/<?php echo htmlspecialchars($ad['image']); ?>" alt="Ad Banner" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <div class="w-full h-full flex items-center justify-center text-slate-400">
                                                <svg class="w-12 h-12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                                    <polyline points="21 15 16 10 5 21"></polyline>
                                                </svg>
                                            </div>
                                        <?php endif; ?>
                                        <div class="absolute top-3 right-3 px-3 py-1 rounded-full text-xs font-bold bg-white/90 dark:bg-slate-900/90 text-primary shadow-sm backdrop-blur-sm">
                                            $<?php echo htmlspecialchars($ad['price']); ?>
                                        </div>
                                    </div>

                                    <!-- Ad Content -->
                                    <div class="p-5 flex-grow flex flex-col justify-between space-y-4">
                                        <div>
                                            <h3 class="font-bold text-slate-900 dark:text-white text-base truncate">
                                                <?php echo htmlspecialchars($ad['name']); ?>
                                            </h3>
                                            <p class="text-xs text-slate-400 mt-1">
                                                By <?php echo htmlspecialchars($ad['person_name']); ?> &bull; <?php echo htmlspecialchars($ad['Date'] ?? $ad['date'] ?? date('Y-m-d')); ?>
                                            </p>
                                        </div>

                                        <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                                            <span class="text-slate-500 dark:text-slate-400">
                                                Fee: <strong>$<?php echo htmlspecialchars($ad['fees']); ?></strong>
                                            </span>
                                            <a href="mailto:<?php echo htmlspecialchars($ad['email']); ?>" class="font-bold text-primary hover:underline flex items-center gap-1">
                                                <span>Contact</span>
                                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>

                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="p-12 text-center bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800">
                            <p class="text-slate-400 text-sm">No active advertisements found. Be the first to publish!</p>
                        </div>
                    <?php endif; ?>
                </div>

            </div>

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
