@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 sm:p-6 transition-transform transform hover:-translate-y-1 hover:shadow-md group col-span-1 md:col-span-2 lg:col-span-5">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-4 w-full">
            
            <div class="flex items-start sm:items-center gap-3 sm:gap-4 flex-1 w-full">
                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-yellow-50 dark:bg-yellow-900/30 flex items-center justify-center flex-shrink-0 group-hover:bg-yellow-100 transition-colors duration-300 mt-1 sm:mt-0">
                    <i class="fas fa-robot text-xl sm:text-2xl text-yellow-600 dark:text-yellow-400"></i>
                </div>
                
                <div class="flex-1 min-w-0">
                    <h3 class="text-gray-500 dark:text-gray-400 text-xs sm:text-sm font-semibold uppercase tracking-wider mb-1.5 sm:mb-1 truncate">Status AI Validation Server</h3>
                    <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                        <span id="ai-status-badge" class="inline-flex w-fit items-center gap-1.5 sm:gap-2 px-2.5 sm:px-3 py-1 rounded-full text-xs sm:text-sm font-bold bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                            <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-gray-400 animate-pulse"></span>
                            <span>Memeriksa...</span>
                        </span>
                        <span id="ai-status-detail" class="text-[10px] sm:text-xs text-gray-400 dark:text-gray-500 truncate w-full sm:w-auto mt-1 sm:mt-0"></span>
                    </div>
                </div>
            </div>

            <button onclick="checkAIStatus()" class="w-full sm:w-auto mt-2 sm:mt-0 py-2 sm:py-0 flex justify-center sm:justify-end items-center gap-1.5 text-xs text-indigo-500 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium transition-colors bg-gray-50 sm:bg-transparent dark:bg-gray-700/50 sm:dark:bg-transparent rounded-lg sm:rounded-none">
                <i class="fas fa-sync-alt" id="refresh-icon"></i> Refresh
            </button>
            
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex flex-col items-center justify-center transition-transform transform hover:-translate-y-1 hover:shadow-md cursor-pointer group">
        <div class="w-14 h-14 rounded-full bg-red-50 dark:bg-red-900/30 flex items-center justify-center mb-4 group-hover:bg-red-100 dark:group-hover:bg-red-900/50 transition-colors duration-300">
            <span class="text-2xl font-bold text-red-600 dark:text-red-400">漢</span>
        </div>
        <h3 class="text-gray-500 dark:text-gray-400 text-sm font-semibold uppercase tracking-wider mb-1">Total Kanji</h3>
        <p class="text-4xl font-bold text-gray-800 dark:text-gray-100">{{ $totalKanji ?? 0 }}</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex flex-col items-center justify-center transition-transform transform hover:-translate-y-1 hover:shadow-md cursor-pointer group">
        <div class="w-14 h-14 rounded-full bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center mb-4 group-hover:bg-blue-100 dark:group-hover:bg-blue-900/50 transition-colors duration-300">
            <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">あ</span>
        </div>
        <h3 class="text-gray-500 dark:text-gray-400 text-sm font-semibold uppercase tracking-wider mb-1">Total Hiragana</h3>
        <p class="text-4xl font-bold text-gray-800 dark:text-gray-100">{{ $totalHiragana ?? 0 }}</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex flex-col items-center justify-center transition-transform transform hover:-translate-y-1 hover:shadow-md cursor-pointer group">
        <div class="w-14 h-14 rounded-full bg-green-50 dark:bg-green-900/30 flex items-center justify-center mb-4 group-hover:bg-green-100 dark:group-hover:bg-green-900/50 transition-colors duration-300">
            <span class="text-2xl font-bold text-green-600 dark:text-green-400">ア</span>
        </div>
        <h3 class="text-gray-500 dark:text-gray-400 text-sm font-semibold uppercase tracking-wider mb-1">Total Katakana</h3>
        <p class="text-4xl font-bold text-gray-800 dark:text-gray-100">{{ $totalKatakana ?? 0 }}</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex flex-col items-center justify-center transition-transform transform hover:-translate-y-1 hover:shadow-md cursor-pointer group">
        <div class="w-14 h-14 rounded-full bg-purple-50 dark:bg-purple-900/30 flex items-center justify-center mb-4 group-hover:bg-purple-100 dark:group-hover:bg-purple-900/50 transition-colors duration-300">
            <i class="fas fa-book-open text-2xl text-purple-600 dark:text-purple-400"></i>
        </div>
        <h3 class="text-gray-500 dark:text-gray-400 text-sm font-semibold uppercase tracking-wider mb-1">Total Materi</h3>
        <p class="text-4xl font-bold text-gray-800 dark:text-gray-100">{{ $totalMateri ?? 0 }}</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex flex-col justify-center transition-transform transform hover:-translate-y-1 hover:shadow-md cursor-pointer group">
        <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center group-hover:bg-indigo-100 dark:group-hover:bg-indigo-900/50 transition-colors duration-300">
                    <i class="fas fa-microphone-alt text-lg text-indigo-600 dark:text-indigo-400"></i>
                </div>
                <h3 class="text-gray-500 dark:text-gray-400 text-sm font-semibold uppercase tracking-wider">Kuota Audio</h3>
            </div>
            </div>
        
        <div class="mt-2 mb-4">
            <p id="elevenlabs-quota-text" class="text-3xl font-bold text-gray-800 dark:text-gray-100">
                <span class="w-6 h-6 rounded-full bg-gray-200 dark:bg-gray-600 animate-pulse inline-block mb-1"></span>
            </p>
        </div>

        <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2 mb-2 overflow-hidden">
            <div id="elevenlabs-progress-fill" class="bg-indigo-500 h-full rounded-full transition-all duration-1000 ease-out" style="width: 0%"></div>
        </div>
        <div class="flex justify-between items-center text-xs text-gray-400 dark:text-gray-500">
            <span id="elevenlabs-used-text">... terpakai</span>
            <span id="elevenlabs-limit-text">... total</span>
        </div>
    </div>
