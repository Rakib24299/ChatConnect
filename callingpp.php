<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voice & Video Call - ChatConnect</title>
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

<body class="bg-slate-900 text-white min-h-screen flex flex-col justify-between">

    <!-- Top Call Header -->
    <header class="p-4 sm:p-6 flex items-center justify-between z-20">
        <div class="flex items-center gap-3">
            <a href="singinconnection.php" class="p-2.5 rounded-2xl bg-white/10 hover:bg-white/20 text-white backdrop-blur-md transition" title="Back to Dashboard">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
            </a>
            <div>
                <h1 class="text-base sm:text-lg font-bold">ChatConnect HD Call</h1>
                <div class="flex items-center gap-2 text-xs text-emerald-400 font-medium">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span id="callTimer">00:00</span> &bull; End-to-End Encrypted
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="singinconnection.php" class="px-3.5 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-white backdrop-blur-md transition text-xs font-semibold flex items-center gap-1.5 border border-white/10">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                <span>Back to Dashboard</span>
            </a>
        </div>
    </header>

    <!-- Center Call Stage -->
    <main class="flex-grow flex items-center justify-center p-4 relative">
        
        <!-- Video Screen Container -->
        <div class="relative w-full max-w-4xl h-[480px] sm:h-[540px] rounded-3xl overflow-hidden bg-slate-950 border border-white/10 shadow-2xl flex items-center justify-center">
            
            <!-- Video Element / Stream -->
            <video id="localVideo" autoplay playsinline muted class="w-full h-full object-cover"></video>

            <!-- Fallback Avatar (when camera is off) -->
            <div id="avatarFallback" class="hidden flex-col items-center justify-center gap-4 text-center">
                <div class="w-28 h-28 rounded-full bg-gradient-to-tr from-primary to-indigo-500 text-white flex items-center justify-center text-4xl font-black shadow-2xl ring-4 ring-white/10">
                    CC
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white">General Group Voice Call</h2>
                    <p class="text-xs text-slate-400 mt-1">Microphone is active</p>
                </div>
            </div>

            <!-- Inset Remote Participant Card -->
            <div class="absolute top-4 right-4 w-32 sm:w-44 h-44 sm:h-56 rounded-2xl overflow-hidden bg-slate-800/90 border border-white/20 shadow-2xl backdrop-blur-md flex flex-col items-center justify-center p-2">
                <div class="w-12 h-12 rounded-full bg-primary/30 text-primary flex items-center justify-center font-bold text-lg mb-2">
                    F
                </div>
                <span class="text-xs font-bold text-white truncate max-w-full">Fahim Ahmed</span>
                <span class="text-[10px] text-emerald-400 mt-0.5">Connected</span>
            </div>

        </div>
    </main>

    <!-- Bottom Controls Bar -->
    <footer class="p-6 pb-8 flex flex-col items-center gap-6 z-20">
        <!-- Action Buttons -->
        <div class="flex items-center gap-4 sm:gap-6 bg-slate-800/80 backdrop-blur-xl border border-white/10 p-3 sm:p-4 rounded-3xl shadow-2xl">
            
            <!-- Mic Toggle -->
            <button id="micBtn" onclick="toggleMic()" class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition" title="Toggle Microphone">
                <svg id="micIcon" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"></path>
                    <path d="M19 10v2a7 7 0 0 1-14 0v-2"></path>
                    <line x1="12" y1="19" x2="12" y2="23"></line>
                    <line x1="8" y1="23" x2="16" y2="23"></line>
                </svg>
            </button>

            <!-- Camera Toggle -->
            <button id="camBtn" onclick="toggleCamera()" class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition" title="Toggle Camera">
                <svg id="camIcon" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="23 7 16 12 23 17 23 7"></polygon>
                    <rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect>
                </svg>
            </button>

            <!-- End Call Button -->
            <a href="chatt.php" class="w-14 h-12 sm:w-16 sm:h-14 rounded-2xl bg-red-600 hover:bg-red-700 text-white flex items-center justify-center shadow-lg shadow-red-600/40 hover:scale-105 transition" title="End Call">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.68 13.31a16 16 0 0 0 3.41 2.6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7 2 2 0 0 1 1.72 2v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.42 19.42 0 0 1-6-6 19.86 19.86 0 0 1-3.12-8.69A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91"></path>
                    <line x1="23" y1="1" x2="1" y2="23"></line>
                </svg>
            </a>

            <!-- Screen Share -->
            <button onclick="shareScreen()" class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition" title="Share Screen">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                    <line x1="8" y1="21" x2="16" y2="21"></line>
                    <line x1="12" y1="17" x2="12" y2="21"></line>
                </svg>
            </button>
        </div>

        <!-- Footer Credits -->
        <div class="w-full max-w-4xl flex flex-col sm:flex-row items-center justify-between text-xs text-slate-400 gap-2 border-t border-white/10 pt-4">
            <div>&copy; 2024 ChatConnect. All rights reserved.</div>
            <div>Developed by <span class="font-bold text-white">Rakib</span></div>
        </div>
    </footer>

    <!-- Call JavaScript -->
    <script>
        let localStream = null;
        let isAudioMuted = false;
        let isVideoOff = false;
        let secondsElapsed = 0;

        // Timer
        setInterval(() => {
            secondsElapsed++;
            const mins = String(Math.floor(secondsElapsed / 60)).padStart(2, '0');
            const secs = String(secondsElapsed % 60).padStart(2, '0');
            document.getElementById('callTimer').innerText = `${mins}:${secs}`;
        }, 1000);

        // Start Camera
        async function initCamera() {
            try {
                localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
                document.getElementById('localVideo').srcObject = localStream;
            } catch (err) {
                console.log("Camera access not available or denied:", err);
                document.getElementById('localVideo').classList.add('hidden');
                document.getElementById('avatarFallback').classList.remove('hidden');
                document.getElementById('avatarFallback').classList.add('flex');
            }
        }

        function toggleMic() {
            if (localStream) {
                const audioTracks = localStream.getAudioTracks();
                if (audioTracks.length > 0) {
                    isAudioMuted = !isAudioMuted;
                    audioTracks[0].enabled = !isAudioMuted;
                    document.getElementById('micBtn').classList.toggle('bg-red-500/20', isAudioMuted);
                    document.getElementById('micBtn').classList.toggle('text-red-400', isAudioMuted);
                }
            }
        }

        function toggleCamera() {
            if (localStream) {
                const videoTracks = localStream.getVideoTracks();
                if (videoTracks.length > 0) {
                    isVideoOff = !isVideoOff;
                    videoTracks[0].enabled = !isVideoOff;
                    document.getElementById('localVideo').classList.toggle('hidden', isVideoOff);
                    document.getElementById('avatarFallback').classList.toggle('hidden', !isVideoOff);
                    document.getElementById('avatarFallback').classList.toggle('flex', isVideoOff);
                    document.getElementById('camBtn').classList.toggle('bg-red-500/20', isVideoOff);
                    document.getElementById('camBtn').classList.toggle('text-red-400', isVideoOff);
                }
            } else {
                isVideoOff = !isVideoOff;
                document.getElementById('localVideo').classList.toggle('hidden', isVideoOff);
                document.getElementById('avatarFallback').classList.toggle('hidden', !isVideoOff);
                document.getElementById('avatarFallback').classList.toggle('flex', isVideoOff);
            }
        }

        async function shareScreen() {
            try {
                const screenStream = await navigator.mediaDevices.getDisplayMedia({ video: true });
                document.getElementById('localVideo').srcObject = screenStream;
            } catch (err) {
                console.log("Screen share cancelled:", err);
            }
        }

        initCamera();
    </script>
</body>

</html>