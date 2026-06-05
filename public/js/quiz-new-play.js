let currentIdx = 0, answers = {}, streak = 0;
let timerInterval = null, elapsedSeconds = 0;
let drawingCanvas = null, drawingCtx = null, isDrawing = false;
let currentStroke = [], allStrokes = [], templateStrokes = [];

function csrf() { return CSRF_TOKEN; }

// ── Timer ─────────────────────────────────────────────────────
function startTimer() {
    timerInterval = setInterval(() => {
        elapsedSeconds++;
        const m = String(Math.floor(elapsedSeconds / 60)).padStart(2, '0');
        const s = String(elapsedSeconds % 60).padStart(2, '0');
        document.getElementById('timerDisplay').textContent = m + ':' + s;
    }, 1000);
}

// ── Progress ──────────────────────────────────────────────────
function updateProgress() {
    const pct = ((currentIdx + 1) / QUESTIONS.length) * 100;
    document.getElementById('progressBar').style.width = pct + '%';
    document.getElementById('progressText').textContent = (currentIdx + 1) + ' / ' + QUESTIONS.length + ' Soal';
}

function buildDots() {
    const c = document.getElementById('dotIndicators'); c.innerHTML = '';
    QUESTIONS.forEach((_, i) => {
        const d = document.createElement('div');
        d.className = 'dot-indicator ' + (i === currentIdx ? 'dot-current' : (answers[i] ? (answers[i].is_correct ? 'dot-correct' : 'dot-wrong') : 'dot-unanswered'));
        c.appendChild(d);
    });
}

// ── Render Question ───────────────────────────────────────────
function renderQuestion() {
    const q = QUESTIONS[currentIdx];
    document.getElementById('questionNumber').textContent = 'Soal ' + (currentIdx + 1) + ' dari ' + QUESTIONS.length;

    const tb = document.getElementById('typeBadge');
    if (q.question_type === 'multiple_choice') { 
        tb.innerHTML = '<i class="fas fa-list-ul mr-1"></i> Pilihan Ganda'; 
    }
    else if (q.question_type === 'drawing') { 
        tb.innerHTML = '<i class="fas fa-pencil-alt mr-1"></i> Menggambar'; 
    }
    else { 
        tb.innerHTML = '<i class="fas fa-headphones mr-1"></i> Mendengarkan'; 
    }

    const qd = document.getElementById('questionDisplay');
    const qt = document.getElementById('questionText');
    qt.textContent = q.question_text;

    if (q.question_type === 'drawing') {
        qd.innerHTML = '';
    } else if (q.question_type === 'listening') {
        qd.innerHTML = '<div class="text-6xl text-[#1cb0f6] mb-2"><i class="fas fa-volume-up"></i></div>';
    } else {
        if (q.character) {
            qd.innerHTML = '<div class="text-6xl font-bold text-slate-800 dark:text-white">' + q.character + '</div>';
        } else { qd.innerHTML = ''; }
    }

    const cc = document.getElementById('questionContent');
    const hintArea = document.getElementById('hintArea');

    // Jika sudah dijawab, langsung tampilkan hasil & sembunyikan hint
    if (answers[currentIdx]) { 
        hintArea.style.display = 'none';
        renderAnswered(q, cc); 
        return; 
    }

    if (q.question_type === 'multiple_choice' || q.question_type === 'listening') { renderMC(q, cc); }
    else if (q.question_type === 'drawing') { renderDrawing(q, cc); }

    if (q.question_type === 'listening' && q.audio_url) { renderAudioPlayer(cc, q.audio_url); }

    // Logic Reset / Munculin Hint
    hintArea.style.display = 'block';
    if (answers['_hint_' + currentIdx]) {
        // Jika hint sudah pernah dipakai di soal ini, langsung tampilkan konten hint-nya
        showHintContent(q, hintArea);
    } else {
        // Jika belum, RESET tombol hint ke bentuk semula
        hintArea.innerHTML = `
            <button id="hintBtn" onclick="useHint()" class="inline-flex items-center justify-center px-5 py-3 text-sm font-black text-amber-500 bg-amber-50 dark:bg-amber-900/20 border-2 border-b-4 border-amber-200 dark:border-amber-800/50 rounded-xl hover:bg-amber-100 dark:hover:bg-amber-900/40 hover:border-amber-400 active:translate-y-1 active:border-b-2 transition-all">
                <i class="fas fa-lightbulb text-lg mr-2"></i> Gunakan Bantuan <span class="kbd border-amber-300 text-amber-600 dark:text-amber-400 ml-3">H</span>
            </button>
        `;
    }

    updateNavButtons(); updateProgress(); buildDots();
}

