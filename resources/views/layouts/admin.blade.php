<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NusaVerse Controller Panel</title>
    <link rel="icon" type="image/png" href="{{ asset('images/NusaVerse.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script type="module" src="https://ajax.googleapis.com/ajax/libs/model-viewer/4.0.0/model-viewer.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #FAF7F2; }
    </style>
</head>
<body class="text-slate-800 antialiased min-h-screen flex flex-col">

    <!-- Floating Capsule Navigation Bar (Matching User Design Mockup) -->
    <header class="sticky top-0 z-50 pt-4 pb-2 px-4">
        <div class="max-w-4xl mx-auto bg-amber-950/95 backdrop-blur-xl border border-amber-800/40 text-amber-100 rounded-full p-1.5 shadow-2xl flex items-center justify-between">
            
            <!-- Left Circular Brand Icon -->
            <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2 pl-1 group">
                <img src="{{ asset('images/NusaVerse.png') }}" alt="NusaVerse Logo" class="w-9 h-9 object-cover rounded-full shadow-md group-hover:scale-105 transition transform border border-amber-600/40">
                <span class="font-extrabold text-sm text-amber-50 pr-1.5 hidden sm:inline-block">NusaVerse</span>
            </a>

            <!-- Center Navigation Links -->
            <nav class="flex items-center space-x-1 sm:space-x-1.5 text-xs">
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'bg-amber-100 text-amber-950 font-bold px-4 py-1.5 rounded-full shadow-sm' : 'text-amber-200/80 hover:text-white hover:bg-amber-900/60 px-3 py-1.5 rounded-full transition font-semibold' }}">
                    Dashboard
                </a>
                <a href="{{ route('admin.heritages.index') }}" class="{{ request()->routeIs('admin.heritages.*') ? 'bg-amber-100 text-amber-950 font-bold px-4 py-1.5 rounded-full shadow-sm' : 'text-amber-200/80 hover:text-white hover:bg-amber-900/60 px-3 py-1.5 rounded-full transition font-semibold' }}">
                    Cagar Budaya
                </a>
                <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'bg-amber-100 text-amber-950 font-bold px-4 py-1.5 rounded-full shadow-sm' : 'text-amber-200/80 hover:text-white hover:bg-amber-900/60 px-3 py-1.5 rounded-full transition font-semibold' }}">
                    Kategori
                </a>
                <a href="{{ route('admin.provinces.index') }}" class="{{ request()->routeIs('admin.provinces.*') ? 'bg-amber-100 text-amber-950 font-bold px-4 py-1.5 rounded-full shadow-sm' : 'text-amber-200/80 hover:text-white hover:bg-amber-900/60 px-3 py-1.5 rounded-full transition font-semibold' }}">
                    Provinsi / Kota
                </a>
                <a href="{{ route('admin.quizzes.index') }}" class="{{ request()->routeIs('admin.quizzes.*') ? 'bg-amber-100 text-amber-950 font-bold px-4 py-1.5 rounded-full shadow-sm' : 'text-amber-200/80 hover:text-white hover:bg-amber-900/60 px-3 py-1.5 rounded-full transition font-semibold' }}">
                    Kuis Sejarah
                </a>
            </nav>

            <!-- Right Side Logout Button -->
            <div class="pr-1 flex items-center">
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-3.5 py-1.5 bg-rose-900/60 hover:bg-rose-800 text-rose-200 hover:text-white rounded-full text-xs font-semibold border border-rose-700/50 transition flex items-center space-x-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @yield('content')
    </main>

    <!-- Floating Flash Toast Notifications (SweetAlert2 Toast) -->
    @if(session('success'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3500,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer);
                        toast.addEventListener('mouseleave', Swal.resumeTimer);
                    }
                });

                Toast.fire({
                    icon: 'success',
                    iconColor: '#34d399',
                    title: '{{ session('success') }}',
                    background: '#1c100b',
                    color: '#fdfbf7',
                    customClass: {
                        popup: 'rounded-2xl border border-amber-700/50 shadow-2xl p-4 text-xs font-semibold'
                    }
                });
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3500,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer);
                        toast.addEventListener('mouseleave', Swal.resumeTimer);
                    }
                });

                Toast.fire({
                    icon: 'error',
                    iconColor: '#f43f5e',
                    title: '{{ session('error') }}',
                    background: '#1c100b',
                    color: '#fdfbf7',
                    customClass: {
                        popup: 'rounded-2xl border border-rose-700/50 shadow-2xl p-4 text-xs font-semibold'
                    }
                });
            });
        </script>
    @endif

    <!-- Ultra-Modern Floating Footer -->
    <footer class="mt-auto pt-6 pb-6 px-4">
        <div class="max-w-4xl mx-auto bg-gradient-to-r from-amber-950 via-amber-900 to-amber-950 border border-amber-800/40 text-amber-100 rounded-3xl p-4 sm:px-6 shadow-xl flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
            
            <!-- Left Side: Brand Badging -->
            <div class="flex items-center space-x-3">
                <img src="{{ asset('images/NusaVerse.png') }}" alt="NusaVerse Logo" class="w-8 h-8 object-cover rounded-full shadow-md border border-amber-600/40">
                <div>
                    <h4 class="font-extrabold text-amber-100 tracking-wide text-xs">NusaVerse Controller</h4>
                    <p class="text-[11px] text-amber-300/60">Pusat Kelola Data Cagar Budaya</p>
                </div>
            </div>

            <!-- Middle: Download Mobile APK Card -->
            <a href="https://drive.google.com/drive/folders/1ovihf2ZKV-7h-7FY0OqY_1JE5e59XGMt?usp=sharing" target="_blank" rel="noopener noreferrer" class="flex items-center space-x-2.5 bg-gradient-to-r from-amber-900/80 to-amber-950/80 hover:from-amber-800 hover:to-amber-900 border border-amber-700/50 hover:border-amber-500/80 px-4 py-2 rounded-2xl text-xs text-amber-100 shadow-md hover:shadow-xl transition transform hover:-translate-y-0.5 group">
                <div class="w-7 h-7 rounded-xl bg-amber-700/50 border border-amber-500/40 text-amber-200 flex items-center justify-center group-hover:scale-110 transition transform">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18l-6-6h4V4h4v8h4l-6 6zM4 20h16"></path>
                    </svg>
                </div>
                <div class="flex items-center space-x-1.5 font-bold text-amber-100 group-hover:text-white transition">
                    <span>Unduh Aplikasi Mobile (APK)</span>
                    <svg class="w-3.5 h-3.5 text-amber-400 group-hover:translate-x-0.5 transition transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                </div>
            </a>

            <!-- Right Side: Copyright Pill -->
            <div class="flex items-center space-x-2">
                <span class="px-3.5 py-1.5 rounded-full bg-amber-900/40 border border-amber-700/30 text-amber-200/90 font-medium text-[11px] shadow-sm">
                    &copy; 2026 <strong class="text-amber-100 font-bold">ZevenDev</strong>
                </span>
            </div>

        </div>
    </footer>

</body>
</html>
