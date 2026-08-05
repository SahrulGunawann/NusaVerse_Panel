@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">Buat Kuis Baru</h2>
            <p class="text-xs text-slate-500">Pilih tipe kuis (Kuis Cagar Budaya atau Kuis Tantangan Custom)</p>
        </div>
        <a href="{{ route('admin.quizzes.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl hover:bg-slate-300 transition">
            &larr; Kembali
        </a>
    </div>

    <form action="{{ route('admin.quizzes.store') }}" method="POST" class="bg-white rounded-3xl p-8 shadow-sm border border-amber-900/10 space-y-6">
        @csrf

        <!-- Mode Selection -->
        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tipe Kuis</label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <label class="p-4 rounded-2xl border border-slate-200 cursor-pointer hover:border-amber-500 transition flex items-center space-x-3 bg-slate-50" id="mode-heritage-label">
                    <input type="radio" name="quiz_mode" value="heritage" checked onchange="toggleQuizMode(this.value)" class="text-amber-600 focus:ring-amber-500">
                    <div>
                        <span class="block text-sm font-bold text-slate-900">Kuis Spesifik Cagar Budaya</span>
                        <span class="text-xs text-slate-500">Terhubung langsung ke 1 Cagar Budaya tertentu</span>
                    </div>
                </label>
                <label class="p-4 rounded-2xl border border-slate-200 cursor-pointer hover:border-amber-500 transition flex items-center space-x-3 bg-slate-50" id="mode-custom-label">
                    <input type="radio" name="quiz_mode" value="custom" onchange="toggleQuizMode(this.value)" class="text-amber-600 focus:ring-amber-500">
                    <div>
                        <span class="block text-sm font-bold text-slate-900">Kuis Tantangan Custom / Gabungan</span>
                        <span class="text-xs text-slate-500">Gabungan soal dari beberapa kategori kuis</span>
                    </div>
                </label>
            </div>
        </div>

        <!-- Heritage Section -->
        <div id="section-heritage">
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Cagar Budaya Terkait</label>
            <select name="heritage_slug" class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 font-semibold text-slate-800">
                @foreach($heritages as $h)
                    <option value="{{ $h->slug }}">{{ $h->name }} ({{ $h->category_name }})</option>
                @endforeach
            </select>
        </div>

        <!-- Custom Section -->
        <div id="section-custom" class="hidden space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Judul Kuis Custom</label>
                <input type="text" name="title" placeholder="Contoh: Tantangan Mahakarya Kerajaan & Candi" class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Deskripsi Kuis</label>
                <textarea name="description" rows="2" placeholder="Jelaskan mengenai tantangan kuis ini..." class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Pilih Kategori Terkait (Dapat Dicentang Lebih Dari 1)</label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach($categories as $c)
                        <label class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl flex items-center space-x-2 text-xs font-semibold cursor-pointer">
                            <input type="checkbox" name="categories[]" value="{{ $c->name }}" class="rounded text-amber-600 focus:ring-amber-500">
                            <span>{{ $c->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Question Repeater -->
        <div class="border-t border-slate-200 pt-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-md font-bold text-slate-900">Daftar Pertanyaan Kuis</h3>
                    <p class="text-xs text-slate-500">Tambahkan soal kuis (Dimulai kosong, klik + Tambah Pertanyaan untuk menambah)</p>
                </div>
                <button type="button" id="add-question-btn" class="px-4 py-2 bg-amber-100 hover:bg-amber-200 text-amber-900 font-bold text-xs rounded-xl transition">
                    + Tambah Pertanyaan
                </button>
            </div>

            <div id="questions-container" class="space-y-4">
                <!-- Starts empty! -->
            </div>
        </div>

        <div class="pt-4 flex justify-end">
            <button type="submit" class="px-8 py-3 bg-amber-700 hover:bg-amber-800 text-white font-bold text-sm rounded-2xl shadow-lg transition">
                Simpan Kuis
            </button>
        </div>
    </form>
</div>

<script>
    function toggleQuizMode(mode) {
        if (mode === 'custom') {
            document.getElementById('section-heritage').classList.add('hidden');
            document.getElementById('section-custom').classList.remove('hidden');
        } else {
            document.getElementById('section-heritage').classList.remove('hidden');
            document.getElementById('section-custom').classList.add('hidden');
        }
    }

    let questionCount = 0;
    document.getElementById('add-question-btn').addEventListener('click', function () {
        const container = document.getElementById('questions-container');
        const div = document.createElement('div');
        div.className = 'question-row p-5 bg-slate-50 rounded-2xl border border-slate-200 space-y-3 relative';
        div.innerHTML = `
            <button type="button" onclick="this.parentElement.remove();" class="absolute top-3 right-3 text-rose-500 hover:text-rose-700 text-xs font-bold px-2 py-1 bg-rose-50 rounded-lg">Hapus</button>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">Pertanyaan #${questionCount + 1}</label>
                <input type="text" name="questions[${questionCount}][question]" required placeholder="Pertanyaan kuis..." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-amber-500 bg-white">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <input type="text" name="questions[${questionCount}][options][0]" required placeholder="Pilihan A" class="px-3 py-2 rounded-lg border border-slate-200 text-xs bg-white">
                <input type="text" name="questions[${questionCount}][options][1]" required placeholder="Pilihan B" class="px-3 py-2 rounded-lg border border-slate-200 text-xs bg-white">
                <input type="text" name="questions[${questionCount}][options][2]" required placeholder="Pilihan C" class="px-3 py-2 rounded-lg border border-slate-200 text-xs bg-white">
                <input type="text" name="questions[${questionCount}][options][3]" required placeholder="Pilihan D" class="px-3 py-2 rounded-lg border border-slate-200 text-xs bg-white">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Pilihan Jawaban Benar</label>
                    <select name="questions[${questionCount}][correct_index]" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs bg-white">
                        <option value="0">Pilihan A (Index 0)</option>
                        <option value="1">Pilihan B (Index 1)</option>
                        <option value="2">Pilihan C (Index 2)</option>
                        <option value="3">Pilihan D (Index 3)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Penjelasan Jawaban</label>
                    <input type="text" name="questions[${questionCount}][explanation]" placeholder="Penjelasan fakta..." class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs bg-white">
                </div>
            </div>
        `;
        container.appendChild(div);
        questionCount++;
    });
</script>
@endsection