function renderMC(q, container) {
    let h = '<div class="grid gap-3">';
    (q.options || []).forEach((opt, i) => {
        h += '<button data-answer="' + encodeURIComponent(opt) + '" class="option-btn w-full text-left px-5 py-4 rounded-xl border-2 border-slate-200 dark:border-gray-600 text-slate-700 dark:text-slate-200 font-bold text-lg hover:border-[#1cb0f6]">';
        h += '<span class="kbd mr-3 border-slate-300 dark:border-gray-500 text-slate-400">' + (i + 1) + '</span>' + opt + '</button>';
    });
    h += '</div>';
    container.innerHTML = h;
    container.querySelectorAll('.option-btn').forEach(btn => {
        btn.addEventListener('click', () => selectAnswer(decodeURIComponent(btn.dataset.answer)));
    });
}

// ── Drawing ───────────────────────────────────────────────────
function renderDrawing(q, container) {
    templateStrokes = q.strokes || [];
    let h = '<div class="flex justify-center mb-4">';
    h += '<div class="relative bg-white dark:bg-gray-900 border-4 border-b-[8px] border-slate-200 dark:border-gray-700 rounded-3xl shadow-sm overflow-hidden" style="max-width:300px;width:100%;aspect-ratio:1">';
    h += '<div class="absolute pointer-events-none border-l-2 border-dashed border-rose-200 dark:border-rose-900/50 h-full left-1/2 opacity-60"></div>';
    h += '<div class="absolute pointer-events-none border-t-2 border-dashed border-rose-200 dark:border-rose-900/50 w-full top-1/2 opacity-60"></div>';
    h += '<canvas id="guideCanvasQ" width="300" height="300" class="block absolute top-0 left-0 z-0 pointer-events-none" style="width:100%;height:100%"></canvas>';
    h += '<canvas id="drawCanvasQ" width="300" height="300" class="block relative z-10 cursor-crosshair touch-none" style="width:100%;height:100%"></canvas>';
    h += '</div></div>';
    h += '<div class="flex justify-center gap-3 mb-4">';
    h += '<button onclick="clearDrawing()" class="px-5 py-2 text-sm font-black rounded-xl border-2 border-b-4 border-slate-200 dark:border-gray-600 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-gray-700 active:translate-y-1 active:border-b-2 transition-all"><i class="fas fa-trash-alt mr-1"></i> Reset</button>';
    h += '<button onclick="undoDrawing()" class="px-5 py-2 text-sm font-black rounded-xl border-2 border-b-4 border-slate-200 dark:border-gray-600 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-gray-700 active:translate-y-1 active:border-b-2 transition-all"><i class="fas fa-undo mr-1"></i> Undo</button>';
    h += '<button onclick="submitDrawing()" class="px-6 py-2 text-sm font-black rounded-xl border-2 border-b-4 border-[#1899d6] bg-[#1cb0f6] text-white hover:brightness-110 active:translate-y-1 active:border-b-2 transition-all shadow-sm"><i class="fas fa-check mr-1"></i> Periksa</button>';
    h += '</div>';
    h += '<div id="drawStatus" class="text-center text-sm font-black text-slate-500 dark:text-slate-400"></div>';
    container.innerHTML = h;
    setTimeout(() => initCanvas(), 50);
}

function initCanvas() {
    drawingCanvas = document.getElementById('drawCanvasQ');
    if (!drawingCanvas) return;
    drawingCtx = drawingCanvas.getContext('2d');
    drawingCtx.lineWidth = 14; drawingCtx.lineCap = 'round'; drawingCtx.lineJoin = 'round';
    allStrokes = []; currentStroke = [];
    drawGuide();
    drawingCanvas.addEventListener('mousedown', dStart);
    drawingCanvas.addEventListener('mousemove', dMove);
    drawingCanvas.addEventListener('mouseup', dEnd);
    drawingCanvas.addEventListener('mouseout', dEnd);
    drawingCanvas.addEventListener('touchstart', dStart, { passive: false });
    drawingCanvas.addEventListener('touchmove', dMove, { passive: false });
    drawingCanvas.addEventListener('touchend', dEnd);
}

