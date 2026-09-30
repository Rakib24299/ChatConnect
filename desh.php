<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChatConnect - Connect, Chat & Share</title>
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
                        <!-- Chat Bubble SVG Icon -->
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                        </svg>
                    </div>
                    <span class="text-2xl font-black tracking-tight text-primary">
                        ChatConnect
                    </span>
                </a>

                <!-- Desktop Nav Links -->
                <nav class="hidden md:flex items-center gap-1 lg:gap-2">
                    <a href="desh.php" class="px-4 py-2 rounded-xl font-semibold text-primary bg-primary-50 dark:bg-primary/10 transition">
                        Home
                    </a>
                    <a href="about.html" class="px-4 py-2 rounded-xl font-medium text-slate-600 hover:text-primary dark:text-slate-300 dark:hover:text-primary-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 transition">
                        About
                    </a>
                    <a href="adminlogin.php" class="px-4 py-2 rounded-xl font-medium text-slate-600 hover:text-primary dark:text-slate-300 dark:hover:text-primary-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 transition">
                        Admin
                    </a>
                </nav>

                <!-- Action Buttons & Dark Mode Toggle -->
                <div class="hidden md:flex items-center gap-3">
                    <!-- Dark Mode Button -->
                    <button onclick="toggleDarkMode()" class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 transition" title="Toggle Theme" aria-label="Toggle Theme">
                        <span id="theme-icon-container">
                            <!-- Moon SVG (Dark mode inactive) -->
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                            </svg>
                        </span>
                    </button>

                    <a href="singin.php" class="px-5 py-2.5 rounded-xl font-semibold text-slate-700 dark:text-slate-200 hover:text-primary dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        Sign In
                    </a>
                    <a href="singup.php" class="px-5 py-2.5 rounded-xl font-semibold text-white bg-primary hover:bg-primary-600 shadow-md shadow-primary/25 hover:shadow-lg hover:shadow-primary/35 hover:-translate-y-0.5 transition duration-200">
                        Sign Up Free
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <div class="flex items-center gap-2 md:hidden">
                    <button onclick="toggleDarkMode()" class="p-2 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        <span id="theme-icon-mobile-container">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                            </svg>
                        </span>
                    </button>
                    <button onclick="toggleMobileMenu()" class="p-2 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 focus:outline-none transition">
                        <span id="menu-icon-container">
                            <!-- Bars SVG -->
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
            <div id="mobile-menu" class="hidden md:hidden py-4 border-t border-slate-200 dark:border-slate-800 space-y-2">
                <a href="desh.php" class="block px-4 py-2.5 rounded-lg font-semibold text-primary bg-primary-50 dark:bg-primary/10">Home</a>
                <a href="about.html" class="block px-4 py-2.5 rounded-lg font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">About</a>
                <a href="adminlogin.php" class="block px-4 py-2.5 rounded-lg font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">Admin</a>
                <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex flex-col gap-2">
                    <a href="singin.php" class="text-center w-full py-2.5 rounded-xl font-semibold border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-200">Sign In</a>
                    <a href="singup.php" class="text-center w-full py-2.5 rounded-xl font-semibold text-white bg-primary hover:bg-primary-600">Sign Up Free</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content / Hero Section -->
    <main class="flex-grow">
        <!-- Hero Section -->
        <section class="relative overflow-hidden py-12 lg:py-20">
            <!-- Background Glow Decor -->
            <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 sm:w-[600px] h-96 sm:h-[600px] bg-primary/15 rounded-full blur-3xl pointer-events-none"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="text-center max-w-3xl mx-auto mb-12">
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-[1.15]">
                        Connect, Chat & Share with <span class="text-primary">ChatConnect</span>
                    </h1>
                    <p class="mt-6 text-lg sm:text-xl text-slate-600 dark:text-slate-300 leading-relaxed">
                        Stay connected with friends, family, and communities anytime, anywhere. Experience fast messaging, seamless media sharing, and instant calling in one unified platform.
                    </p>
                    <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a href="singup.php" class="w-full sm:w-auto px-8 py-4 rounded-2xl font-bold text-white bg-primary hover:bg-primary-600 shadow-xl shadow-primary/30 hover:shadow-2xl hover:shadow-primary/40 hover:-translate-y-0.5 transition duration-200 flex items-center justify-center gap-3">
                            <span>Get Started Now</span>
                            <!-- Arrow Right SVG -->
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </a>
                        <a href="singin.php" class="w-full sm:w-auto px-8 py-4 rounded-2xl font-bold text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-800/80 shadow-sm transition duration-200 flex items-center justify-center gap-2">
                            <!-- Log In SVG -->
                            <svg class="w-5 h-5 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                                <polyline points="10 17 15 12 10 7"></polyline>
                                <line x1="15" y1="12" x2="3" y2="12"></line>
                            </svg>
                            <span>Sign In</span>
                        </a>
                    </div>
                </div>

                <!-- Showcase Image Mockup -->
                <div class="max-w-5xl mx-auto mt-10 rounded-3xl p-3 sm:p-4 bg-gradient-to-b from-white/80 to-slate-100/50 dark:from-slate-800/60 dark:to-slate-900/40 border border-slate-200/80 dark:border-slate-800 backdrop-blur-xl shadow-2xl">
                    <div class="overflow-hidden rounded-2xl bg-slate-950">
                        <img src="deshpic.php" alt="ChatConnect App Preview" class="w-full h-auto object-cover hover:scale-[1.02] transition-transform duration-500">
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Grid -->
        <section class="py-16 bg-white dark:bg-slate-900/50 border-t border-slate-200/80 dark:border-slate-800/80 transition-colors">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-14">
                    <h2 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-4xl">
                        Designed for Effortless Communication
                    </h2>
                    <p class="mt-4 text-base sm:text-lg text-slate-600 dark:text-slate-400">
                        Everything you need to interact, connect, and collaborate with your network effortlessly.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="p-8 rounded-3xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-700/60 hover:border-primary/50 hover:shadow-xl hover:shadow-primary/5 transition duration-300">
                        <div class="w-14 h-14 rounded-2xl bg-primary-50 dark:bg-primary/10 text-primary flex items-center justify-center mb-6">
                            <!-- Messages SVG Icon -->
                            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">
                            Instant Messaging
                        </h3>
                        <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                            Send direct messages and multimedia with friends seamlessly and instantly across all devices.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="p-8 rounded-3xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-700/60 hover:border-primary/50 hover:shadow-xl hover:shadow-primary/5 transition duration-300">
                        <div class="w-14 h-14 rounded-2xl bg-primary-50 dark:bg-primary/10 text-primary flex items-center justify-center mb-6">
                            <!-- Video Call SVG Icon -->
                            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="23 7 16 12 23 17 23 7"></polygon>
                                <rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">
                            Audio & Video Calls
                        </h3>
                        <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                            Crystal-clear voice and video calling support to stay in touch face-to-face anytime.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="p-8 rounded-3xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-700/60 hover:border-primary/50 hover:shadow-xl hover:shadow-primary/5 transition duration-300">
                        <div class="w-14 h-14 rounded-2xl bg-primary-50 dark:bg-primary/10 text-primary flex items-center justify-center mb-6">
                            <!-- Shield SVG Icon -->
                            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">
                            Database Protection
                        </h3>
                        <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                            Structured MySQL database management to securely manage user authentication and messages.
                        </p>
                    </div>
                </div>
            </div>
        </section>
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

    <!-- Scripts for Dark Mode & Mobile Menu -->
    <script>
        const sunSvg = `<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>`;
        
        const moonSvg = `<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>`;

        const barsSvg = `<svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>`;

        const xmarkSvg = `<svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>`;

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

        initTheme();
    </script>
</body>

</html>
