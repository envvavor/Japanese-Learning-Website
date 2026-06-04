@extends('layouts.app')

@section('title', 'Latihan: ' . $folder->name . ' — Manabu')

@push('styles')
<style>
    .card-flip { perspective: 1000px; }
    .card-flip-inner {
        position: relative;
        width: 100%;
        height: 100%;
        transition: transform 0.5s;
        transform-style: preserve-3d;
    }
    .card-flip-inner.flipped { transform: rotateY(180deg); }
    .card-front, .card-back {
        position: absolute;
        inset: 0;
        backface-visibility: hidden;
        -webkit-backface-visibility: hidden;
    }
    .card-back { transform: rotateY(180deg); }
    .slide-in { animation: slideIn 0.3s ease-out; }
    @keyframes slideIn {
        from { opacity: 0; transform: translateX(60px); }
        to { opacity: 1; transform: translateX(0); }
    }
    .bounce-in { animation: bounceIn 0.4s ease-out; }
    @keyframes bounceIn {
        0% { opacity: 0; transform: scale(0.8); }
        60% { transform: scale(1.05); }
        100% { opacity: 1; transform: scale(1); }
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-slate-900 font-sans"
     x-data="flashcardApp()"
     x-init="init()">

    <template x-if="!finished">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 py-6 sm:py-10">

        <div class="flex items-center justify-between mb-6 sm:mb-8">
            <a href="{{ route('vocabulary-folders.show', $folder) }}"
               class="w-10 h-10 sm:w-12 sm:h-12 flex items-center justify-center bg-white dark:bg-gray-800 border-2 border-b-[4px] border-slate-200 dark:border-gray-700 rounded-xl text-slate-500 dark:text-slate-400 hover:text-indigo-600 active:border-b-2 active:translate-y-1 transition-all shrink-0 shadow-sm">
                <i class="fas fa-times text-lg"></i>
            </a>
            <div class="flex-1 mx-4">
                <div class="h-3 bg-slate-200 dark:bg-gray-700 rounded-full overflow-hidden border border-slate-300 dark:border-gray-600">
                    <div class="h-full bg-[#1cb0f6] rounded-full transition-all duration-500"
                         :style="`width: ${((currentIndex) / cards.length) * 100}%`"></div>
                </div>
            </div>
            <span class="text-xs sm:text-sm font-black text-slate-500 uppercase tracking-widest shrink-0"
                  x-text="`${currentIndex + 1}/${cards.length}`"></span>
        </div>

        <div class="card-flip mx-auto" style="height: 360px; max-width: 480px;">
            <div class="card-flip-inner rounded-[2rem]" :class="{ 'flipped': isFlipped }" @click="flipCard()">

                <div class="card-front bg-white dark:bg-gray-800 border-2 border-b-[8px] border-slate-200 dark:border-gray-700 rounded-[2rem] p-6 sm:p-8 flex flex-col items-center justify-center cursor-pointer shadow-lg hover:shadow-xl transition-shadow">
                    <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Klik untuk membalik</div>
                    <p class="text-5xl sm:text-6xl font-black text-slate-800 dark:text-white mb-3 text-center leading-tight"
                       x-text="currentCard.original"></p>
                    <template x-if="currentCard.jlpt_level">
                        <span class="px-3 py-1 rounded-xl border-2 text-[10px] font-black uppercase tracking-widest bg-sky-100 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 border-sky-200 dark:border-sky-800 mt-2"
                              x-text="currentCard.jlpt_level"></span>
                    </template>
                    <div class="mt-6 flex items-center gap-2 text-slate-300 dark:text-gray-600">
                        <i class="fas fa-hand-pointer text-lg"></i>
                        <span class="text-xs font-bold">Tap / Klik</span>
                    </div>
                </div>

                <div class="card-back bg-white dark:bg-gray-800 border-2 border-b-[8px] border-indigo-200 dark:border-indigo-800 rounded-[2rem] p-6 sm:p-8 flex flex-col items-center justify-center cursor-pointer shadow-lg">
                    <p class="text-3xl sm:text-4xl font-black text-slate-800 dark:text-white mb-2 text-center"
                       x-text="currentCard.original"></p>
                    <template x-if="currentCard.furigana && currentCard.furigana !== currentCard.original">
                        <p class="text-lg sm:text-xl font-bold text-indigo-500 mb-4 bg-indigo-50 dark:bg-indigo-900/30 px-4 py-1 rounded-xl border-2 border-dashed border-indigo-200 dark:border-indigo-800 text-center"
                           x-text="currentCard.furigana"></p>
                    </template>
                    <div class="w-full h-px bg-slate-200 dark:bg-gray-700 my-3"></div>
                    <p class="text-xl sm:text-2xl font-black text-slate-700 dark:text-slate-200 text-center mb-1"
                       x-text="currentCard.indonesian || 'Menerjemahkan...'"></p>
                    <p class="text-sm font-bold text-slate-400 italic text-center"
                       x-text="currentCard.english"></p>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-center gap-4 sm:gap-6 mt-8 sm:mt-10" x-show="isFlipped" x-transition>
            <button @click="answer(false)"
                    class="flex-1 max-w-[200px] py-4 sm:py-5 bg-rose-500 border-2 border-b-[6px] border-rose-700 text-white font-black text-sm sm:text-base uppercase tracking-widest rounded-2xl hover:brightness-110 active:border-b-2 active:translate-y-1 transition-all shadow-sm flex items-center justify-center gap-2">
                <i class="fas fa-times text-lg"></i> Salah
            </button>
            <button @click="answer(true)"
                    class="flex-1 max-w-[200px] py-4 sm:py-5 bg-emerald-500 border-2 border-b-[6px] border-emerald-700 text-white font-black text-sm sm:text-base uppercase tracking-widest rounded-2xl hover:brightness-110 active:border-b-2 active:translate-y-1 transition-all shadow-sm flex items-center justify-center gap-2">
                <i class="fas fa-check text-lg"></i> Benar
            </button>
        </div>

        <div class="flex items-center justify-center mt-4" x-show="!isFlipped">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">
                <i class="fas fa-lightbulb text-amber-400 mr-1"></i> Coba ingat artinya sebelum membalik!
            </p>
        </div>

    </div>
    </template>

    <template x-if="finished">
    <div class="max-w-lg mx-auto px-4 sm:px-6 py-10 sm:py-20">
        <div class="bg-white dark:bg-gray-800 border-2 border-b-[8px] border-slate-200 dark:border-gray-700 rounded-[2rem] p-8 sm:p-10 text-center shadow-lg bounce-in">

            <div class="w-24 h-24 sm:w-28 sm:h-28 mx-auto mb-6 bg-amber-100 dark:bg-amber-900/30 border-4 border-amber-400 dark:border-amber-600 rounded-full flex items-center justify-center">
                <i class="fas fa-trophy text-5xl sm:text-6xl text-amber-400"></i>
            </div>

            <h2 class="text-2xl sm:text-3xl font-black text-slate-800 dark:text-white uppercase tracking-wider mb-2">
                Latihan Selesai!
            </h2>
            <p class="text-sm font-bold text-slate-500 mb-6">おつかれさま!</p>

            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="bg-emerald-50 dark:bg-emerald-900/20 border-2 border-emerald-200 dark:border-emerald-800 rounded-2xl p-4">
                    <p class="text-3xl font-black text-emerald-500" x-text="correctCount"></p>
                    <p class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">Benar</p>
                </div>
                <div class="bg-rose-50 dark:bg-rose-900/20 border-2 border-rose-200 dark:border-rose-800 rounded-2xl p-4">
                    <p class="text-3xl font-black text-rose-500" x-text="wrongCount"></p>
                    <p class="text-[10px] font-black text-rose-600 dark:text-rose-400 uppercase tracking-widest">Salah</p>
                </div>
            </div>

            <div class="bg-amber-50 dark:bg-amber-900/20 border-2 border-amber-200 dark:border-amber-800 rounded-2xl p-4 mb-6">
                <p class="text-lg font-black text-amber-500">
                    +<span x-text="xpEarned"></span> XP
                </p>
                <p class="text-[10px] font-black text-amber-600 dark:text-amber-400 uppercase tracking-widest">Diperoleh</p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('vocabulary-folders.practice', $folder) }}"
                   class="flex-1 py-4 bg-[#1cb0f6] border-2 border-b-[6px] border-[#1899d6] text-white font-black text-sm uppercase tracking-widest rounded-2xl hover:brightness-110 active:border-b-2 active:translate-y-1 transition-all shadow-sm text-center">
                    <i class="fas fa-redo mr-2"></i> Ulangi
                </a>
                <a href="{{ route('vocabulary-folders.show', $folder) }}"
                   class="flex-1 py-4 bg-white dark:bg-gray-700 border-2 border-b-[6px] border-slate-200 dark:border-gray-600 text-slate-600 dark:text-slate-300 font-black text-sm uppercase tracking-widest rounded-2xl hover:bg-slate-50 dark:hover:bg-gray-600 active:border-b-2 active:translate-y-1 transition-all shadow-sm text-center">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
            </div>
        </div>
    </div>
    </template>