function dGetPos(e) {
    const r = drawingCanvas.getBoundingClientRect();
    const sx = drawingCanvas.width / r.width, sy = drawingCanvas.height / r.height;
    const cx = e.clientX || e.touches[0].clientX, cy = e.clientY || e.touches[0].clientY;
    return { x: (cx - r.left) * sx, y: (cy - r.top) * sy };
}
function dStart(e) {
    e.preventDefault(); isDrawing = true; currentStroke = [];
    const dark = document.documentElement.classList.contains('dark');
    drawingCtx.strokeStyle = dark ? '#f8fafc' : '#2c3e50';
    const p = dGetPos(e); currentStroke.push(p);
    drawingCtx.beginPath(); drawingCtx.moveTo(p.x, p.y);
}
function dMove(e) { if (!isDrawing) return; e.preventDefault(); const p = dGetPos(e); currentStroke.push(p); drawingCtx.lineTo(p.x, p.y); drawingCtx.stroke(); }
function dEnd() { if (!isDrawing) return; isDrawing = false; if (currentStroke.length > 2) allStrokes.push(currentStroke); }

function drawGuide() {
    const gc = document.getElementById('guideCanvasQ'); 
    if (!gc || !templateStrokes || !templateStrokes.length) return;
    
    const g = gc.getContext('2d'); 
    g.clearRect(0, 0, 300, 300);

    if (!answers['_hint_' + currentIdx]) return;

    const colors = ['rgba(99,102,241,0.18)', 'rgba(234,179,8,0.18)', 'rgba(239,68,68,0.18)', 'rgba(16,185,129,0.18)', 'rgba(249,115,22,0.18)', 'rgba(168,85,247,0.18)'];
    templateStrokes.forEach((s, i) => {
        if (!s || !s.length) return;
        g.beginPath(); g.lineWidth = 12; g.lineCap = 'round'; g.lineJoin = 'round';
        g.strokeStyle = colors[i % colors.length]; g.moveTo(s[0].x, s[0].y);
        for (let j = 1; j < s.length; j++) g.lineTo(s[j].x, s[j].y);
        g.stroke();
        g.fillStyle = 'rgba(99,102,241,0.45)'; g.beginPath(); g.arc(s[0].x, s[0].y, 8, 0, Math.PI * 2); g.fill();
        g.fillStyle = 'rgba(255,255,255,0.95)'; g.font = 'bold 9px sans-serif'; g.textAlign = 'center'; g.textBaseline = 'middle';
        g.fillText(i + 1, s[0].x, s[0].y);
    });
}

function clearDrawing() { allStrokes = []; currentStroke = []; if (drawingCtx) drawingCtx.clearRect(0, 0, 300, 300); drawGuide(); }
function undoDrawing() { if (allStrokes.length > 0) { allStrokes.pop(); redrawDrawing(); } }
function redrawDrawing() {
    if (!drawingCtx) return; drawingCtx.clearRect(0, 0, 300, 300);
    const dark = document.documentElement.classList.contains('dark');
    drawingCtx.strokeStyle = dark ? '#f8fafc' : '#2c3e50';
    allStrokes.forEach(s => { if (!s.length) return; drawingCtx.beginPath(); drawingCtx.moveTo(s[0].x, s[0].y); for (let i = 1; i < s.length; i++) drawingCtx.lineTo(s[i].x, s[i].y); drawingCtx.stroke(); });
}

// ── Stroke Validation ─────────────────────────────────────────
function getBBox(strokes) { let a = Infinity, b = Infinity, c = -Infinity, d = -Infinity; strokes.forEach(s => s.forEach(p => { if (p.x < a) a = p.x; if (p.y < b) b = p.y; if (p.x > c) c = p.x; if (p.y > d) d = p.y })); return { minX: a, minY: b, maxX: c, maxY: d, width: c - a, height: d - b }; }
function normStrokes(strokes) { if (!strokes || !strokes.length) return []; const b = getBBox(strokes); const m = Math.max(b.width, b.height) || 1; const sc = 100 / m; const cx = (b.minX + b.maxX) / 2; const cy = (b.minY + b.maxY) / 2; return strokes.map(s => s.map(p => ({ x: (p.x - cx) * sc, y: (p.y - cy) * sc }))); }
function getDist(a, b) { return Math.hypot(a.x - b.x, a.y - b.y); }
function pathLen(pts) { let d = 0; for (let i = 1; i < pts.length; i++) d += getDist(pts[i - 1], pts[i]); return d; }
function resamplePts(pts, n) { if (!pts || !pts.length) return pts; let I = pathLen(pts) / (n - 1), D = 0, np = [pts[0]]; for (let i = 1; i < pts.length; i++) { let d = getDist(pts[i - 1], pts[i]); if ((D + d) >= I) { let qx = pts[i - 1].x + ((I - D) / d) * (pts[i].x - pts[i - 1].x); let qy = pts[i - 1].y + ((I - D) / d) * (pts[i].y - pts[i - 1].y); let q = { x: qx, y: qy }; np.push(q); pts.splice(i, 0, q); D = 0; } else { D += d; } } while (np.length < n) np.push(pts[pts.length - 1]); return np; }

