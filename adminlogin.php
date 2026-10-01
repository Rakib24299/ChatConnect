<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - ChatConnect</title>
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
                <a href="desh.php" class="flex items-center gap-2">
                    <div class="w-9 h-9 bg-blue-600 text-white flex items-center justify-center rounded-none font-bold">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-blue-600 dark:text-blue-400">
                        ChatConnect
                    </span>
                </a>

                <!-- Desktop Nav Links -->
                <nav class="hidden md:flex items-center gap-1">
                    <a href="desh.php" class="px-3 py-2 font-medium text-slate-600 hover:text-blue-600 dark:text-slate-300 dark:hover:text-white rounded-none">
                        Home
                    </a>
                    <a href="about.html" class="px-3 py-2 font-medium text-slate-600 hover:text-blue-600 dark:text-slate-300 dark:hover:text-white rounded-none">
                        About
                    </a>
                    <a href="adminlogin.php" class="px-3 py-2 font-medium text-blue-600 bg-blue-50 dark:bg-slate-700 dark:text-blue-400 border border-blue-200 dark:border-slate-600 rounded-none">
                        Admin
                    </a>
                </nav>

                <!-- Action Buttons & Dark Mode Toggle -->
                <div class="hidden md:flex items-center gap-3">
                    <button onclick="toggleDarkMode()" class="p-2 border border-slate-300 dark:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-none" title="Toggle Theme">
                        <span id="theme-icon-container">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                            </svg>
                        </span>
                    </button>

                    <a href="singin.php" class="px-4 py-2 font-medium border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-none">
                        Sign In
                    </a>
                    <a href="singup.php" class="px-4 py-2 font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-none">
                        Sign Up Free
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <div class="flex items-center gap-2 md:hidden">
                    <button onclick="toggleDarkMode()" class="p-2 border border-slate-300 dark:border-slate-600 text-slate-600 dark:text-slate-300 rounded-none">
                        <span id="theme-icon-mobile-container">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                            </svg>
                        </span>
                    </button>
                    <button onclick="toggleMobileMenu()" class="p-2 border border-slate-300 dark:border-slate-600 text-slate-600 dark:text-slate-300 rounded-none">
                        <span id="menu-icon-container">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="3" y1="12" x2="21" y2="12"></line>
                                <line x1="3" y1="6" x2="21" y2="6"></line>
                                <line x1="3" y1="18" x2="21" y2="18"></line>
                            </svg>
                        </span>
                    </button>
                </div>
            </div>

            <!-- Mobile Navigation Drawer -->
            <div id="mobile-menu" class="hidden md:hidden py-4 border-t border-slate-300 dark:border-slate-700 space-y-2">
                <a href="desh.php" class="block px-3 py-2 font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-none">Home</a>
                <a href="about.html" class="block px-3 py-2 font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-none">About</a>
                <a href="adminlogin.php" class="block px-3 py-2 font-semibold text-blue-600 bg-blue-50 dark:bg-slate-700 dark:text-blue-400 rounded-none">Admin</a>
                <div class="pt-3 border-t border-slate-300 dark:border-slate-700 flex flex-col gap-2">
                    <a href="singin.php" class="text-center w-full py-2 font-medium border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 rounded-none">Sign In</a>
                    <a href="singup.php" class="text-center w-full py-2 font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-none">Sign Up Free</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md">
            <!-- Card Container -->
            <div class="bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-none p-6 sm:p-8">
                
                <!-- Card Header -->
                <div class="text-center mb-6">
                    <div class="w-12 h-12 mx-auto bg-blue-100 dark:bg-slate-700 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-3 rounded-none">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            <circle cx="12" cy="11" r="2"></circle>
                            <path d="M12 13v4"></path>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                        Admin Login
                    </h1>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Enter credentials to access admin dashboard
                    </p>
                </div>

                <!-- Form -->
                <form action="adminconnect.php" method="POST" class="space-y-4">
                    
                    <!-- Username Input -->
                    <div>
                        <label for="username" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Username / Email
                        </label>
                        <input type="text" id="username" name="username" required
                            placeholder="Enter username"
                            class="w-full px-3 py-2 rounded-none border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-blue-600 text-sm">
                    </div>

                    <!-- Password Input -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Password
                        </label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required
                                placeholder="Enter password"
                                class="w-full px-3 py-2 pr-10 rounded-none border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-blue-600 text-sm">
                            <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                                <span id="password-toggle-icon">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="submit" class="w-full py-2.5 px-4 font-bold text-white bg-blue-600 hover:bg-blue-700 text-sm rounded-none">
                            Access Admin Dashboard
                        </button>
                    </div>

                    <!-- Member Signin Link -->
                    <div class="text-center pt-3 border-t border-slate-200 dark:border-slate-700">
                        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400">
                            Looking for user area?
                            <a href="singin.php" class="font-bold text-blue-600 dark:text-blue-400 hover:underline ml-1">
                                User Sign In
                            </a>
                        </p>
                    </div>

                </form>

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

    <!-- Scripts for Theme, Mobile Menu & Password Toggle -->
    <script>
        const sunSvg = `<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>`;
        
        const moonSvg = `<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>`;

        const barsSvg = `<svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>`;

        const xmarkSvg = `<svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>`;

        const eyeSvg = `<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>`;

        const eyeOffSvg = `<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>`;

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

        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('password-toggle-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.innerHTML = eyeOffSvg;
            } else {
                passwordInput.type = 'password';
                toggleIcon.innerHTML = eyeSvg;
            }
        }

        initTheme();
    </script>
</body>

</html>