</div>

<!-- <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 md:p-8">
    <div class="flex items-center justify-between mb-6 border-b border-gray-100 dark:border-gray-700 pb-4">
        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">Selamat datang di Admin Panel</h3>
        <span class="text-sm text-gray-500 dark:text-gray-400"><i class="far fa-clock mr-1"></i> Hari ini: {{ now()->translatedFormat('l, d F Y') }}</span>
    </div>
    <div class="text-gray-600 dark:text-gray-300 space-y-4">
        <p class="leading-relaxed">Gunakan menu di sebelah kiri untuk mengelola berbagai data aplikasi Anda.</p>
        <div class="bg-indigo-50 dark:bg-indigo-950/50 rounded-lg p-5 border border-indigo-100 dark:border-indigo-800">
            <h4 class="font-bold text-indigo-800 dark:text-indigo-300 mb-2 flex items-center"><i class="fas fa-info-circle mr-2"></i> Akses Cepat</h4>
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('admin.kanjis.create') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium transition-colors inline-flex items-center group">
                        <i class="fas fa-plus-circle mr-2 opacity-75 group-hover:opacity-100"></i> Tambah Kanji Baru
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.kanjis.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium transition-colors inline-flex items-center group">
                        <i class="fas fa-list-ul mr-2 opacity-75 group-hover:opacity-100"></i> Lihat Daftar Kanji
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.materis.create') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium transition-colors inline-flex items-center group">
                        <i class="fas fa-plus-circle mr-2 opacity-75 group-hover:opacity-100"></i> Tambah Materi Baru
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.materis.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium transition-colors inline-flex items-center group">
                        <i class="fas fa-list-ul mr-2 opacity-75 group-hover:opacity-100"></i> Lihat Daftar Materi
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div> -->

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
    <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 md:p-8 hover:shadow-md transition-shadow">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
            <div>
                <h3 class="text-lg font-black text-gray-800 dark:text-gray-100 flex items-center gap-2 uppercase tracking-wider">
                    <i class="fas fa-chart-area text-indigo-500"></i> Statistik Aktivitas Kuis
                </h3>
                <p id="chart-subtitle" class="text-xs font-bold text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-widest">Tren pengerjaan kuis dalam 7 hari terakhir</p>
            </div>
            <div class="flex items-center bg-gray-100 dark:bg-gray-700 p-1 rounded-lg self-start sm:self-auto">
                <button onclick="updateChartRange(7)" id="btn-range-7" class="px-3 py-1.5 text-[10px] font-black uppercase tracking-widest rounded-md transition-all text-indigo-600 dark:text-indigo-400 bg-white dark:bg-gray-800 shadow-sm">7 Hari</button>
                <button onclick="updateChartRange(15)" id="btn-range-15" class="px-3 py-1.5 text-[10px] font-black uppercase tracking-widest rounded-md transition-all text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">15 Hari</button>
                <button onclick="updateChartRange(30)" id="btn-range-30" class="px-3 py-1.5 text-[10px] font-black uppercase tracking-widest rounded-md transition-all text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">30 Hari</button>
            </div>
        </div>
        <div class="w-full h-[350px] relative">
            <canvas id="quizActivityChart"></canvas>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 md:p-8 hover:shadow-md transition-shadow flex flex-col">
        <div class="mb-8">
            <h3 class="text-lg font-black text-gray-800 dark:text-gray-100 flex items-center gap-2 uppercase tracking-wider">
                <i class="fas fa-chart-pie text-emerald-500"></i> Distribusi Nilai
            </h3>
            <p class="text-xs font-bold text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-widest">Predikat Kuis Pengguna</p>
        </div>
        <div class="w-full flex-1 relative flex justify-center items-center min-h-[300px]">
            <canvas id="gradePieChart"></canvas>
        </div>
    </div>