function highlightWrongStrokes(wrongIdx) {
    redrawDrawing();
    const prev = drawingCtx.strokeStyle, prevW = drawingCtx.lineWidth;
    wrongIdx.forEach(sn => {
        const stroke = allStrokes[sn - 1]; if (!stroke || !stroke.length) return;
        drawingCtx.strokeStyle = 'rgba(239,68,68,0.75)'; drawingCtx.lineWidth = 18;
        drawingCtx.beginPath(); drawingCtx.moveTo(stroke[0].x, stroke[0].y);
        for (let i = 1; i < stroke.length; i++) drawingCtx.lineTo(stroke[i].x, stroke[i].y);
        drawingCtx.stroke();
        const bx = stroke[0].x, by = Math.max(stroke[0].y - 16, 12);
        drawingCtx.fillStyle = 'rgba(239,68,68,0.9)'; drawingCtx.beginPath(); drawingCtx.arc(bx, by, 10, 0, Math.PI * 2); drawingCtx.fill();
        drawingCtx.fillStyle = '#ffffff'; drawingCtx.font = 'bold 11px sans-serif'; drawingCtx.textAlign = 'center'; drawingCtx.textBaseline = 'middle';
        drawingCtx.fillText(sn, bx, by);
    });
    drawingCtx.strokeStyle = prev; drawingCtx.lineWidth = prevW;
}

function quizAutoSaveToDataset(normUser, targetChar) {
    try {
        if (!targetChar) return;
        const tc = document.createElement('canvas'); tc.width = 64; tc.height = 64;
        const tCtx = tc.getContext('2d'); tCtx.fillStyle = '#000'; tCtx.fillRect(0, 0, 64, 64);
        tCtx.lineWidth = 3; tCtx.lineCap = 'round'; tCtx.lineJoin = 'round'; tCtx.strokeStyle = '#fff';
        normUser.forEach(stroke => { if (!stroke.length) return; tCtx.beginPath(); tCtx.moveTo((stroke[0].x * 0.45) + 32, (stroke[0].y * 0.45) + 32); for (let i = 1; i < stroke.length; i++) { tCtx.lineTo((stroke[i].x * 0.45) + 32, (stroke[i].y * 0.45) + 32); } tCtx.stroke(); });
        fetch('/api/dataset/save', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf() }, body: JSON.stringify({ character: targetChar, image_base64: tc.toDataURL('image/png') }) }).catch(() => {});
    } catch (e) {}
}

