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
<html lang="en">

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
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Alert Notification -->
            <?php if (!empty($alertMessage)): ?>
                <div class="mb-6 p-3 rounded-none <?php echo $alertType === 'success' ? 'bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-400 border border-green-300 dark:border-green-800' : 'bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400 border border-red-300 dark:border-red-800'; ?> text-xs sm:text-sm font-semibold flex items-center gap-2">
                    <span><?php echo htmlspecialchars($alertMessage); ?></span>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                
                <!-- Left Column: Create Ad Form -->
                <div class="lg:col-span-5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-none p-5">
                    <div class="mb-4">
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Post New Advertisement</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Fill in details to post an advertisement.</p>
                    </div>

                    <form action="ads.php" method="POST" enctype="multipart/form-data" class="space-y-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Advertiser Name
                            </label>
                            <input type="text" name="person_name" required placeholder="Full Name" class="w-full px-3 py-2 rounded-none border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-blue-600 text-xs sm:text-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Contact Email
                            </label>
                            <input type="email" name="email" required placeholder="user@example.com" class="w-full px-3 py-2 rounded-none border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-blue-600 text-xs sm:text-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Ad Title / Product Name
                            </label>
                            <input type="text" name="name" required placeholder="Product Title" class="w-full px-3 py-2 rounded-none border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-blue-600 text-xs sm:text-sm">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                    Price ($)
                                </label>
                                <input type="text" name="price" required placeholder="50" class="w-full px-3 py-2 rounded-none border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-blue-600 text-xs sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                    Fees ($)
                                </label>
                                <input type="text" name="fees" required placeholder="5" class="w-full px-3 py-2 rounded-none border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-blue-600 text-xs sm:text-sm">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Banner Image
                            </label>
                            <input type="file" name="image" accept=".jpg, .jpeg, .png, .webp" required class="w-full text-xs text-slate-500 rounded-none file:mr-2 file:py-1.5 file:px-3 file:border file:border-slate-300 file:bg-slate-100 file:text-slate-700 file:rounded-none">
                        </div>

                        <div class="pt-2">
                            <button type="submit" name="submit" class="w-full py-2 px-4 font-bold text-white bg-blue-600 hover:bg-blue-700 text-xs sm:text-sm rounded-none">
                                Publish Advertisement
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Right Column: Active Advertisements Gallery -->
                <div class="lg:col-span-7 space-y-4">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white">Active Advertisements</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">All live advertisements published on ChatConnect.</p>
                    </div>

                    <?php if ($rows && $rows->num_rows > 0): ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <?php while ($ad = $rows->fetch_assoc()): ?>
                                <div class="bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-none overflow-hidden flex flex-col justify-between">
                                    
                                    <!-- Image Preview -->
                                    <div class="h-40 w-full bg-slate-100 dark:bg-slate-900 overflow-hidden relative border-b border-slate-200 dark:border-slate-700">
                                        <?php if (!empty($ad['image']) && file_exists('img/' . $ad['image'])): ?>
                                            <img src="img/<?php echo htmlspecialchars($ad['image']); ?>" alt="Ad Banner" class="w-full h-full object-cover rounded-none">
                                        <?php else: ?>
                                            <div class="w-full h-full flex items-center justify-center text-slate-400 text-xs">
                                                No Image
                                            </div>
                                        <?php endif; ?>
                                        <div class="absolute top-2 right-2 px-2 py-0.5 text-xs font-bold bg-white dark:bg-slate-900 text-blue-600 border border-slate-300 dark:border-slate-700 rounded-none">
                                            $<?php echo htmlspecialchars($ad['price']); ?>
                                        </div>
                                    </div>

                                    <!-- Ad Content -->
                                    <div class="p-4 flex-grow flex flex-col justify-between space-y-3">
                                        <div>
                                            <h3 class="font-bold text-slate-900 dark:text-white text-sm truncate">
                                                <?php echo htmlspecialchars($ad['name']); ?>
                                            </h3>
                                            <p class="text-xs text-slate-400 mt-0.5">
                                                By <?php echo htmlspecialchars($ad['person_name']); ?> • <?php echo htmlspecialchars($ad['Date'] ?? $ad['date'] ?? date('Y-m-d')); ?>
                                            </p>
                                        </div>

                                        <div class="pt-2 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between text-xs">
                                            <span class="text-slate-500 dark:text-slate-400">
                                                Fee: <strong>$<?php echo htmlspecialchars($ad['fees']); ?></strong>
                                            </span>
                                            <a href="mailto:<?php echo htmlspecialchars($ad['email']); ?>" class="font-bold text-blue-600 dark:text-blue-400 hover:underline">
                                                Contact
                                            </a>
                                        </div>
                                    </div>

                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="p-8 text-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-none">
                            <p class="text-slate-500 text-xs">No active advertisements found.</p>
                        </div>
                    <?php endif; ?>
                </div>

            </div>

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