</div>


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('quizActivityChart').getContext('2d');
        
        window.chartAllLabels = {!! json_encode($labels) !!};
        window.chartAllAttemptData = {!! json_encode($attemptData) !!};
        window.chartAllSessionData = {!! json_encode($sessionData) !!};
        
        const isDarkMode = document.documentElement.classList.contains('dark');
        const textColor = isDarkMode ? '#9ca3af' : '#6b7280';
        const gridColor = isDarkMode ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)';

        // Create gradients
        const gradientIndigo = ctx.createLinearGradient(0, 0, 0, 350);
        gradientIndigo.addColorStop(0, 'rgba(99, 102, 241, 0.4)'); // Indigo 500
        gradientIndigo.addColorStop(1, 'rgba(99, 102, 241, 0.0)');

        const gradientEmerald = ctx.createLinearGradient(0, 0, 0, 350);
        gradientEmerald.addColorStop(0, 'rgba(16, 185, 129, 0.4)'); // Emerald 500
        gradientEmerald.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

        window.quizChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: window.chartAllLabels.slice(-7),
                datasets: [
                    {
                        label: 'Kuis Builder (Manual)',
                        data: window.chartAllAttemptData.slice(-7),
                        borderColor: '#6366f1', // Indigo 500
                        backgroundColor: gradientIndigo,
                        borderWidth: 3,
                        pointBackgroundColor: isDarkMode ? '#1f2937' : '#ffffff',
                        pointBorderColor: '#6366f1',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: '#6366f1',
                        pointHoverBorderColor: '#ffffff',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Kuis AI (Auto)',
                        data: window.chartAllSessionData.slice(-7),
                        borderColor: '#10b981', // Emerald 500
                        backgroundColor: gradientEmerald,
                        borderWidth: 3,
                        pointBackgroundColor: isDarkMode ? '#1f2937' : '#ffffff',
                        pointBorderColor: '#10b981',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: '#10b981',
                        pointHoverBorderColor: '#ffffff',
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end',
                        labels: { 
                            color: textColor, 
                            font: { family: "'Segoe UI', Arial, sans-serif", weight: 'bold', size: 12 },
                            usePointStyle: true,
                            boxWidth: 8,
                            padding: 20
                        }
                    },
                    tooltip: {
                        backgroundColor: isDarkMode ? 'rgba(17, 24, 39, 0.95)' : 'rgba(255, 255, 255, 0.95)',
                        titleColor: isDarkMode ? '#f3f4f6' : '#111827',
                        bodyColor: isDarkMode ? '#d1d5db' : '#4b5563',
                        borderColor: isDarkMode ? '#374151' : '#e5e7eb',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 12,
                        titleFont: { family: "'Segoe UI', Arial, sans-serif", size: 13, weight: 'bold' },
                        bodyFont: { family: "'Segoe UI', Arial, sans-serif", size: 13, weight: '500' },
                        boxPadding: 6,
                        usePointStyle: true,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y + ' Sesi';
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false, drawBorder: false },
                        ticks: { color: textColor, font: { weight: '600', size: 11 }, padding: 10 }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: gridColor, borderDash: [4, 4], drawBorder: false },
                        ticks: { stepSize: 1, color: textColor, font: { weight: '600', size: 11 }, padding: 15 },
                        border: { display: false }
                    }
                }
            }
        });
        // Create Pie Chart
        const pieCtx = document.getElementById('gradePieChart').getContext('2d');
        const pieLabels = {!! json_encode($gradeLabels) !!};
        const pieData = {!! json_encode($gradeData) !!};

        new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: pieLabels,
                datasets: [{
                    data: pieData,
                    backgroundColor: [
                        '#10b981', // S - Emerald
                        '#3b82f6', // A - Blue
                        '#8b5cf6', // B - Violet
                        '#f59e0b', // C - Amber
                        '#f97316', // D - Orange
                        '#ef4444'  // F - Red
                    ],
                    borderWidth: isDarkMode ? 2 : 0,
                    borderColor: isDarkMode ? '#1f2937' : '#ffffff',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: textColor,
                            font: { family: "'Segoe UI', Arial, sans-serif", weight: 'bold', size: 12 },
                            usePointStyle: true,
                            padding: 20
                        }
                    },
                    tooltip: {
                        backgroundColor: isDarkMode ? 'rgba(17, 24, 39, 0.95)' : 'rgba(255, 255, 255, 0.95)',
                        titleColor: isDarkMode ? '#f3f4f6' : '#111827',
                        bodyColor: isDarkMode ? '#d1d5db' : '#4b5563',
                        borderColor: isDarkMode ? '#374151' : '#e5e7eb',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 12,
                        titleFont: { family: "'Segoe UI', Arial, sans-serif", size: 13, weight: 'bold' },
                        bodyFont: { family: "'Segoe UI', Arial, sans-serif", size: 13, weight: '500' },
                        boxPadding: 6,
                        usePointStyle: true,
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed !== null) {
                                    label += context.parsed + ' Sesi';
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    });

    window.updateChartRange = function(days) {
        if (!window.quizChart) return;
        
        window.quizChart.data.labels = window.chartAllLabels.slice(-days);
        window.quizChart.data.datasets[0].data = window.chartAllAttemptData.slice(-days);
        window.quizChart.data.datasets[1].data = window.chartAllSessionData.slice(-days);
        window.quizChart.update();

        const ranges = [7, 15, 30];
        ranges.forEach(r => {
            const btn = document.getElementById('btn-range-' + r);
            if (r === days) {
                btn.className = "px-3 py-1.5 text-[10px] font-black uppercase tracking-widest rounded-md transition-all text-indigo-600 dark:text-indigo-400 bg-white dark:bg-gray-800 shadow-sm";
            } else {
                btn.className = "px-3 py-1.5 text-[10px] font-black uppercase tracking-widest rounded-md transition-all text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200";
            }
        });

        document.getElementById('chart-subtitle').innerText = 'Tren pengerjaan kuis dalam ' + days + ' hari terakhir';
    }

    const AI_URL = 'http://127.0.0.1:5000/status'; // sesuaikan URL Flask kamu

    async function checkAIStatus() {
        const badge = document.getElementById('ai-status-badge');
        const detail = document.getElementById('ai-status-detail');
        const icon = document.getElementById('refresh-icon');

        // Loading state
        icon.classList.add('fa-spin');
        badge.innerHTML = `<span class="w-2 h-2 rounded-full bg-gray-400 animate-pulse"></span> Memeriksa...`;
        badge.className = 'inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-bold bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400';

        try {
            const controller = new AbortController();
            const timeout = setTimeout(() => controller.abort(), 5000); // timeout 5 detik

            const res = await fetch(AI_URL, { signal: controller.signal });
            clearTimeout(timeout);

            if (res.ok) {
                const data = await res.json();
                badge.innerHTML = `<span class="w-2 h-2 rounded-full bg-green-400"></span> Online`;
                badge.className = 'inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-bold bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-400';
                detail.textContent = `${data.total_classes ?? '-'} karakter didukung`;
            } else {
                throw new Error('Response tidak OK');
            }
        } catch (err) {
            badge.innerHTML = `<span class="w-2 h-2 rounded-full bg-red-400"></span> Offline`;
            badge.className = 'inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-bold bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-400';
            detail.textContent = err.name === 'AbortError' ? 'Timeout (>5 detik)' : 'Tidak dapat terhubung ke server AI';
        } finally {
            icon.classList.remove('fa-spin');
        }
    }

    async function checkElevenLabsQuota() {
        const quotaText = document.getElementById('elevenlabs-quota-text');
        const progressFill = document.getElementById('elevenlabs-progress-fill');
        const usedText = document.getElementById('elevenlabs-used-text');
        const limitText = document.getElementById('elevenlabs-limit-text');
        
        try {
            const res = await fetch('{{ route("admin.elevenlabs.quota") }}', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (res.status === 401 || res.status === 419) {
                window.location.href = '/login';
                return;
            }
            
            if (res.ok) {
                const data = await res.json();
                
                const remaining = data.remaining || 0;
                const limit = data.limit || 0;
                const used = data.used || 0;

                const formatter = new Intl.NumberFormat('id-ID');
                
                // Hitung persentase pemakaian
                let percentage = 0;
                if (limit > 0) {
                    percentage = (used / limit) * 100;
                }

                // Update Text Angka
                quotaText.innerHTML = `${formatter.format(remaining)} <span class="text-xs text-gray-400 dark:text-gray-500 font-normal">Tersisa</span>`;
                usedText.innerText = `${formatter.format(used)} terpakai`;
                limitText.innerText = `${formatter.format(limit)} total`;

                // Animate Progress Bar
                progressFill.style.width = `${percentage}%`;

                // Logika Warna Bar (Optional tapi bikin keren)
                // Reset class warna dulu
                progressFill.classList.remove('bg-indigo-500', 'bg-yellow-500', 'bg-red-500');
                
                if (percentage >= 90) {
                    progressFill.classList.add('bg-red-500'); // Kritis (> 90%)
                } else if (percentage >= 75) {
                    progressFill.classList.add('bg-yellow-500'); // Warning (> 75%)
                } else {
                    progressFill.classList.add('bg-indigo-500'); // Aman
                }

            } else {
                throw new Error('Gagal memuat API');
            }
        } catch (err) {
            quotaText.innerHTML = `<span class="text-sm font-normal text-red-500 bg-red-50 dark:bg-red-900/30 px-2 py-1 rounded border border-red-200 dark:border-red-800">Gagal Dimuat</span>`;
            console.error('ElevenLabs Quota Error:', err);
        }
    }

    // Cek otomatis saat halaman dimuat
    checkAIStatus();
    checkElevenLabsQuota();

    // Auto-refresh setiap 60 detik
    setInterval(() => {
        checkAIStatus();
        checkElevenLabsQuota();
    }, 60000);
</script>
@endpush
@endsection