function submitDrawing() {
    const statusEl = document.getElementById('drawStatus');
    if (allStrokes.length === 0) { statusEl.innerHTML = '<span class="text-amber-600 font-bold"><i class="fas fa-exclamation-triangle"></i> Tulis hurufnya dulu!</span>'; return; }
    if (!templateStrokes || !templateStrokes.length) { statusEl.innerHTML = '<span class="text-rose-600 font-bold"><i class="fas fa-times-circle"></i> Data template belum tersedia!</span>'; return; }

    const templateCount = templateStrokes.length, userCount = allStrokes.length;
    const normUser = normStrokes(allStrokes), normTemp = normStrokes(templateStrokes);
    const N = 40, TOL = 42, mc = Math.min(templateCount, userCount);
    let totalScore = 0, wrongStrokes = [];

    for (let i = 0; i < mc; i++) {
        const up = resamplePts(normUser[i], N), tp = resamplePts(normTemp[i], N);
        let cxU = 0, cyU = 0, cxT = 0, cyT = 0;
        for (let j = 0; j < N; j++) { cxU += up[j].x; cyU += up[j].y; cxT += tp[j].x; cyT += tp[j].y; }
        cxU /= N; cyU /= N; cxT /= N; cyT /= N;
        const posErr = getDist({ x: cxU, y: cyU }, { x: cxT, y: cyT });
        let shapeErr = 0;
        for (let j = 0; j < N; j++) shapeErr += getDist({ x: up[j].x - cxU + cxT, y: up[j].y - cyU + cyT }, tp[j]);
        shapeErr /= N;
        let pct = 100 - (shapeErr + posErr * 0.25) / TOL * 100;
        pct = Math.max(0, Math.min(100, pct));
        totalScore += pct;
        if (pct < 65) wrongStrokes.push(i + 1);
    }
    if (userCount < templateCount) { for (let k = userCount + 1; k <= templateCount; k++) wrongStrokes.push(k); }

    const overallPct = totalScore / templateCount;
    let msg = 'Akurasi: ' + overallPct.toFixed(1) + '%';
    if (userCount > templateCount) msg += '<br><span class="text-xs text-rose-600 font-bold">Kelebihan ' + (userCount - templateCount) + ' goresan!</span>';
    if (userCount < templateCount) msg += '<br><span class="text-xs text-rose-600 font-bold">Kekurangan ' + (templateCount - userCount) + ' goresan!</span>';
    if (wrongStrokes.length > 0) { msg += '<br><span class="text-xs text-rose-600 font-bold">Cek goresan ke: ' + wrongStrokes.join(', ') + '</span>'; highlightWrongStrokes(wrongStrokes); }

    const q = QUESTIONS[currentIdx];
    const targetChar = q.character;

    if (overallPct >= 75 && userCount === templateCount && wrongStrokes.length === 0) {
        statusEl.innerHTML = '<div class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-[#1cb0f6] mr-2 mb-[-3px]"></div> Validating...';
        quizAutoSaveToDataset(normUser, targetChar);

        const tc = document.createElement('canvas'); tc.width = 64; tc.height = 64;
        const tCtx = tc.getContext('2d'); tCtx.fillStyle = '#000'; tCtx.fillRect(0, 0, 64, 64);
        tCtx.lineWidth = 3; tCtx.lineCap = 'round'; tCtx.strokeStyle = '#fff';
        normUser.forEach(stroke => { if (!stroke.length) return; tCtx.beginPath(); tCtx.moveTo((stroke[0].x * 0.45) + 32, (stroke[0].y * 0.45) + 32); for (let i = 1; i < stroke.length; i++) { tCtx.lineTo((stroke[i].x * 0.45) + 32, (stroke[i].y * 0.45) + 32); } tCtx.stroke(); });

        fetch('/api/validate-ai', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf() }, body: JSON.stringify({ character: targetChar, image_base64: tc.toDataURL('image/png') }) })
            .then(r => r.json()).then(data => {
                if (data.success) {
                    if (data.is_supported === false) { statusEl.innerHTML = '<span class="text-emerald-500"><i class="fas fa-check-circle"></i> SEMPURNA!</span><br>Urutan goresan benar (' + overallPct.toFixed(1) + '%).'; submitAnswer(targetChar, overallPct); return; }
                    if (data.is_match) { statusEl.innerHTML = '<span class="text-emerald-500"><i class="fas fa-check-circle"></i> SEMPURNA!</span><br>Urutan benar. AI yakin ini <b>' + data.predicted_char + '</b>.'; submitAnswer(targetChar, overallPct); }
                    else { statusEl.innerHTML = '<span class="text-amber-500"><i class="fas fa-exclamation-circle"></i> HAMPIR!</span><br>Urutan benar, tapi AI menebak <b>' + data.predicted_char + '</b>.<br>Perbaiki proporsi!'; submitAnswer(targetChar, Math.max(overallPct * 0.6, 50)); }
                } else { statusEl.innerHTML = 'Urutan Benar (' + overallPct.toFixed(1) + '%)!<br><span class="text-xs text-rose-500">Server AI offline.</span>'; submitAnswer(targetChar, overallPct); }
            }).catch(() => { statusEl.innerHTML = 'Urutan Benar (' + overallPct.toFixed(1) + '%)!'; submitAnswer(targetChar, overallPct); });
    } else if (overallPct >= 45 && userCount === templateCount) {
        statusEl.innerHTML = '<span class="text-amber-500 font-black"><i class="fas fa-exclamation-triangle"></i> Hampir Benar!</span><br>' + msg;
    } else {
        statusEl.innerHTML = '<span class="text-rose-500 font-black"><i class="fas fa-times-circle"></i> Coba Perbaiki!</span><br>' + msg;
    }
}