</div>

@push('scripts')
<script>
function flashcardApp() {
    return {
        cards: @json($vocabularies),
        progressMap: @json($progressMap),
        currentIndex: 0,
        isFlipped: false,
        finished: false,
        correctCount: 0,
        wrongCount: 0,
        xpEarned: 0,

        get currentCard() {
            return this.cards[this.currentIndex] || {};
        },

        init() {
            this.cards = this.shuffleArray([...this.cards]);
        },

        shuffleArray(arr) {
            for (let i = arr.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [arr[i], arr[j]] = [arr[j], arr[i]];
            }
            return arr;
        },

        flipCard() {
            this.isFlipped = !this.isFlipped;
        },

        async answer(isCorrect) {
            if (isCorrect) this.correctCount++;
            else this.wrongCount++;

            try {
                await fetch('{{ route('vocabulary-folders.progress', $folder) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        vocabulary_id: this.currentCard.id,
                        is_correct: isCorrect,
                    })
                });
            } catch(e) {}

            this.isFlipped = false;

            await new Promise(r => setTimeout(r, 300));

            if (this.currentIndex < this.cards.length - 1) {
                this.currentIndex++;
            } else {
                await this.finishSession();
            }
        },

        async finishSession() {
            try {
                const res = await fetch('{{ route('vocabulary-folders.finish', $folder) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        correct: this.correctCount,
                        total: this.cards.length,
                    })
                });
                const data = await res.json();
                this.xpEarned = data.xp_earned || 0;
            } catch(e) {
                this.xpEarned = this.correctCount * 2;
            }
            this.finished = true;
        }
    }
}
</script>
@endpush
@endsection
