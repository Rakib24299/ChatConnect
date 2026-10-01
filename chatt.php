<?php
session_start();
require_once 'connection.php';

$userEmail = $_SESSION['userEmail'] ?? 'User';
$userName = $_SESSION['userName'] ?? 'Chat Member';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messenger Chat - ChatConnect</title>
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

    <!-- Main Messenger Container -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-none overflow-hidden flex flex-col md:flex-row h-[650px]">
            
            <!-- Left Sidebar: Conversations List -->
            <div id="chats-sidebar" class="w-full md:w-80 border-r border-slate-300 dark:border-slate-700 flex flex-col bg-slate-50 dark:bg-slate-900">
                <!-- Search & Header -->
                <div class="p-3 border-b border-slate-300 dark:border-slate-700 space-y-2">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">Conversations</h2>
                        <a href="callingpp.php" class="px-2 py-1 bg-blue-100 dark:bg-slate-700 text-blue-600 dark:text-blue-400 hover:bg-blue-200 text-xs font-semibold rounded-none">
                            Start Call
                        </a>
                    </div>
                    <!-- Search Input -->
                    <input type="text" placeholder="Search conversations..." class="w-full px-3 py-1.5 rounded-none border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-blue-600">
                </div>

                <!-- Chat Conversations List -->
                <div class="flex-grow overflow-y-auto divide-y divide-slate-200 dark:divide-slate-800 p-1 space-y-1">
                    
                    <!-- Active Chat (Group / Friend) -->
                    <div class="p-2.5 bg-white dark:bg-slate-800 border border-blue-500 rounded-none flex items-center gap-2.5 cursor-pointer">
                        <div class="w-9 h-9 bg-blue-600 text-white flex items-center justify-center font-bold text-xs rounded-none shrink-0">
                            GC
                        </div>
                        <div class="flex-grow overflow-hidden">
                            <div class="flex items-center justify-between">
                                <h4 class="font-bold text-slate-900 dark:text-white text-xs truncate">General Group Chat</h4>
                                <span class="text-[10px] text-slate-400">Now</span>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 truncate">You: Welcome to ChatConnect!</p>
                        </div>
                    </div>

                    <!-- Friend 1 -->
                    <div class="p-2.5 hover:bg-white dark:hover:bg-slate-800 border border-transparent rounded-none flex items-center gap-2.5 cursor-pointer">
                        <div class="w-9 h-9 bg-indigo-600 text-white flex items-center justify-center font-bold text-xs rounded-none shrink-0">
                            F
                        </div>
                        <div class="flex-grow overflow-hidden">
                            <div class="flex items-center justify-between">
                                <h4 class="font-bold text-slate-900 dark:text-white text-xs truncate">Fahim Ahmed</h4>
                                <span class="text-[10px] text-slate-400">10:30 AM</span>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 truncate">Let's check the database queries today.</p>
                        </div>
                    </div>

                    <!-- Friend 2 -->
                    <div class="p-2.5 hover:bg-white dark:hover:bg-slate-800 border border-transparent rounded-none flex items-center gap-2.5 cursor-pointer">
                        <div class="w-9 h-9 bg-purple-600 text-white flex items-center justify-center font-bold text-xs rounded-none shrink-0">
                            S
                        </div>
                        <div class="flex-grow overflow-hidden">
                            <div class="flex items-center justify-between">
                                <h4 class="font-bold text-slate-900 dark:text-white text-xs truncate">Sajjad Hossain</h4>
                                <span class="text-[10px] text-slate-400">Yesterday</span>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 truncate">Awesome project update!</p>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Right Area: Active Chat Window -->
            <div class="flex-grow flex flex-col bg-white dark:bg-slate-800">
                
                <!-- Chat Top Header -->
                <div class="p-3 border-b border-slate-300 dark:border-slate-700 flex items-center justify-between bg-white dark:bg-slate-800">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 bg-blue-600 text-white flex items-center justify-center font-bold text-xs rounded-none">
                            GC
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white text-sm">General Group Chat</h3>
                            <span class="text-xs text-green-600 dark:text-green-400">Active</span>
                        </div>
                    </div>

                    <!-- Call & Tool Buttons -->
                    <div class="flex items-center gap-1.5">
                        <a href="callingpp.php" class="p-1.5 border border-slate-300 dark:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-none text-xs" title="Start Call">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="23 7 16 12 23 17 23 7"></polygon>
                                <rect x="1" y="5" width="15" height="14"></rect>
                            </svg>
                        </a>
                        <a href="viewfriend.php" class="p-1.5 border border-slate-300 dark:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-none text-xs" title="View Friends">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Chat Messages Scroll Area -->
                <div id="chatMessages" class="flex-grow overflow-y-auto p-4 space-y-3 bg-slate-50 dark:bg-slate-900">
                    
                    <!-- System Time Banner -->
                    <div class="text-center">
                        <span class="px-2 py-0.5 text-xs bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-none">
                            Today
                        </span>
                    </div>

                    <!-- Received Message 1 -->
                    <div class="flex items-start gap-2">
                        <div class="w-7 h-7 bg-indigo-600 text-white flex items-center justify-center text-xs font-bold rounded-none shrink-0">
                            F
                        </div>
                        <div class="max-w-md">
                            <div class="flex items-baseline gap-2 mb-0.5">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Fahim</span>
                                <span class="text-[10px] text-slate-400">10:15 AM</span>
                            </div>
                            <div class="p-2.5 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-xs border border-slate-300 dark:border-slate-700 rounded-none">
                                Hey everyone! Welcome to ChatConnect.
                            </div>
                        </div>
                    </div>

                    <!-- Received Message 2 -->
                    <div class="flex items-start gap-2">
                        <div class="w-7 h-7 bg-purple-600 text-white flex items-center justify-center text-xs font-bold rounded-none shrink-0">
                            S
                        </div>
                        <div class="max-w-md">
                            <div class="flex items-baseline gap-2 mb-0.5">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Sajjad</span>
                                <span class="text-[10px] text-slate-400">10:16 AM</span>
                            </div>
                            <div class="p-2.5 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-xs border border-slate-300 dark:border-slate-700 rounded-none">
                                The new clean UI is fast and easy to navigate!
                            </div>
                        </div>
                    </div>

                    <!-- Sent Message (You) -->
                    <div class="flex items-start justify-end gap-2">
                        <div class="max-w-md text-right">
                            <div class="flex items-baseline justify-end gap-2 mb-0.5">
                                <span class="text-[10px] text-slate-400">10:18 AM</span>
                                <span class="text-xs font-bold text-blue-600 dark:text-blue-400">You</span>
                            </div>
                            <div class="p-2.5 bg-blue-600 text-white text-xs text-left rounded-none">
                                Hello friends! You can now send real-time text and media seamlessly.
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Chat Input Box Bar -->
                <div class="p-3 border-t border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800">
                    
                    <!-- File Preview Area -->
                    <div id="imagePreviewContainer" class="hidden mb-2 p-2 bg-slate-100 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 flex items-center justify-between rounded-none">
                        <div class="flex items-center gap-2">
                            <img id="imagePreview" src="" alt="Preview" class="w-10 h-10 object-cover border border-slate-300 dark:border-slate-600 rounded-none">
                            <span id="imageFileName" class="text-xs font-medium text-slate-700 dark:text-slate-300 truncate max-w-xs"></span>
                        </div>
                        <button onclick="clearImage()" class="p-1 text-slate-500 hover:text-red-600">
                            ✕
                        </button>
                    </div>

                    <div class="flex items-center gap-2">
                        <!-- Image Attachment Trigger -->
                        <label for="imageInput" class="p-2 border border-slate-300 dark:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-none cursor-pointer shrink-0" title="Attach Photo">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="18" height="18"></rect>
                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                <polyline points="21 15 16 10 5 21"></polyline>
                            </svg>
                            <input type="file" id="imageInput" accept="image/*" class="hidden" onchange="previewSelectedImage(this)">
                        </label>

                        <!-- Message Text Input -->
                        <input type="text" id="messageInput" placeholder="Type a message..." class="flex-grow px-3 py-2 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-blue-600 text-xs sm:text-sm rounded-none" onkeypress="handleKeyPress(event)">

                        <!-- Send Button -->
                        <button onclick="sendMessage()" class="py-2 px-4 font-bold text-white bg-blue-600 hover:bg-blue-700 text-xs sm:text-sm rounded-none shrink-0">
                            Send
                        </button>
                    </div>
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

    <!-- Scripts for Messaging, Image Previews & Theme -->
    <script>
        function previewSelectedImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('imagePreview').src = e.target.result;
                    document.getElementById('imageFileName').innerText = input.files[0].name;
                    document.getElementById('imagePreviewContainer').classList.remove('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function clearImage() {
            const input = document.getElementById('imageInput');
            input.value = '';
            document.getElementById('imagePreviewContainer').classList.add('hidden');
        }

        function handleKeyPress(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        }

        function sendMessage() {
            const messageInput = document.getElementById('messageInput');
            const chatMessages = document.getElementById('chatMessages');
            const imageInput = document.getElementById('imageInput');
            const text = messageInput.value.trim();
            const hasImage = imageInput.files && imageInput.files.length > 0;

            if (!text && !hasImage) return;

            const timeNow = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

            if (hasImage) {
                const file = imageInput.files[0];
                const reader = new FileReader();
                reader.onload = function(e) {
                    let imageHtml = `<img src="${e.target.result}" alt="Photo" class="max-w-xs mb-1 border border-slate-300 rounded-none">`;
                    let textHtml = text ? `<p>${escapeHtml(text)}</p>` : '';
                    
                    let bubble = `
                        <div class="flex items-start justify-end gap-2">
                            <div class="max-w-md text-right">
                                <div class="flex items-baseline justify-end gap-2 mb-0.5">
                                    <span class="text-[10px] text-slate-400">${timeNow}</span>
                                    <span class="text-xs font-bold text-blue-600 dark:text-blue-400">You</span>
                                </div>
                                <div class="p-2.5 bg-blue-600 text-white text-xs text-left rounded-none">
                                    ${imageHtml}
                                    ${textHtml}
                                </div>
                            </div>
                        </div>
                    `;
                    chatMessages.insertAdjacentHTML('beforeend', bubble);
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                    clearImage();
                }
                reader.readAsDataURL(file);
            } else {
                let bubble = `
                    <div class="flex items-start justify-end gap-2">
                        <div class="max-w-md text-right">
                            <div class="flex items-baseline justify-end gap-2 mb-0.5">
                                <span class="text-[10px] text-slate-400">${timeNow}</span>
                                <span class="text-xs font-bold text-blue-600 dark:text-blue-400">You</span>
                            </div>
                            <div class="p-2.5 bg-blue-600 text-white text-xs text-left rounded-none">
                                ${escapeHtml(text)}
                            </div>
                        </div>
                    </div>
                `;
                chatMessages.insertAdjacentHTML('beforeend', bubble);
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }

            messageInput.value = '';
        }

        function escapeHtml(string) {
            return String(string).replace(/[&<>"'`=\/]/g, function (s) {
                return ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#39;',
                    '/': '&#x2F;',
                    '`': '&#x60;',
                    '=': '&#x3D;'
                })[s];
            });
        }

        // Theme Toggle
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