function useHint() {
    const q = QUESTIONS[currentIdx];
    // Cegah pencet hint berulang
    if (answers[currentIdx] || answers['_hint_' + currentIdx]) return;
    
    // Tandai hint sudah dipakai
    answers['_hint_' + currentIdx] = true;
    showHintContent(q, document.getElementById('hintArea'));

    if (q.question_type === 'drawing') {
        drawGuide();
    }
}

function showHintContent(q, container) {
    let html = '<div class="inline-flex flex-col items-center px-6 py-4 border-2 border-b-[6px] border-amber-300 dark:border-amber-700 rounded-2xl bg-amber-50 dark:bg-amber-900/20 text-center w-full sm:w-auto">';
    html += '<span class="text-sm font-black text-amber-600 dark:text-amber-400 uppercase tracking-widest"><i class="fas fa-lightbulb text-lg mr-1"></i> Hint: <span class="text-slate-800 dark:text-white capitalize">' + (q.meaning || q.correct_answer) + '</span></span>';

    // Jika tipe soal gambar, munculkan huruf atau petunjuk goresan
    if (q.question_type === 'drawing') {
        if (q.stroke_order_image) {
            html += '<img src="' + q.stroke_order_image + '" class="mx-auto mt-4 max-h-32 rounded-xl bg-white p-2 border-2 border-slate-200 shadow-sm" alt="Stroke Order Guide">';
        } else if (q.character) {
            html += '<span class="text-6xl font-bold text-slate-800 dark:text-white block mt-4 drop-shadow-sm">' + q.character + '</span>';
        }
    }

    html += '</div>';
    container.innerHTML = html;
}

// ── Answer Submission ─────────────────────────────────────────
function selectAnswer(ans) { submitAnswer(ans, null); }

async function submitAnswer(ans, accuracyScore) {
    const q = QUESTIONS[currentIdx];
    if (answers[currentIdx]) return;

    try {
        const res = await fetch('/quizzes/' + QUIZ_ID + '/answer', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf(), 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ item_id: q.id, user_answer: String(ans), accuracy_score: accuracyScore })
        });
        if (!res.ok) {
            const errText = await res.text();
            console.error('Answer HTTP error:', res.status, errText);
            alert('Server error (' + res.status + '). Cek console untuk detail.');
            return;
        }
        const data = await res.json();
        answers[currentIdx] = { user_answer: ans, is_correct: data.is_correct, correct_answer: data.correct_answer, accuracy_score: accuracyScore };
        if (data.is_correct) streak++; else streak = 0;
        renderQuestion();
    } catch (e) { console.error('Answer error:', e); alert('Network error: ' + e.message); }
}

