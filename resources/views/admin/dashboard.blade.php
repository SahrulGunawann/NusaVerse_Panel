@extends('layouts.admin')

@section('content')
<div class="space-y-8">
    <!-- Header Hero Banner -->
    <div class="bg-gradient-to-br from-amber-950 via-amber-900 to-amber-950 text-white rounded-3xl p-6 sm:p-8 shadow-2xl relative overflow-hidden border border-amber-800/40">
        <!-- Ambient Glow Backdrops -->
        <div class="absolute -right-12 -bottom-12 w-64 h-64 bg-amber-500/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute right-1/3 -top-16 w-48 h-48 bg-amber-600/10 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div class="space-y-3 max-w-2xl">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-amber-50 leading-tight">
                    Selamat Datang di NusaVerse Controller Panel
                </h2>

                <p class="text-amber-200/80 text-xs sm:text-sm leading-relaxed">
                    Pusat manajemen data cagar budaya Nusantara & kuis interaktif. Tambahkan situs sejarah baru, kelola model 3D (<code class="text-amber-300 font-mono bg-amber-950/60 px-1.5 py-0.5 rounded border border-amber-800/40">.glb</code>), dan sinkronkan secara instan dengan aplikasi mobile.
                </p>
            </div>

            <!-- Database Action Box -->
            <div class="flex-shrink-0 bg-amber-950/60 border border-amber-800/60 rounded-2xl p-4 shadow-inner space-y-2">
                <span class="text-[11px] font-bold text-amber-300/80 uppercase tracking-wider block">Manajemen Database</span>
                <form action="{{ route('admin.resetDb') }}" method="POST" onsubmit="return confirmResetDb(event, this);">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2.5 bg-rose-950/90 hover:bg-rose-900 text-rose-200 border border-rose-700/60 font-bold text-xs rounded-xl shadow-lg transition flex items-center justify-center space-x-2 group">
                        <span class="group-hover:scale-110 transition transform">⚠️</span>
                        <span>Kosongkan Seluruh Database</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- 1. Cagar Budaya Card -->
        <a href="{{ route('admin.heritages.index') }}" class="bg-white p-6 rounded-3xl shadow-sm border border-amber-900/10 hover:shadow-xl hover:border-amber-700/30 transition-all duration-300 group flex items-center justify-between block">
            <div class="space-y-1">
                <p class="text-[11px] font-bold text-amber-800 uppercase tracking-widest flex items-center space-x-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                    <span>Cagar Budaya</span>
                </p>
                <h3 class="text-3xl font-black text-slate-900 group-hover:text-amber-900 transition">{{ $totalHeritages }}</h3>
                <p class="text-xs text-slate-400 font-medium">Situs Terdaftar di System</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-700 to-amber-950 text-amber-100 flex items-center justify-center shadow-lg group-hover:scale-110 transition transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0-4h4m-4 0V3m-4 8h4m-4 0V7m0 4H7m4 0v4m0 0h4m-4 0v4"></path>
                </svg>
            </div>
        </a>

        <!-- 2. Kategori Card -->
        <a href="{{ route('admin.categories.index') }}" class="bg-white p-6 rounded-3xl shadow-sm border border-amber-900/10 hover:shadow-xl hover:border-amber-700/30 transition-all duration-300 group flex items-center justify-between block">
            <div class="space-y-1">
                <p class="text-[11px] font-bold text-amber-800 uppercase tracking-widest flex items-center space-x-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                    <span>Kategori</span>
                </p>
                <h3 class="text-3xl font-black text-slate-900 group-hover:text-amber-900 transition">{{ $totalCategories }}</h3>
                <p class="text-xs text-slate-400 font-medium">Klasifikasi Jenis Budaya</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-800 to-amber-950 text-amber-100 flex items-center justify-center shadow-lg group-hover:scale-110 transition transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
            </div>
        </a>

        <!-- 3. Provinsi / Kota Card -->
        <a href="{{ route('admin.provinces.index') }}" class="bg-white p-6 rounded-3xl shadow-sm border border-amber-900/10 hover:shadow-xl hover:border-amber-700/30 transition-all duration-300 group flex items-center justify-between block">
            <div class="space-y-1">
                <p class="text-[11px] font-bold text-amber-800 uppercase tracking-widest flex items-center space-x-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                    <span>Provinsi / Kota</span>
                </p>
                <h3 class="text-3xl font-black text-slate-900 group-hover:text-amber-900 transition">{{ $totalProvinces }}</h3>
                <p class="text-xs text-slate-400 font-medium">Wilayah Persebaran Budaya</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-700 to-amber-900 text-amber-100 flex items-center justify-center shadow-lg group-hover:scale-110 transition transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
        </a>

        <!-- 4. Kuis Sejarah Card -->
        <a href="{{ route('admin.quizzes.index') }}" class="bg-white p-6 rounded-3xl shadow-sm border border-amber-900/10 hover:shadow-xl hover:border-amber-700/30 transition-all duration-300 group flex items-center justify-between block">
            <div class="space-y-1">
                <p class="text-[11px] font-bold text-amber-800 uppercase tracking-widest flex items-center space-x-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                    <span>Kuis Sejarah</span>
                </p>
                <h3 class="text-3xl font-black text-slate-900 group-hover:text-amber-900 transition">{{ $totalQuizzes }}</h3>
                <p class="text-xs text-slate-400 font-medium">Paket Kuis Edukasi Active</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-600 to-amber-900 text-amber-100 flex items-center justify-center shadow-lg group-hover:scale-110 transition transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                </svg>
            </div>
        </a>
    </div>

    <!-- Recent Heritages List -->
    <div class="bg-white rounded-3xl shadow-sm border border-amber-900/10 p-6 space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-900">Cagar Budaya Terbaru</h3>
            <a href="{{ route('admin.heritages.create') }}" class="px-4 py-2 bg-amber-700 hover:bg-amber-800 text-white font-semibold text-xs rounded-xl shadow-md transition flex items-center">
                + Tambah Baru
            </a>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse($heritages as $item)
                <div class="py-4 flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        @php
                            $words = explode(' ', trim($item->name));
                            $initials = count($words) >= 2 
                                ? mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1) 
                                : mb_substr($words[0], 0, 2);
                            $initials = strtoupper($initials);
                            $hasCover = !empty($item->cover_image) && $item->cover_image !== 'none';
                        @endphp
                        <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-200 overflow-hidden flex-shrink-0 relative flex items-center justify-center">
                            @if($hasCover)
                                <img src="{{ Str::startsWith($item->cover_image, 'http') || Str::startsWith($item->cover_image, 'assets') ? asset($item->cover_image) : asset('storage/' . $item->cover_image) }}" class="w-full h-full object-cover" alt="{{ $item->name }}" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden'); this.nextElementSibling.classList.add('flex');">
                            @endif
                            <div class="w-full h-full bg-gradient-to-br from-amber-700 to-amber-950 text-amber-100 font-extrabold items-center justify-center text-sm tracking-wider {{ $hasCover ? 'hidden' : 'flex' }}">
                                {{ $initials }}
                            </div>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900 text-sm">{{ $item->name }}</h4>
                            <p class="text-xs text-slate-500">{{ $item->category_name }} &middot; {{ $item->province_name }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('admin.heritages.edit', $item->id) }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition">Edit</a>
                    </div>
                </div>
            @empty
                <p class="text-xs text-slate-400 py-6 text-center">Belum ada data cagar budaya.</p>
            @endforelse
        </div>
    </div>
