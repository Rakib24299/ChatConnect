<!DOCTYPE html>
<html lang="en">

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

<body class="bg-slate-900 text-white min-h-screen flex flex-col justify-between font-sans">

    <!-- Top Call Header -->
    <header class="p-4 bg-slate-800 border-b border-slate-700 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="singinconnection.php" class="p-2 border border-slate-600 bg-slate-700 hover:bg-slate-600 text-white rounded-none text-xs flex items-center gap-1">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
            </a>
            <div>
                <h1 class="text-sm sm:text-base font-bold">ChatConnect Calling Stage</h1>
                <div class="flex items-center gap-1.5 text-xs text-green-400">
                    <span class="w-2 h-2 rounded-none bg-green-400"></span>
                    <span id="callTimer">00:00</span> • Connected
                </div>
            </div>
        </div>

        <div>
            <a href="singinconnection.php" class="px-3 py-1.5 border border-slate-600 bg-slate-700 hover:bg-slate-600 text-white text-xs font-semibold rounded-none">
                Back to Dashboard
            </a>
        </div>
    </header>

    <!-- Center Call Stage -->
    <main class="flex-grow flex items-center justify-center p-4">
        
        <!-- Video Screen Container -->
        <div class="relative w-full max-w-3xl h-[400px] sm:h-[480px] bg-slate-950 border border-slate-700 rounded-none flex items-center justify-center">
            
            <!-- Video Element / Stream -->
            <video id="localVideo" autoplay playsinline muted class="w-full h-full object-cover rounded-none"></video>

            <!-- Fallback Avatar (when camera is off) -->
            <div id="avatarFallback" class="hidden flex-col items-center justify-center gap-3 text-center">
                <div class="w-20 h-20 bg-blue-600 text-white flex items-center justify-center text-2xl font-bold rounded-none">
                    CC
                </div>
                <div>
                    <h2 class="text-base font-bold text-white">Voice Calling Mode</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Microphone active</p>
                </div>
            </div>

            <!-- Inset Remote Participant Card -->
            <div class="absolute top-3 right-3 w-32 h-40 bg-slate-800 border border-slate-700 rounded-none flex flex-col items-center justify-center p-2">
                <div class="w-10 h-10 bg-blue-600 text-white flex items-center justify-center font-bold text-sm mb-1 rounded-none">
                    F
                </div>
                <span class="text-xs font-bold text-white truncate max-w-full">Fahim</span>
                <span class="text-[10px] text-green-400 mt-0.5">Online</span>
            </div>

        </div>
    </main>

    <!-- Bottom Controls Bar -->
    <footer class="p-4 bg-slate-800 border-t border-slate-700 flex flex-col items-center gap-3">
        <!-- Action Buttons -->
        <div class="flex items-center gap-3">
            
            <!-- Mic Toggle -->
            <button id="micBtn" onclick="toggleMic()" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 border border-slate-600 text-white flex items-center gap-1.5 text-xs font-semibold rounded-none" title="Toggle Microphone">
                <svg id="micIcon" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"></path>
                    <path d="M19 10v2a7 7 0 0 1-14 0v-2"></path>
                    <line x1="12" y1="19" x2="12" y2="23"></line>
                    <line x1="8" y1="23" x2="16" y2="23"></line>
                </svg>
                <span>Mic</span>
            </button>

            <!-- Camera Toggle -->
            <button id="camBtn" onclick="toggleCamera()" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 border border-slate-600 text-white flex items-center gap-1.5 text-xs font-semibold rounded-none" title="Toggle Camera">
                <svg id="camIcon" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="23 7 16 12 23 17 23 7"></polygon>
                    <rect x="1" y="5" width="15" height="14"></rect>
                </svg>
                <span>Cam</span>
            </button>

            <!-- Screen Share -->
            <button onclick="shareScreen()" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 border border-slate-600 text-white flex items-center gap-1.5 text-xs font-semibold rounded-none" title="Share Screen">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="3" width="20" height="14"></rect>
                    <line x1="8" y1="21" x2="16" y2="21"></line>
                    <line x1="12" y1="17" x2="12" y2="21"></line>
                </svg>
                <span>Share</span>
            </button>

            <!-- End Call Button -->
            <a href="chatt.php" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white flex items-center gap-1.5 text-xs font-bold rounded-none" title="End Call">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.68 13.31a16 16 0 0 0 3.41 2.6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7 2 2 0 0 1 1.72 2v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.42 19.42 0 0 1-6-6 19.86 19.86 0 0 1-3.12-8.69A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91"></path>
                    <line x1="23" y1="1" x2="1" y2="23"></line>
                </svg>
                <span>End Call</span>
            </a>
        </div>

        <!-- Footer Credits -->
        <div class="w-full max-w-3xl flex flex-col sm:flex-row items-center justify-between text-xs text-slate-400 gap-1 border-t border-slate-700 pt-2">
            <div>&copy; 2024 ChatConnect. All rights reserved.</div>
            <div>Developed by <span class="font-bold text-white">Rakib</span></div>
        </div>
    </footer>

    <!-- Call JavaScript -->
    <script>
        let localStream = null;
        let isAudioMuted = false;
        let isVideoStopped = false;
        let callSeconds = 0;

        // Call Timer
        setInterval(() => {
            callSeconds++;
            const mins = String(Math.floor(callSeconds / 60)).padStart(2, '0');
            const secs = String(callSeconds % 60).padStart(2, '0');
            document.getElementById('callTimer').innerText = `${mins}:${secs}`;
        }, 1000);

        // Access WebCam stream
        async function initMedia() {
            try {
                localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
                const videoEl = document.getElementById('localVideo');
                videoEl.srcObject = localStream;
            } catch (err) {
                console.log("Camera access not available or denied:", err);
                showAvatarFallback();
            }
        }

        function showAvatarFallback() {
            const videoEl = document.getElementById('localVideo');
            const avatar = document.getElementById('avatarFallback');
            videoEl.classList.add('hidden');
            avatar.classList.remove('hidden');
            avatar.classList.add('flex');
        }

        function toggleMic() {
            if (localStream) {
                const audioTrack = localStream.getAudioTracks()[0];
                if (audioTrack) {
                    isAudioMuted = !isAudioMuted;
                    audioTrack.enabled = !isAudioMuted;
                    document.getElementById('micBtn').classList.toggle('bg-red-600', isAudioMuted);
                }
            }
        }

        function toggleCamera() {
            if (localStream) {
                const videoTrack = localStream.getVideoTracks()[0];
                if (videoTrack) {
                    isVideoStopped = !isVideoStopped;
                    videoTrack.enabled = !isVideoStopped;
                    
                    const videoEl = document.getElementById('localVideo');
                    const avatar = document.getElementById('avatarFallback');
                    
                    if (isVideoStopped) {
                        videoEl.classList.add('hidden');
                        avatar.classList.remove('hidden');
                        avatar.classList.add('flex');
                        document.getElementById('camBtn').classList.add('bg-red-600');
                    } else {
                        videoEl.classList.remove('hidden');
                        avatar.classList.add('hidden');
                        avatar.classList.remove('flex');
                        document.getElementById('camBtn').classList.remove('bg-red-600');
                    }
                }
            } else {
                showAvatarFallback();
            }
        }

        async function shareScreen() {
            try {
                const screenStream = await navigator.mediaDevices.getDisplayMedia({ video: true });
                const videoEl = document.getElementById('localVideo');
                videoEl.srcObject = screenStream;
                videoEl.classList.remove('hidden');
                document.getElementById('avatarFallback').classList.add('hidden');
            } catch (err) {
                console.log("Screen share cancelled:", err);
            }
        }

        initMedia();
    </script>
</body>

</html>