function renderAnswered(q, container) {
    document.getElementById('hintArea').style.display = 'none';
    const a = answers[currentIdx];
    if (q.question_type === 'drawing') {
        let h = '<div class="text-center p-6 rounded-2xl border-2 border-b-[6px] ' + (a.is_correct ? 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-300 dark:border-emerald-800' : 'bg-rose-50 dark:bg-rose-900/20 border-rose-300 dark:border-rose-800') + '">';
        h += '<p class="text-2xl font-black uppercase tracking-widest ' + (a.is_correct ? 'text-emerald-500' : 'text-rose-500') + '">' + (a.is_correct ? '<i class="fas fa-check-circle"></i> Benar!' : '<i class="fas fa-times-circle"></i> Kurang Tepat') + '</p>';
        h += '<p class="text-sm font-bold text-slate-600 dark:text-slate-400 mt-2 uppercase tracking-widest">Akurasi: ' + Math.round(a.accuracy_score || 0) + '%</p>';
        if (q.stroke_order_image) h += '<img src="' + q.stroke_order_image + '" class="mx-auto mt-4 max-h-32 rounded-xl bg-white p-2 border-2 border-slate-200 shadow-sm" alt="stroke order">';
        h += '</div>';
        container.innerHTML = h;
    } else {
        let h = '<div class="grid gap-3">';
        (q.options || []).forEach(opt => {
            let cls = 'option-btn w-full text-left px-5 py-4 rounded-xl border-2 font-bold text-lg cursor-default ';
            if (opt === a.correct_answer) cls += 'option-correct';
            else if (opt === a.user_answer && !a.is_correct) cls += 'option-wrong';
            else cls += 'border-slate-200 dark:border-gray-600 text-slate-400 dark:text-slate-500';
            h += '<button disabled class="' + cls + '">' + opt;
            if (opt === a.correct_answer) h += ' <i class="fas fa-check-circle float-right mt-1 text-white"></i>';
            if (opt === a.user_answer && !a.is_correct) h += ' <i class="fas fa-times-circle float-right mt-1 text-white"></i>';
            h += '</button>';
        });
        h += '</div>';
        container.innerHTML = h;
        if (q.question_type === 'listening' && q.audio_url) renderAudioPlayer(container, q.audio_url);
    }
    updateNavButtons(); buildDots();
}

// ── Audio Player ──────────────────────────────────────────────
function renderAudioPlayer(container, url) {
    let h = '<div class="flex justify-center mb-6"><button data-audio-url="' + encodeURIComponent(url) + '" class="audio-play-btn w-20 h-20 rounded-full border-2 border-b-[6px] border-[#1899d6] dark:border-[#1172a1] bg-[#1cb0f6] dark:bg-[#1899d6] text-white flex items-center justify-center text-3xl hover:brightness-110 active:translate-y-1 active:border-b-2 transition-all shadow-md">';
    h += '<i class="fas fa-play ml-2"></i></button></div>';
    container.insertAdjacentHTML('afterbegin', h);
    container.querySelector('.audio-play-btn').addEventListener('click', function(){ playAudio(decodeURIComponent(this.dataset.audioUrl)); });
}

let currentAudio = null;
function playAudio(url) {
    if (currentAudio) { currentAudio.pause(); currentAudio = null; }
    currentAudio = new Audio(url); currentAudio.play().catch(() => {});
}

// ── Navigation ────────────────────────────────────────────────
function nextQuestion() {
    if (currentIdx < QUESTIONS.length - 1) { currentIdx++; renderQuestion(); }
    else if (allAnswered()) { finishQuiz(); }
}
function prevQuestion() { if (currentIdx > 0) { currentIdx--; renderQuestion(); } }

function updateNavButtons() {
    document.getElementById('prevBtn').disabled = currentIdx === 0;
    const nb = document.getElementById('nextBtn');
    if (currentIdx === QUESTIONS.length - 1 && allAnswered()) { 
        nb.innerHTML = '<i class="fas fa-flag-checkered mr-2"></i> Selesai'; 
        nb.onclick = finishQuiz; 
    } else { 
        nb.innerHTML = 'Lanjut <i class="fas fa-chevron-right ml-2"></i>'; 
        nb.onclick = nextQuestion; 
    }
}

function allAnswered() { for (let i = 0; i < QUESTIONS.length; i++) { if (!answers[i]) return false; } return true; }

// ── Finish Quiz ───────────────────────────────────────────────
async function finishQuiz() {
    clearInterval(timerInterval);
    const answersPayload = QUESTIONS.map((q, i) => ({
        item_id: q.id,
        is_correct: answers[i]?.is_correct ?? false,
    }));

    try {
        const res = await fetch('/quizzes/' + QUIZ_ID + '/finish', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf() },
            body: JSON.stringify({ answers: answersPayload })
        });
        const data = await res.json();
        showResults(data);
    } catch (e) { console.error('Finish error:', e); }
}

