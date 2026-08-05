@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">Tambah Provinsi / Kota Baru</h2>
            <p class="text-xs text-slate-500">Tambahkan lokasi provinsi atau kota cagar budaya di Indonesia</p>
        </div>
        <a href="{{ route('admin.provinces.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl hover:bg-slate-300 transition">
            &larr; Kembali
        </a>
    </div>

    <form action="{{ route('admin.provinces.store') }}" method="POST" class="bg-white rounded-3xl p-8 shadow-sm border border-amber-900/10 space-y-6">
        @csrf

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Provinsi / Kota</label>
            <input type="text" name="name" required placeholder="Contoh: Jawa Tengah, DKI Jakarta, Bandung, Bali, dll." class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
        </div>

        <div class="pt-4 flex justify-end">
            <button type="submit" class="px-8 py-3 bg-amber-700 hover:bg-amber-800 text-white font-bold text-sm rounded-2xl shadow-lg transition">
                Simpan Lokasi
            </button>
        </div>
    </form>
</div>
@endsection
