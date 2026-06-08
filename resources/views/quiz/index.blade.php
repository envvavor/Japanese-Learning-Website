@extends('layouts.app')

@section('title', 'Latihan Bebas — Manabu')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 font-sans" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-[#1cb0f6]/10 dark:bg-[#1899d6]/20 text-[#1cb0f6] dark:text-[#1899d6] rounded-2xl flex items-center justify-center text-3xl border-2 border-[#1cb0f6]/20 shrink-0">
                <i class="fas fa-dumbbell"></i>
            </div>
            <div>
                <h1 class="text-3xl font-black text-slate-800 dark:text-white tracking-wide uppercase">
                    Latihan Bebas (Beta)
                </h1>
                <p class="text-sm font-bold text-slate-500 dark:text-slate-400 mt-1">Uji pemahamanmu secara acak tanpa mengikuti Roadmap.</p>
                <p class="text-sm font-bold text-slate-500 dark:text-slate-400 mt-1">Fitur ini masih dalam pengembangan.</p>
            </div>
        </div>
        <a href="{{ route('quizzes.index') }}"
            class="inline-flex items-center justify-center px-5 py-3 border-2 border-b-[6px] border-slate-200 dark:border-gray-700 rounded-2xl text-sm font-black text-slate-600 dark:text-slate-300 bg-white dark:bg-gray-800 hover:bg-slate-50 dark:hover:bg-gray-700 active:border-b-2 active:translate-y-1 transition-all uppercase tracking-wider shrink-0">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    {{-- Error Banner --}}
    <div id="quizError" class="hidden mb-6 p-4 rounded-2xl bg-rose-50 dark:bg-rose-900/20 border-2 border-b-4 border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-400 font-bold"></div>

    {{-- Category Selection --}}
    <div class="bg-white dark:bg-gray-800 border-2 border-b-[6px] border-slate-200 dark:border-gray-700 rounded-[1.5rem] p-6 shadow-sm mb-6">
        <h2 class="text-lg font-black text-slate-800 dark:text-white uppercase tracking-wider mb-4 flex items-center gap-2">
            <i class="fas fa-folder-open text-[#1cb0f6]"></i> Pilih Kategori
        </h2>
        <div class="flex flex-wrap gap-3" id="categoryOptions">
            <button type="button" onclick="selectCategory(null)" data-category=""
                class="category-pill px-6 py-3 rounded-2xl text-sm font-black uppercase tracking-widest border-2 transition-all duration-150 border-b-[4px] border-[#1cb0f6] bg-[#1cb0f6]/10 text-[#1cb0f6] translate-y-0.5">
                Semua
            </button>
            <button type="button" onclick="selectCategory('hiragana')" data-category="hiragana"
                class="category-pill px-6 py-3 rounded-2xl text-sm font-black uppercase tracking-widest border-2 transition-all duration-150 border-b-[4px] border-slate-200 dark:border-gray-600 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-gray-700 active:translate-y-1 active:border-b-2">
                Hiragana
            </button>
            <button type="button" onclick="selectCategory('katakana')" data-category="katakana"
                class="category-pill px-6 py-3 rounded-2xl text-sm font-black uppercase tracking-widest border-2 transition-all duration-150 border-b-[4px] border-slate-200 dark:border-gray-600 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-gray-700 active:translate-y-1 active:border-b-2">
                Katakana
            </button>
            <button type="button" onclick="selectCategory('kanji')" data-category="kanji"
                class="category-pill px-6 py-3 rounded-2xl text-sm font-black uppercase tracking-widest border-2 transition-all duration-150 border-b-[4px] border-slate-200 dark:border-gray-600 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-gray-700 active:translate-y-1 active:border-b-2">
                Kanji
            </button>
        </div>
    </div>

    {{-- Quiz Type Selection --}}
    <div class="bg-white dark:bg-gray-800 border-2 border-b-[6px] border-slate-200 dark:border-gray-700 rounded-[1.5rem] p-6 shadow-sm mb-6">
        <h2 class="text-lg font-black text-slate-800 dark:text-white uppercase tracking-wider mb-4 flex items-center gap-2">
            <i class="fas fa-bullseye text-[#1cb0f6]"></i> Jenis Quiz
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4" id="quizTypeOptions">
            <button type="button" onclick="selectQuizType('multiple_choice')" data-type="multiple_choice"
                class="quiz-type-card p-5 rounded-2xl border-2 border-b-[6px] border-slate-200 dark:border-gray-600 text-left transition-all duration-150 hover:bg-slate-50 dark:hover:bg-gray-700 active:translate-y-1 active:border-b-2 group">
                <div class="flex items-center gap-3 mb-2">
                    <span class="w-12 h-12 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 text-indigo-500 flex items-center justify-center text-xl font-black">
                        A
                    </span>
                    <h3 class="font-black text-slate-800 dark:text-white uppercase tracking-wide">Pilihan Ganda</h3>
                </div>
                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 ml-[60px]">Pilih jawaban yang benar</p>
            </button>

            <button type="button" onclick="selectQuizType('drawing')" data-type="drawing"
                class="quiz-type-card p-5 rounded-2xl border-2 border-b-[6px] border-slate-200 dark:border-gray-600 text-left transition-all duration-150 hover:bg-slate-50 dark:hover:bg-gray-700 active:translate-y-1 active:border-b-2 group">
                <div class="flex items-center gap-3 mb-2">
                    <span class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 text-emerald-500 flex items-center justify-center text-xl">
                        <i class="fas fa-pencil-alt"></i>
                    </span>
                    <h3 class="font-black text-slate-800 dark:text-white uppercase tracking-wide">Menggambar</h3>
                </div>
                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 ml-[60px]">Tulis karakter dengan goresan</p>
            </button>

            <button type="button" onclick="selectQuizType('listening')" data-type="listening"
                class="quiz-type-card p-5 rounded-2xl border-2 border-b-[6px] border-slate-200 dark:border-gray-600 text-left transition-all duration-150 hover:bg-slate-50 dark:hover:bg-gray-700 active:translate-y-1 active:border-b-2 group">
                <div class="flex items-center gap-3 mb-2">
                    <span class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-900/30 text-amber-500 flex items-center justify-center text-xl">
                        <i class="fas fa-headphones"></i>
                    </span>
                    <h3 class="font-black text-slate-800 dark:text-white uppercase tracking-wide">Mendengarkan</h3>
                </div>
                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 ml-[60px]">Dengarkan dan jawab</p>
            </button>

            <button type="button" onclick="selectQuizType('matching')" data-type="matching"
                class="quiz-type-card p-5 rounded-2xl border-2 border-b-[6px] border-slate-200 dark:border-gray-600 text-left transition-all duration-150 hover:bg-slate-50 dark:hover:bg-gray-700 active:translate-y-1 active:border-b-2 group">
                <div class="flex items-center gap-3 mb-2">
                    <span class="w-12 h-12 rounded-xl bg-pink-100 dark:bg-pink-900/30 text-pink-500 flex items-center justify-center text-xl">
                        <i class="fas fa-puzzle-piece"></i>
                    </span>
                    <h3 class="font-black text-slate-800 dark:text-white uppercase tracking-wide">Mencocokkan</h3>
                </div>
                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 ml-[60px]">Pasangkan kata dengan arti</p>
            </button>

            <button type="button" onclick="selectQuizType('mixed')" data-type="mixed"
                class="quiz-type-card p-5 rounded-2xl border-2 border-b-[6px] border-slate-200 dark:border-gray-600 text-left transition-all duration-150 hover:bg-slate-50 dark:hover:bg-gray-700 active:translate-y-1 active:border-b-2 group">
                <div class="flex items-center gap-3 mb-2">
                    <span class="w-12 h-12 rounded-xl bg-violet-100 dark:bg-violet-900/30 text-violet-500 flex items-center justify-center text-xl">
                        <i class="fas fa-random"></i>
                    </span>
                    <h3 class="font-black text-slate-800 dark:text-white uppercase tracking-wide">Campuran</h3>
                </div>
                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 ml-[60px]">Kombinasi semua soal</p>
            </button>
        </div>
    </div>

    {{-- Question Count --}}
    <div class="bg-white dark:bg-gray-800 border-2 border-b-[6px] border-slate-200 dark:border-gray-700 rounded-[1.5rem] p-6 shadow-sm mb-6">
        <h2 class="text-lg font-black text-slate-800 dark:text-white uppercase tracking-wider mb-4 flex items-center gap-2">
            <i class="fas fa-list-ol text-[#1cb0f6]"></i> Jumlah Soal
        </h2>
        <div class="flex flex-wrap sm:flex-nowrap gap-3" id="questionCountOptions">
            <button type="button" onclick="selectQuestionCount(5)" data-count="5"
                class="count-pill flex-1 min-w-[60px] py-4 rounded-2xl text-base font-black border-2 transition-all duration-150 border-b-[4px] border-slate-200 dark:border-gray-600 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-gray-700 active:translate-y-1 active:border-b-2">
                5
            </button>
            <button type="button" onclick="selectQuestionCount(10)" data-count="10"
                class="count-pill flex-1 min-w-[60px] py-4 rounded-2xl text-base font-black border-2 transition-all duration-150 border-b-[4px] border-[#1cb0f6] bg-[#1cb0f6]/10 text-[#1cb0f6] translate-y-0.5">
                10
            </button>
            <button type="button" onclick="selectQuestionCount(15)" data-count="15"
                class="count-pill flex-1 min-w-[60px] py-4 rounded-2xl text-base font-black border-2 transition-all duration-150 border-b-[4px] border-slate-200 dark:border-gray-600 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-gray-700 active:translate-y-1 active:border-b-2">
                15
            </button>
            <button type="button" onclick="selectQuestionCount(20)" data-count="20"
                class="count-pill flex-1 min-w-[60px] py-4 rounded-2xl text-base font-black border-2 transition-all duration-150 border-b-[4px] border-slate-200 dark:border-gray-600 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-gray-700 active:translate-y-1 active:border-b-2">
                20
            </button>
        </div>
    </div>

    {{-- Start Button --}}
    <button id="startQuizBtn" onclick="startQuiz()"
        class="w-full py-5 rounded-[2rem] text-white font-black text-xl uppercase tracking-widest bg-[#1cb0f6] border-2 border-b-[8px] border-[#1899d6] hover:brightness-110 active:border-b-2 active:translate-y-2 transition-all flex items-center justify-center gap-3">
        <i class="fas fa-play"></i>
        <span id="startBtnText">Mulai Quiz</span>
        <i id="startBtnSpinner" class="fas fa-circle-notch fa-spin hidden"></i>
    </button>

    <div class="text-center mt-6">
        <a href="{{ route('quiz.history') }}" class="inline-flex items-center text-sm font-black text-[#1cb0f6] hover:text-[#1899d6] uppercase tracking-wider transition-colors">
            <i class="fas fa-history mr-2"></i> Lihat Riwayat Quiz
        </a>
    </div>

    {{-- Recent Sessions --}}
    @if($recentSessions->count() > 0)
    <div class="mt-14">
        <h2 class="text-lg font-black text-slate-800 dark:text-white uppercase tracking-wider mb-6 flex items-center gap-3">
            <i class="fas fa-clock text-slate-400"></i> Quiz Terakhir
            <span class="ml-auto h-1 flex-1 bg-slate-100 dark:bg-gray-800 rounded-full"></span>
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @foreach($recentSessions as $session)
            <div class="bg-white dark:bg-gray-800 border-2 border-b-[6px] border-slate-200 dark:border-gray-700 rounded-2xl p-5 hover:border-[#1cb0f6]/30 transition-colors cursor-default">
                <div class="flex justify-between items-start mb-3">
                    <span class="text-xs font-bold text-slate-400 dark:text-slate-500"><i class="far fa-calendar-alt mr-1"></i> {{ $session->created_at->format('d M') }}</span>
                    <span class="px-2 py-1 rounded-lg text-[10px] font-black border-2 border-b-4
                        {{ $session->score >= 70 ? 'bg-emerald-50 border-emerald-200 text-emerald-500 dark:bg-emerald-900/30 dark:border-emerald-800 dark:text-emerald-400' : 'bg-rose-50 border-rose-200 text-rose-500 dark:bg-rose-900/30 dark:border-rose-800 dark:text-rose-400' }}">
                        {{ $session->grade }}
                    </span>
                </div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-xs font-black text-slate-600 dark:text-slate-300 uppercase tracking-wide">
                        @switch($session->quiz_type)
                            @case('multiple_choice') <i class="fas fa-list-ul mr-1"></i> Ganda @break
                            @case('drawing') <i class="fas fa-pencil-alt mr-1"></i> Gambar @break
                            @case('listening') <i class="fas fa-headphones mr-1"></i> Dengar @break
                            @case('matching') <i class="fas fa-puzzle-piece mr-1"></i> Cocok @break
                            @case('mixed') <i class="fas fa-random mr-1"></i> Campur @break
                            @default {{ ucfirst($session->quiz_type) }}
                        @endswitch
                    </span>
                </div>
                <div class="flex items-center gap-3 mt-4 pt-4 border-t-2 border-dashed border-slate-100 dark:border-gray-700">
                    <span class="text-2xl font-black {{ $session->score >= 70 ? 'text-emerald-500' : 'text-rose-500' }}">
                        {{ number_format($session->score, 0) }}%
                    </span>
                    <span class="text-xs font-bold text-slate-400"><i class="fas fa-check-circle mr-1"></i> {{ $session->correct_answers }}/{{ $session->total_questions }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>

<script>
    let selectedCategory = null;
    let selectedQuizType = null;
    let selectedQuestionCount = 10;

    // Gamified active vs inactive classes
    const pillActive = 'category-pill px-6 py-3 rounded-2xl text-sm font-black uppercase tracking-widest border-2 transition-all duration-150 border-b-[4px] border-[#1cb0f6] bg-[#1cb0f6]/10 text-[#1cb0f6] translate-y-0.5';
    const pillInactive = 'category-pill px-6 py-3 rounded-2xl text-sm font-black uppercase tracking-widest border-2 transition-all duration-150 border-b-[4px] border-slate-200 dark:border-gray-600 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-gray-700 active:translate-y-1 active:border-b-2';

    const cardActive = 'quiz-type-card p-5 rounded-2xl border-2 transition-all duration-150 group border-b-[4px] border-[#1cb0f6] bg-[#1cb0f6]/5 ring-2 ring-[#1cb0f6]/20 translate-y-0.5';
    const cardInactive = 'quiz-type-card p-5 rounded-2xl border-2 border-b-[6px] border-slate-200 dark:border-gray-600 text-left transition-all duration-150 hover:bg-slate-50 dark:hover:bg-gray-700 active:translate-y-1 active:border-b-2 group';

    const countActive = 'count-pill flex-1 min-w-[60px] py-4 rounded-2xl text-base font-black border-2 transition-all duration-150 border-b-[4px] border-[#1cb0f6] bg-[#1cb0f6]/10 text-[#1cb0f6] translate-y-0.5';
    const countInactive = 'count-pill flex-1 min-w-[60px] py-4 rounded-2xl text-base font-black border-2 transition-all duration-150 border-b-[4px] border-slate-200 dark:border-gray-600 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-gray-700 active:translate-y-1 active:border-b-2';

    function selectCategory(cat) {
        selectedCategory = cat;
        document.querySelectorAll('.category-pill').forEach(btn => {
            const btnCat = btn.getAttribute('data-category');
            const isSelected = (btnCat === '' && cat === null) || (btnCat === cat);
            btn.className = isSelected ? pillActive : pillInactive;
        });
    }

    function selectQuizType(type) {
        selectedQuizType = type;
        document.querySelectorAll('.quiz-type-card').forEach(btn => {
            const btnType = btn.getAttribute('data-type');
            btn.className = (btnType === type) ? cardActive : cardInactive;
        });
    }

    function selectQuestionCount(count) {
        selectedQuestionCount = count;
        document.querySelectorAll('.count-pill').forEach(btn => {
            const btnCount = btn.getAttribute('data-count');
            btn.className = (parseInt(btnCount) === count) ? countActive : countInactive;
        });
    }

    function csrfToken() {
        return document.querySelector('meta[name="csrf-token"]').content;
    }

    async function startQuiz() {
        if (!selectedQuizType) {
            showError('Pilih jenis quiz terlebih dahulu!');
            return;
        }

        const btn = document.getElementById('startQuizBtn');
        const btnText = document.getElementById('startBtnText');
        const spinner = document.getElementById('startBtnSpinner');

        btn.disabled = true;
        btn.classList.add('opacity-80', 'cursor-not-allowed', 'translate-y-2', 'border-b-2');
        btnText.textContent = 'Memulai...';
        spinner.classList.remove('hidden');
        hideError();

        try {
            const res = await fetch('/quiz/start', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken(),
                },
                body: JSON.stringify({
                    category: selectedCategory,
                    quiz_type: selectedQuizType,
                    question_count: selectedQuestionCount,
                }),
            });

            const data = await res.json();

            if (!res.ok) {
                showError(data.error || data.message || 'Terjadi kesalahan saat memulai quiz.');
                resetBtn(btn, btnText, spinner);
                return;
            }

            // Store in sessionStorage and redirect
            sessionStorage.setItem('quizSession', JSON.stringify({
                session_id: data.session_id,
                questions: data.questions,
            }));

            window.location.href = '/quiz/play';

        } catch (err) {
            showError('Gagal terhubung ke server. Periksa koneksi Anda.');
            resetBtn(btn, btnText, spinner);
        }
    }

    function resetBtn(btn, btnText, spinner) {
        btn.disabled = false;
        btn.classList.remove('opacity-80', 'cursor-not-allowed', 'translate-y-2', 'border-b-2');
        btnText.textContent = 'Mulai Quiz';
        spinner.classList.add('hidden');
    }

    function showError(msg) {
        const el = document.getElementById('quizError');
        el.innerHTML = `<i class="fas fa-exclamation-triangle mr-2"></i> ${msg}`;
        el.classList.remove('hidden');
    }

    function hideError() {
        document.getElementById('quizError').classList.add('hidden');
    }
</script>
@endsection