function showResults(data) {
    document.getElementById('quizContainer').classList.add('hidden');
    document.querySelector('.quiz-bottom').classList.add('hidden');
    const rs = document.getElementById('resultsScreen'); rs.classList.remove('hidden');

    const pct = data.score, passed = data.passed;
    const circ = 2 * Math.PI * 54, offset = circ - (pct / 100) * circ;
    const gradeColor = pct >= 100 ? 'text-emerald-500' : pct >= 70 ? 'text-[#1cb0f6]' : 'text-rose-500';
    const strokeColor = pct >= 100 ? 'stroke-emerald-500' : pct >= 70 ? 'stroke-[#1cb0f6]' : 'stroke-rose-500';

    let h = '<div class="bg-white dark:bg-gray-800 border-2 border-b-[8px] border-slate-200 dark:border-gray-700 rounded-[2rem] shadow-xl p-8 text-center relative overflow-hidden">';
    h += '<div class="absolute top-0 left-0 w-full h-3 ' + (passed ? 'bg-emerald-500' : 'bg-rose-500') + '"></div>';

    if (passed) {
        h += '<h2 class="text-3xl font-black text-slate-800 dark:text-white mt-6 mb-2 uppercase tracking-widest"><i class="fas fa-crown text-amber-500 mr-2"></i> LULUS!</h2>';
        h += '<p class="text-sm text-emerald-600 dark:text-emerald-400 mb-6 font-bold uppercase tracking-widest">Kerja Bagus!</p>';
    } else {
        h += '<h2 class="text-3xl font-black text-slate-800 dark:text-white mt-6 mb-2 uppercase tracking-widest"><i class="fas fa-dumbbell text-slate-400 mr-2"></i> Terus Latihan!</h2>';
        h += '<p class="text-sm text-rose-500 dark:text-rose-400 mb-6 font-bold uppercase tracking-widest">Kamu perlu 100% untuk lewat.</p>';
    }

    h += '<div class="flex justify-center mb-8"><div class="relative w-40 h-40">';
    h += '<svg class="w-40 h-40 -rotate-90" viewBox="0 0 120 120">';
    h += '<circle cx="60" cy="60" r="54" fill="none" stroke-width="12" class="stroke-slate-200 dark:stroke-gray-700"/>';
    h += '<circle cx="60" cy="60" r="54" fill="none" stroke-width="12" stroke-linecap="round" class="score-circle ' + strokeColor + '" stroke-dasharray="' + circ + '" stroke-dashoffset="' + circ + '"/>';
    h += '</svg><div class="absolute inset-0 flex flex-col items-center justify-center">';
    h += '<span class="text-4xl font-black ' + gradeColor + '">' + Math.round(pct) + '%</span>';
    h += '<span class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">' + data.correct + '/' + data.total + ' Benar</span>';
    h += '</div></div></div>';

    h += '<div class="flex flex-col sm:flex-row gap-4 mt-6">';
    h += '<a href="/quizzes" class="flex-1 py-4 rounded-2xl text-sm font-black border-2 border-b-[6px] border-slate-200 dark:border-gray-600 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-gray-700 active:translate-y-1 active:border-b-2 transition-all text-center uppercase tracking-widest"><i class="fas fa-map-marked-alt mr-2"></i> Peta</a>';
    if (!passed) {
        let retryUrl = window.location.pathname;
        if (data.wrong_item_ids && data.wrong_item_ids.length > 0) {
            retryUrl += '?retry=' + data.wrong_item_ids.join(',');
        }
        h += '<button onclick="window.location.href=\'' + retryUrl + '\'" class="flex-1 py-4 rounded-2xl text-sm font-black border-2 border-b-[6px] border-[#1899d6] bg-[#1cb0f6] text-white hover:brightness-110 active:translate-y-1 active:border-b-2 transition-all uppercase tracking-widest"><i class="fas fa-redo-alt mr-2"></i> Coba Lagi</button>';
    } else {
        h += '<a href="/quizzes" class="flex-1 py-4 rounded-2xl text-sm font-black border-2 border-b-[6px] border-emerald-600 bg-emerald-500 text-white hover:brightness-110 active:translate-y-1 active:border-b-2 transition-all text-center uppercase tracking-widest"><i class="fas fa-forward mr-2"></i> Lanjut</a>';
    }
    h += '</div></div>';
    rs.innerHTML = h;

    setTimeout(() => { const c = rs.querySelector('.score-circle'); if (c) c.style.strokeDashoffset = offset; }, 100);
}

// ── Keyboard shortcuts ────────────────────────────────────────
document.addEventListener('keydown', e => {
    if (e.key === 'ArrowRight') nextQuestion();
    else if (e.key === 'ArrowLeft') prevQuestion();
    else if (e.key.toLowerCase() === 'h') useHint();
    else if (e.key >= '1' && e.key <= '4' && !answers[currentIdx]) {
        const btns = document.querySelectorAll('.option-btn:not(:disabled)');
        const idx = parseInt(e.key) - 1;
        if (btns[idx]) btns[idx].click();
    }
});

// ── Init ──────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    if (!QUESTIONS || !QUESTIONS.length) { window.location.href = '/quizzes'; return; }
    startTimer(); renderQuestion(); updateProgress(); buildDots();
});