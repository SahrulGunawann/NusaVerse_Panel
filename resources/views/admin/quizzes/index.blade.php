@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">Kelola Kuis Sejarah</h2>
            <p class="text-xs text-slate-500">Daftar kuis dan soal-soal interaktif</p>
        </div>
        <div class="flex items-center space-x-3">
            <form action="{{ route('admin.quizzes.index') }}" method="GET" class="flex items-center space-x-2" id="searchForm">
                <div class="relative">
                    <input type="text" name="search" id="liveSearchInput" value="{{ request('search') }}" placeholder="Cari kuis..." class="w-48 sm:w-60 pl-9 pr-4 py-2 bg-white border border-amber-900/10 rounded-2xl text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none shadow-sm" autocomplete="off">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                @if(request('search'))
                    <a href="{{ route('admin.quizzes.index') }}" class="px-3 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-xl text-xs font-bold transition">Reset</a>
                @endif
            </form>
            <a href="{{ route('admin.quizzes.create') }}" class="px-4 py-2 bg-amber-700 hover:bg-amber-800 text-white font-semibold text-xs rounded-2xl shadow-md transition flex items-center whitespace-nowrap">
                + Tambah Baru
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6" id="quizGrid">
        @forelse($quizzes as $quiz)
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-amber-900/10 flex flex-col justify-between space-y-4 quiz-card">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 bg-amber-100 text-amber-800 rounded-full font-bold text-[10px] uppercase tracking-wider">{{ $quiz->category }}</span>
                        <span class="text-xs text-slate-400 font-semibold">{{ $quiz->questions->count() }} Soal</span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">{{ $quiz->title }}</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">{{ $quiz->description }}</p>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-2">
                    <a href="{{ route('admin.quizzes.edit', $quiz->id) }}" class="px-4 py-2 bg-amber-700 hover:bg-amber-800 text-white text-xs font-bold rounded-xl shadow-sm transition inline-block">
                        Kelola Soal Kuis &rarr;
                    </a>
                    <form action="{{ route('admin.quizzes.destroy', $quiz->id) }}" method="POST" class="inline-block" onsubmit="return confirmDeleteQuiz(event, this, '{{ addslashes($quiz->title) }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3 py-2 bg-rose-100 hover:bg-rose-200 text-rose-700 text-xs font-semibold rounded-xl transition">Hapus</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-2 bg-white rounded-3xl p-8 text-center text-slate-400 border border-slate-200">
                Belum ada kuis yang dibuat. Klik tombol di atas untuk membuat kuis baru.
            </div>
        @endforelse
    </div>

    @if($quizzes->hasPages())
        <div class="px-6 py-4 bg-white rounded-3xl border border-amber-900/10 shadow-sm">
            {{ $quizzes->links() }}
        </div>
    @endif
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById('liveSearchInput');
    if (searchInput) {
        if (searchInput.value) {
            searchInput.focus();
            const val = searchInput.value;
            searchInput.value = '';
            searchInput.value = val;
        }

        let debounceTimer = null;
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            const cards = document.querySelectorAll('.quiz-card');
            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                card.style.display = text.includes(query) ? '' : 'none';
            });

            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                this.form.submit();
            }, 500);
        });
    }
});

function confirmDeleteQuiz(e, form, title) {
    e.preventDefault();
    Swal.fire({
        title: 'Hapus Kuis Sejarah?',
        html: `<p class="text-sm text-slate-600 mb-1">Apakah Anda yakin ingin menghapus kuis</p><p class="text-base font-bold text-slate-900 mb-2">"${title}"?</p><p class="text-xs text-rose-500 font-semibold bg-rose-50 p-2.5 rounded-xl border border-rose-100 mt-2">⚠️ Tindakan ini permanen. Semua pertanyaan kuis ini akan terhapus.</p>`,
        icon: 'warning',
        iconColor: '#e11d48',
        showCancelButton: true,
        confirmButtonColor: '#e11d48',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus Kuis',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-3xl shadow-2xl border border-slate-100 p-6',
            confirmButton: 'px-5 py-2.5 rounded-2xl font-bold text-xs shadow-md',
            cancelButton: 'px-5 py-2.5 rounded-2xl font-semibold text-xs mr-3'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
    return false;
}
</script>
@endsection