</div>

<script>
function confirmResetDb(e, form) {
    e.preventDefault();
    Swal.fire({
        title: 'Kosongkan Database System?',
        html: `
            <div class="text-left space-y-3">
                <p class="text-sm text-slate-600">Apakah Anda YAKIN ingin menghapus <strong>SELURUH isi database</strong>?</p>
                <div class="bg-rose-50 border border-rose-200 text-rose-800 p-3.5 rounded-2xl text-xs space-y-1.5">
                    <p class="font-bold text-rose-900">⚠️ Perhatian Khusus:</p>
                    <ul class="list-disc list-inside text-rose-800 space-y-1 pl-1">
                        <li>Seluruh data Cagar Budaya akan terhapus</li>
                        <li>Seluruh Kuis Sejarah & Soal akan terhapus</li>
                        <li>Seluruh Kategori & Provinsi akan dibersihkan</li>
                    </ul>
                </div>
                <p class="text-xs text-slate-400 text-center">Tindakan ini permanen dan tidak dapat dibatalkan.</p>
            </div>
        `,
        icon: 'warning',
        iconColor: '#e11d48',
        showCancelButton: true,
        confirmButtonColor: '#be123c',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Kosongkan Database!',
        cancelButtonText: 'Batal',
        customClass: {
            popup: 'rounded-3xl p-6 border border-slate-100 shadow-2xl',
            confirmButton: 'px-5 py-2.5 rounded-xl font-bold text-xs shadow-md',
            cancelButton: 'px-5 py-2.5 rounded-xl font-bold text-xs'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
}
</script>
@endsection
