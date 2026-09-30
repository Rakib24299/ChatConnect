<?php
session_start();
require_once 'connection.php';

$userEmail = $_SESSION['userEmail'] ?? 'User';
$userName = $_SESSION['userName'] ?? 'Chat Member';
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

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
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(156, 163, 175, 0.4);
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(156, 163, 175, 0.7);
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

                <!-- Desktop Nav Links -->
                <nav class="hidden md:flex items-center gap-1 lg:gap-2">
                    <a href="desh.php" class="px-4 py-2 rounded-xl font-medium text-slate-600 hover:text-primary dark:text-slate-300 dark:hover:text-primary-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 transition">
                        Home
                    </a>
                    <a href="singinconnection.php" class="px-4 py-2 rounded-xl font-medium text-slate-600 hover:text-primary dark:text-slate-300 dark:hover:text-primary-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 transition">
                        Dashboard
                    </a>
                    <a href="viewfriend.php" class="px-4 py-2 rounded-xl font-medium text-slate-600 hover:text-primary dark:text-slate-300 dark:hover:text-primary-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 transition">
                        Friends
                    </a>
                    <a href="chatt.php" class="px-4 py-2 rounded-xl font-semibold text-primary bg-primary-50 dark:bg-primary/10 transition">
                        Chat
                    </a>
                </nav>

                <!-- Actions -->
                <div class="flex items-center gap-3">
                    <button onclick="toggleDarkMode()" class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 transition" title="Toggle Theme" aria-label="Toggle Theme">
                        <span id="theme-icon-container">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                            </svg>
                        </span>
                    </button>
                    <a href="singinconnection.php" class="px-5 py-2.5 rounded-xl font-semibold text-white bg-primary hover:bg-primary-600 shadow-md shadow-primary/25 transition duration-200 text-sm">
                        User Hub
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Messenger Container -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-none overflow-hidden flex flex-col md:flex-row h-[750px]">
            
            <!-- Left Sidebar: Conversations List -->
            <div id="chats-sidebar" class="w-full md:w-80 lg:w-96 border-r border-slate-200/80 dark:border-slate-800 flex flex-col bg-slate-50/50 dark:bg-slate-900/50">
                <!-- Search & Header -->
                <div class="p-4 border-b border-slate-200/80 dark:border-slate-800 space-y-3">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-extrabold text-slate-900 dark:text-white">Messages</h2>
                        <a href="callingpp.php" class="p-2 rounded-xl bg-primary-50 dark:bg-primary/10 text-primary hover:bg-primary hover:text-white transition" title="Start Call">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="23 7 16 12 23 17 23 7"></polygon>
                                <rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect>
                            </svg>
                        </a>
                    </div>
                    <!-- Search Input -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                        </div>
                        <input type="text" placeholder="Search Messenger..." class="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs sm:text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                </div>

                <!-- Chat Conversations List -->
                <div class="flex-grow overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800/60 p-2 space-y-1">
                    
                    <!-- Active Chat (Group / Friend) -->
                    <div class="p-3 rounded-2xl bg-white dark:bg-slate-800 shadow-sm border border-slate-200/60 dark:border-slate-700/60 flex items-center gap-3 cursor-pointer">
                        <div class="relative">
                            <div class="w-12 h-12 rounded-2xl bg-primary text-white flex items-center justify-center font-bold text-base shadow-sm">
                                GC
                            </div>
                            <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full bg-emerald-500 ring-2 ring-white dark:ring-slate-900"></span>
                        </div>
                        <div class="flex-grow overflow-hidden">
                            <div class="flex items-center justify-between">
                                <h4 class="font-bold text-slate-900 dark:text-white text-sm truncate">General Group Chat</h4>
                                <span class="text-[11px] font-medium text-slate-400">Now</span>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 truncate mt-0.5">You: Hey everyone, welcome to ChatConnect!</p>
                        </div>
                    </div>

                    <!-- Friend 1 -->
                    <div class="p-3 rounded-2xl hover:bg-white/80 dark:hover:bg-slate-800/60 transition flex items-center gap-3 cursor-pointer">
                        <div class="relative">
                            <div class="w-12 h-12 rounded-2xl bg-indigo-500 text-white flex items-center justify-center font-bold text-base shadow-sm">
                                F
                            </div>
                            <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full bg-emerald-500 ring-2 ring-white dark:ring-slate-900"></span>
                        </div>
                        <div class="flex-grow overflow-hidden">
                            <div class="flex items-center justify-between">
                                <h4 class="font-bold text-slate-900 dark:text-white text-sm truncate">Fahim Ahmed</h4>
                                <span class="text-[11px] font-medium text-slate-400">10:30 AM</span>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 truncate mt-0.5">Let's check the database queries today.</p>
                        </div>
                    </div>

                    <!-- Friend 2 -->
                    <div class="p-3 rounded-2xl hover:bg-white/80 dark:hover:bg-slate-800/60 transition flex items-center gap-3 cursor-pointer">
                        <div class="relative">
                            <div class="w-12 h-12 rounded-2xl bg-purple-500 text-white flex items-center justify-center font-bold text-base shadow-sm">
                                S
                            </div>
                            <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full bg-slate-300 dark:bg-slate-600 ring-2 ring-white dark:ring-slate-900"></span>
                        </div>
                        <div class="flex-grow overflow-hidden">
                            <div class="flex items-center justify-between">
                                <h4 class="font-bold text-slate-900 dark:text-white text-sm truncate">Sajjad Hossain</h4>
                                <span class="text-[11px] font-medium text-slate-400">Yesterday</span>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 truncate mt-0.5">Awesome update to the UI!</p>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Right Area: Active Chat Window -->
            <div class="flex-grow flex flex-col bg-white dark:bg-slate-900">
                
                <!-- Chat Top Header -->
                <div class="p-4 border-b border-slate-200/80 dark:border-slate-800 flex items-center justify-between bg-white dark:bg-slate-900">
                    <div class="flex items-center gap-3.5">
                        <div class="relative">
                            <div class="w-11 h-11 rounded-2xl bg-primary text-white flex items-center justify-center font-bold text-base shadow-sm">
                                GC
                            </div>
                            <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full bg-emerald-500 ring-2 ring-white dark:ring-slate-900"></span>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white text-base">General Messenger Group Chat</h3>
                            <div class="flex items-center gap-1.5 text-xs text-emerald-600 dark:text-emerald-400 font-semibold">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span>Active & Encrypted</span>
                            </div>
                        </div>
                    </div>

                    <!-- Call & Tool Buttons -->
                    <div class="flex items-center gap-2">
                        <a href="callingpp.php" class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 transition" title="Start Voice/Video Call">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="23 7 16 12 23 17 23 7"></polygon>
                                <rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect>
                            </svg>
                        </a>
                        <a href="viewfriend.php" class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 transition" title="View Members">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Chat Messages Scroll Area -->
                <div id="chatMessages" class="flex-grow overflow-y-auto p-5 sm:p-6 space-y-4 bg-slate-50/40 dark:bg-slate-950/40">
                    
                    <!-- System Time Banner -->
                    <div class="text-center">
                        <span class="px-3.5 py-1 rounded-full text-xs font-semibold bg-slate-200/60 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                            Today
                        </span>
                    </div>

                    <!-- Received Message 1 -->
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-indigo-500 text-white flex items-center justify-center text-xs font-bold shrink-0">
                            F
                        </div>
                        <div class="max-w-md">
                            <div class="flex items-baseline gap-2 mb-1">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Fahim</span>
                                <span class="text-[10px] text-slate-400">10:15 AM</span>
                            </div>
                            <div class="p-3.5 rounded-2xl rounded-tl-sm bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-sm border border-slate-200/70 dark:border-slate-700/60 shadow-sm">
                                Hey everyone! Welcome to our new ChatConnect system.
                            </div>
                        </div>
                    </div>

                    <!-- Received Message 2 -->
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-purple-500 text-white flex items-center justify-center text-xs font-bold shrink-0">
                            S
                        </div>
                        <div class="max-w-md">
                            <div class="flex items-baseline gap-2 mb-1">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Sajjad</span>
                                <span class="text-[10px] text-slate-400">10:16 AM</span>
                            </div>
                            <div class="p-3.5 rounded-2xl rounded-tl-sm bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-sm border border-slate-200/70 dark:border-slate-700/60 shadow-sm">
                                The new 2024 UI with Tailwind is super clean and fast! 🚀
                            </div>
                        </div>
                    </div>

                    <!-- Sent Message (You) -->
                    <div class="flex items-start justify-end gap-3">
                        <div class="max-w-md text-right">
                            <div class="flex items-baseline justify-end gap-2 mb-1">
                                <span class="text-[10px] text-slate-400">10:18 AM</span>
                                <span class="text-xs font-bold text-primary">You</span>
                            </div>
                            <div class="p-3.5 rounded-2xl rounded-tr-sm bg-primary text-white text-sm shadow-md shadow-primary/20 text-left">
                                Hello friends! You can now send real-time text and media seamlessly.
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Chat Input Box Bar -->
                <div class="p-4 border-t border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900">
                    
                    <!-- File Preview Area (if selected) -->
                    <div id="imagePreviewContainer" class="hidden mb-3 p-2 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <img id="imagePreview" src="" alt="Preview" class="w-12 h-12 object-cover rounded-xl border border-slate-200 dark:border-slate-700">
                            <span id="imageFileName" class="text-xs font-semibold text-slate-700 dark:text-slate-300 truncate max-w-xs"></span>
                        </div>
                        <button onclick="clearImage()" class="p-1.5 rounded-lg text-slate-500 hover:text-red-500 transition">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>

                    <div class="flex items-center gap-2 sm:gap-3">
                        <!-- Image Attachment Trigger -->
                        <label for="imageInput" class="p-3 rounded-2xl border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 dark:text-slate-400 hover:text-primary transition cursor-pointer shrink-0" title="Attach Photo">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                <polyline points="21 15 16 10 5 21"></polyline>
                            </svg>
                            <input type="file" id="imageInput" accept="image/*" class="hidden" onchange="previewSelectedImage(this)">
                        </label>

                        <!-- Message Text Input -->
                        <div class="relative flex-grow">
                            <input type="text" id="messageInput" placeholder="Type a message..." class="w-full pl-4 pr-10 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary text-sm" onkeypress="handleKeyPress(event)">
                        </div>

                        <!-- Send Button -->
                        <button onclick="sendMessage()" class="py-3 px-5 sm:px-6 rounded-2xl font-bold text-white bg-primary hover:bg-primary-600 shadow-md shadow-primary/25 hover:shadow-lg transition flex items-center justify-center gap-2 shrink-0">
                            <span class="hidden sm:inline text-sm">Send</span>
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="22" y1="2" x2="11" y2="13"></line>
                                <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                            </svg>
                        </button>
                    </div>
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
                    let imageHtml = `<img src="${e.target.result}" alt="Photo" class="rounded-xl max-w-xs mb-2 shadow-sm border border-white/20">`;
                    let textHtml = text ? `<p>${escapeHtml(text)}</p>` : '';
                    
                    let bubble = `
                        <div class="flex items-start justify-end gap-3">
                            <div class="max-w-md text-right">
                                <div class="flex items-baseline justify-end gap-2 mb-1">
                                    <span class="text-[10px] text-slate-400">${timeNow}</span>
                                    <span class="text-xs font-bold text-primary">You</span>
                                </div>
                                <div class="p-3.5 rounded-2xl rounded-tr-sm bg-primary text-white text-sm shadow-md shadow-primary/20 text-left">
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
                    <div class="flex items-start justify-end gap-3">
                        <div class="max-w-md text-right">
                            <div class="flex items-baseline justify-end gap-2 mb-1">
                                <span class="text-[10px] text-slate-400">${timeNow}</span>
                                <span class="text-xs font-bold text-primary">You</span>
                            </div>
                            <div class="p-3.5 rounded-2xl rounded-tr-sm bg-primary text-white text-sm shadow-md shadow-primary/20 text-left">
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
