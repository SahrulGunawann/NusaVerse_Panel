@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">Tambah Cagar Budaya Baru</h2>
            <p class="text-xs text-slate-500">Input data cagar budaya, linimasa sejarah, titik informasi 3D, file 3D (.glb), dan koordinat peta</p>
        </div>
        <a href="{{ route('admin.heritages.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl hover:bg-slate-300 transition">
            &larr; Kembali
        </a>
    </div>

    <form action="{{ route('admin.heritages.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-3xl p-8 shadow-sm border border-amber-900/10 space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Cagar Budaya</label>
                <input type="text" name="name" required placeholder="Contoh: Monumen Nasional" class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kategori</label>
                <select name="category_name" required class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Provinsi / Kota</label>
                <select name="province_name" required class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    @foreach($provinces as $prov)
                        <option value="{{ $prov->name }}">{{ $prov->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Latitude</label>
                <input type="number" step="any" name="latitude" required placeholder="-6.175392" class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Longitude</label>
                <input type="number" step="any" name="longitude" required placeholder="106.827153" class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Deskripsi Lengkap / Sejarah (Penjelasan Utama)</label>
            <textarea name="full_description" rows="5" required placeholder="Tuliskan sejarah dan penjelasan lengkap tentang cagar budaya ini..." class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"></textarea>
        </div>

        <!-- DYNAMIC REPEATER: SUB-JUDUL & PENJELASAN TAMBAHAN (OPSIONAL) -->
        <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Sub-Judul & Penjelasan Tambahan (Opsional)</h4>
                    <p class="text-[11px] text-slate-500">Tambahkan paragraf atau bab penjelas spesifik seperti "Arsitektur", "Makna Simbolis", dll.</p>
                </div>
                <button type="button" id="add-section-btn" class="px-4 py-2 bg-amber-100 hover:bg-amber-200 text-amber-900 font-bold text-xs rounded-xl transition">
                    + Tambah Sub-Judul (Opsional)
                </button>
            </div>

            <div id="sections-container" class="space-y-3">
                <!-- Section rows appended via JS -->
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6 bg-amber-50/50 rounded-2xl border border-amber-200">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Gambar Sampul (Cover Image)</label>
                <input type="file" name="cover_image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-amber-700 file:text-white hover:file:bg-amber-800">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">File 3D Model (.glb / .gltf)</label>
                <input type="file" name="model_3d" accept=".glb,.gltf" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-amber-700 file:text-white hover:file:bg-amber-800">
            </div>
        </div>

        <!-- DYNAMIC REPEATER: SUMBER & REFERENSI INFORMASI -->
        <div class="border-t border-slate-200 pt-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-md font-bold text-slate-900">Sumber & Referensi Informasi</h3>
                    <p class="text-xs text-slate-500">Tambahkan satu atau beberapa tautan referensi resmi cagar budaya ini</p>
                </div>
                <button type="button" id="add-source-btn" class="px-4 py-2 bg-amber-100 hover:bg-amber-200 text-amber-900 font-bold text-xs rounded-xl transition">
                    + Tambah Sumber Referensi
                </button>
            </div>

            <div id="sources-container" class="space-y-3">
                <!-- Starts empty, click + Tambah Sumber Referensi to add -->
            </div>
        </div>

        <!-- FEATURED HERITAGE OPTION -->
        <div class="p-5 bg-gradient-to-r from-amber-50 to-orange-50 rounded-2xl border border-amber-300 flex items-center justify-between shadow-sm">
            <div>
                <h4 class="text-xs font-extrabold text-amber-900 uppercase tracking-wider">Tampilkan di Bangunan Unggulan Beranda (Featured)</h4>
                <p class="text-[11px] text-amber-800">Aktifkan opsi ini agar cagar budaya ini ditampilkan secara utama pada slide Bangunan Unggulan di aplikasi mobile (Beranda)</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="hidden" name="is_featured" value="0">
                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} class="w-5 h-5 text-amber-600 rounded border-amber-300 focus:ring-amber-500">
            </label>
        </div>

        <!-- DYNAMIC REPEATER: LINIMASA SEJARAH -->
        <div class="border-t border-slate-200 pt-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-md font-bold text-slate-900">Linimasa Sejarah</h3>
                    <p class="text-xs text-slate-500">Tambahkan urutan peristiwa sejarah berdasarkan tahun/abad</p>
                </div>
                <button type="button" id="add-timeline-btn" class="px-4 py-2 bg-amber-100 hover:bg-amber-200 text-amber-900 font-bold text-xs rounded-xl transition">
                    + Tambah Peristiwa Sejarah
                </button>
            </div>

            <div id="timeline-container" class="space-y-4">
                <!-- Starts empty, click + Tambah Peristiwa Sejarah to add -->
            </div>
        </div>

        <!-- DYNAMIC REPEATER & LIVE PREVIEW: TITIK INFORMASI 3D -->
        <div class="border-t border-slate-200 pt-6 space-y-6">
            <div>
                <h3 class="text-md font-bold text-slate-900">Titik Informasi 3D (Point of Interest)</h3>
                <p class="text-xs text-slate-500">Kelola titik lokasi penjelas pada bagian spesifik objek 3D (Judul, Penjelasan, & Posisi X, Y, Z)</p>
            </div>

            <!-- LIVE INTERACTIVE 3D PREVIEW BOX FOR WEB ADMIN -->
            <div class="bg-slate-900 p-6 rounded-3xl border border-amber-500/30 space-y-4 shadow-inner">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-xs font-bold text-amber-400 uppercase tracking-wider">Pratinjau Live 3D & Lokasi Titik</h4>
                        <p class="text-[11px] text-slate-400">Titik pin di bawah bergeser secara real-time mengikuti koordinat X, Y, Z yang Anda ketik</p>
                    </div>
                    <span class="px-3 py-1 bg-amber-500/20 text-amber-300 text-[10px] font-bold rounded-full border border-amber-500/40">
                        Interactive Live Preview
                    </span>
                </div>

                <div id="preview-box" class="relative w-full h-80 bg-slate-950 rounded-2xl overflow-hidden border border-slate-800 flex items-center justify-center">
                    <model-viewer
                        id="web-admin-3d-viewer"
                        src="{{ url('/api/v1/media/models/model.glb') }}"
                        alt="3D Model Preview"
                        camera-controls
                        shadow-intensity="1"
                        camera-orbit="0deg 75deg 105%"
                        max-camera-orbit="auto 90deg auto"
                        min-camera-orbit="auto 0deg auto"
                        bounds="tight"
                        style="width: 100%; height: 100%; background-color: #090d16;"
                    >
                    </model-viewer>

                    <!-- Overlay Dynamic Hotspot Pins -->
                    <div id="web-hotspots-overlay" class="absolute inset-0 pointer-events-none"></div>
                </div>

                <!-- Button Bar Underneath Preview Card -->
                <div class="pt-1 flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <button type="button" id="toggle-web-hotspots-btn" onclick="toggleWebHotspots()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-amber-300 font-bold text-xs rounded-xl border border-amber-500/30 transition">
                            <span id="toggle-hotspots-text">Sembunyikan Titik Info</span>
                        </button>

                        <button type="button" onclick="resetWebAdminCamera()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs rounded-xl border border-slate-700 transition">
                            <span>Reset Kamera</span>
                        </button>
                    </div>

                    <button type="button" id="add-hotspot-btn" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold text-xs rounded-xl shadow-md transition flex items-center space-x-1.5">
                        <span>+ Tambah Titik Informasi 3D</span>
                    </button>
                </div>
            </div>

            <div id="hotspot-container" class="space-y-4">
                <!-- Starts empty, click + Tambah Titik Informasi 3D to add -->
            </div>
        </div>



        <div class="pt-4 flex justify-end">
            <button type="submit" class="px-8 py-3 bg-amber-700 hover:bg-amber-800 text-white font-bold text-sm rounded-2xl shadow-lg transition">
                Simpan Cagar Budaya
            </button>
        </div>
    </form>
</div>

<script>
    let sectionCount = 0;
    document.getElementById('add-section-btn').addEventListener('click', function () {
        const container = document.getElementById('sections-container');
        const div = document.createElement('div');
        div.className = 'section-row p-4 bg-white rounded-xl border border-slate-200 space-y-2 relative shadow-sm';
        div.innerHTML = `
            <button type="button" onclick="this.parentElement.remove();" class="absolute top-3 right-3 text-rose-500 hover:text-rose-700 text-xs font-bold px-2 py-1 bg-rose-50 rounded-lg">Hapus</button>
            <div>
                <label class="block text-[11px] font-bold text-slate-600 mb-1">Sub-Judul Paragraf</label>
                <input type="text" name="additional_sections[${sectionCount}][title]" placeholder="Contoh: Arsitektur & Ornamen" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-amber-500">
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-600 mb-1">Penjelasan Detail Sub-Judul</label>
                <textarea name="additional_sections[${sectionCount}][content]" rows="3" placeholder="Tuliskan penjelasan untuk bagian ini..." class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-amber-500"></textarea>
            </div>
        `;
        container.appendChild(div);
        sectionCount++;
    });

    let sourceCount = 0;
    document.getElementById('add-source-btn').addEventListener('click', function () {
        const container = document.getElementById('sources-container');
        const div = document.createElement('div');
        div.className = 'source-row p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-2 relative';
        div.innerHTML = `
            <button type="button" onclick="this.parentElement.remove()" class="absolute top-3 right-3 text-rose-500 hover:text-rose-700 text-xs font-bold px-2 py-1 bg-rose-50 rounded-lg">Hapus</button>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Nama Sumber / Penerbit</label>
                    <input type="text" name="sources[${sourceCount}][name]" placeholder="Contoh: Kemdikbud Kebudayaan" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Link Web Sumber (URL)</label>
                    <input type="url" name="sources[${sourceCount}][url]" placeholder="Contoh: https://kebudayaan.kemdikbud.go.id/..." class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-amber-500">
                </div>
            </div>
        `;
        container.appendChild(div);
        sourceCount++;
    });

    let timelineCount = 0;
    document.getElementById('add-timeline-btn').addEventListener('click', function () {
        const container = document.getElementById('timeline-container');
        const div = document.createElement('div');
        div.className = 'timeline-row p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-3 relative';
        div.innerHTML = `
            <button type="button" onclick="this.parentElement.remove()" class="absolute top-3 right-3 text-rose-500 hover:text-rose-700 text-xs font-bold px-2 py-1 bg-rose-50 rounded-lg">Hapus</button>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Tahun / Periode</label>
                    <input type="text" name="timeline_events[${timelineCount}][year]" placeholder="Contoh: 1975" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-amber-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Judul Peristiwa</label>
                    <input type="text" name="timeline_events[${timelineCount}][title]" placeholder="Contoh: Peresmian Monumen" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-amber-500">
                </div>
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-600 mb-1">Deskripsi Peristiwa Sejarah</label>
                <textarea name="timeline_events[${timelineCount}][description]" rows="2" placeholder="Penjelasan singkat..." class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-amber-500"></textarea>
            </div>
        `;
        container.appendChild(div);
        timelineCount++;
    });

    let hotspotCount = 0;
    document.getElementById('add-hotspot-btn').addEventListener('click', function () {
        const container = document.getElementById('hotspot-container');
        const div = document.createElement('div');
        div.className = 'hotspot-row p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-3 relative';
        div.innerHTML = `
            <button type="button" onclick="this.parentElement.remove(); updateWebAdminHotspots();" class="absolute top-3 right-3 text-rose-500 hover:text-rose-700 text-xs font-bold px-2 py-1 bg-rose-50 rounded-lg">Hapus</button>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div class="md:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Judul Titik Info</label>
                    <input type="text" name="hotspot_items[${hotspotCount}][title]" placeholder="Contoh: Pelataran Cawan" class="hotspot-input w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-amber-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Koordinat Posisi (X, Y, Z)</label>
                    <div class="grid grid-cols-3 gap-2">
                        <input type="number" step="any" name="hotspot_items[${hotspotCount}][x]" value="0" placeholder="X" class="hotspot-input w-full px-2 py-2 rounded-xl border border-slate-200 text-xs text-center focus:ring-2 focus:ring-amber-500">
                        <input type="number" step="any" name="hotspot_items[${hotspotCount}][y]" value="0.8" placeholder="Y" class="hotspot-input w-full px-2 py-2 rounded-xl border border-slate-200 text-xs text-center focus:ring-2 focus:ring-amber-500">
                        <input type="number" step="any" name="hotspot_items[${hotspotCount}][z]" value="0" placeholder="Z" class="hotspot-input w-full px-2 py-2 rounded-xl border border-slate-200 text-xs text-center focus:ring-2 focus:ring-amber-500">
                    </div>
                </div>
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-600 mb-1">Penjelasan Detail Bagian Ini</label>
                <textarea name="hotspot_items[${hotspotCount}][description]" rows="2" placeholder="Penjelasan bagian..." class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-amber-500"></textarea>
            </div>
        `;
        container.appendChild(div);
        hotspotCount++;
        attachHotspotListeners();
        updateWebAdminHotspots();
    });

    function resetWebAdminCamera() {
        const viewer = document.getElementById('web-admin-3d-viewer');
        if (viewer) {
            viewer.cameraOrbit = '0deg 75deg 105%';
            viewer.cameraTarget = 'auto auto auto';
            viewer.fieldOfView = 'auto';
            if (typeof viewer.resetTurntableRotation === 'function') {
                viewer.resetTurntableRotation();
            }
            if (typeof viewer.jumpToGoal === 'function') {
                viewer.jumpToGoal();
            }
        }
    }

    let showWebHotspots = true;
    function toggleWebHotspots() {
        showWebHotspots = !showWebHotspots;
        const btnText = document.getElementById('toggle-hotspots-text');
        if (btnText) {
            btnText.textContent = showWebHotspots ? 'Sembunyikan Titik Info' : 'Tampilkan Titik Info';
        }
        updateWebAdminHotspots();
    }

    // Real-time Live 3D Hotspot Overlay Projection script for Web Admin
    function updateWebAdminHotspots() {
        const overlay = document.getElementById('web-hotspots-overlay');
        const box = document.getElementById('preview-box');
        if (!overlay || !box) return;

        overlay.innerHTML = '';
        if (!showWebHotspots) return;

        const rows = document.querySelectorAll('.hotspot-row');

        const boxWidth = box.clientWidth || 600;
        const boxHeight = box.clientHeight || 320;

        rows.forEach((row, idx) => {
            const titleInput = row.querySelector('input[name*="[title]"]');
            const xInput = row.querySelector('input[name*="[x]"]');
            const yInput = row.querySelector('input[name*="[y]"]');

            const title = titleInput ? titleInput.value || `Titik #${idx+1}` : `Titik #${idx+1}`;
            const x = xInput ? parseFloat(xInput.value) || 0 : 0;
            const y = yInput ? parseFloat(yInput.value) || 0 : 0;

            const posX = (boxWidth / 2) + (x * 70.0) - 45;
            const posY = (boxHeight * 0.48) - (y * 65.0) - 14;

            const clampedX = Math.max(10, Math.min(boxWidth - 110, posX));
            const clampedY = Math.max(15, Math.min(boxHeight - 35, posY));

            const pin = document.createElement('div');
            pin.className = 'absolute flex items-center space-x-1.5 px-3 py-1.5 bg-amber-100 border-2 border-amber-600 rounded-full shadow-lg text-[11px] font-bold text-amber-950 transition-all duration-200';
            pin.style.left = clampedX + 'px';
            pin.style.top = clampedY + 'px';
            pin.innerHTML = `
                <span class="w-2.5 h-2.5 rounded-full bg-amber-600 animate-pulse"></span>
                <span>${title}</span>
            `;
            overlay.appendChild(pin);
        });
    }

    function attachHotspotListeners() {
        document.querySelectorAll('.hotspot-input').forEach(input => {
            input.removeEventListener('input', updateWebAdminHotspots);
            input.addEventListener('input', updateWebAdminHotspots);
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        attachHotspotListeners();
        updateWebAdminHotspots();
    });
</script>
@endsection
