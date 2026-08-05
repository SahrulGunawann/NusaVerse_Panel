<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login &middot; NusaVerse Controller</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
        }
    </style>
</head>
<body class="min-h-screen text-slate-800 bg-slate-100 flex items-center justify-center p-4 relative overflow-hidden">
    <!-- Subtle Background Glow -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-amber-500/10 rounded-full filter blur-[120px] pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-amber-700/10 rounded-full filter blur-[120px] pointer-events-none"></div>

    <!-- Login Container -->
    <div class="w-full max-w-md relative z-10 space-y-6">
        <!-- Logo & Header -->
        <div class="text-center space-y-3">
            <div class="inline-flex items-center space-x-3 px-4 py-2 bg-gradient-to-r from-amber-950 via-amber-900 to-amber-950 border border-amber-800/40 rounded-full shadow-lg">
                <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-amber-500 via-amber-600 to-amber-400 text-amber-950 font-black flex items-center justify-center text-xs tracking-tighter shadow-md">
                    NV
                </div>
                <span class="text-sm font-extrabold text-amber-100 tracking-wide">NusaVerse Controller</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Selamat Datang</h1>
            <p class="text-xs text-slate-500 font-medium max-w-sm mx-auto leading-relaxed">Mari kumpulkan dan lestarikan data edukasi cagar budaya untuk generasi masa depan yang kompeten.</p>
        </div>

        <!-- Clean White Card Form -->
        <div class="bg-white border border-amber-900/10 shadow-xl rounded-3xl p-6 sm:p-8 space-y-6">
            @if(session('success'))
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            iconColor: '#34d399',
                            title: "{{ session('success') }}",
                            showConfirmButton: false,
                            timer: 3500,
                            timerProgressBar: true,
                            background: '#1c100b',
                            color: '#fdfbf7',
                            customClass: {
                                popup: 'rounded-2xl border border-amber-700/50 shadow-2xl p-4 text-xs font-semibold'
                            }
                        });
                    });
                </script>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
                @csrf

                <!-- Username or Email Input -->
                <div class="space-y-1.5">
                    <label for="login" class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Username atau Email
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <input type="text" name="login" id="login" value="{{ old('login', $rememberedLogin ?? request()->cookie('last_remembered_login')) }}" required autofocus placeholder="Masukkan username atau email" class="w-full pl-10 pr-4 py-3 bg-slate-50 border @error('login') border-rose-500 @else border-slate-200 @enderror rounded-2xl text-xs font-semibold text-slate-900 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 focus:outline-none transition shadow-sm">
                    </div>
                    @error('login')
                        <p class="text-[11px] text-rose-600 font-semibold mt-1 flex items-center space-x-1">
                            <span>⚠️ {{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <!-- Password Input -->
                <div class="space-y-1.5">
                    <label for="password" class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Kata Sandi
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <input type="password" name="password" id="password" required placeholder="Masukkan kata sandi" class="w-full pl-10 pr-10 py-3 bg-slate-50 border @error('password') border-rose-500 @else border-slate-200 @enderror rounded-2xl text-xs font-semibold text-slate-900 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 focus:outline-none transition shadow-sm">
                        <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-amber-800 transition">
                            <svg id="eyeIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-[11px] text-rose-600 font-semibold mt-1 flex items-center space-x-1">
                            <span>⚠️ {{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between text-xs pt-1">
                    <label for="remember" class="flex items-center space-x-2 cursor-pointer group select-none">
                        <input type="checkbox" name="remember" id="remember" value="1" {{ old('remember') || !empty($rememberedLogin ?? request()->cookie('last_remembered_login')) ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-300 bg-slate-100 text-amber-700 focus:ring-amber-500 cursor-pointer">
                        <span class="text-slate-600 group-hover:text-slate-900 font-medium transition">Ingat Sesi Saya</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-3.5 px-4 bg-amber-700 hover:bg-amber-800 text-white font-extrabold text-xs rounded-2xl shadow-md hover:shadow-lg transition transform active:scale-[0.98] flex items-center justify-center space-x-2">
                    <span>Masuk ke Dashboard Controller</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </form>
        </div>

        <!-- Clean Footer Notice -->
        <p class="text-center text-xs text-slate-400 font-medium">
            &copy; 2026 NusaVerse Controller
        </p>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passInput = document.getElementById('password');
            if (passInput.type === 'password') {
                passInput.type = 'text';
            } else {
                passInput.type = 'password';
            }
        }
    </script>
</body>
</html>
