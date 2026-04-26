// === QUIZ PLAY CORE ===
let sessionId=null,questions=[],currentIdx=0,answers={},streak=0,maxStreak=0;
let timerInterval=null,elapsedSeconds=0,globalTextMode=false;
let drawingCanvas=null,drawingCtx=null,isDrawing=false,currentStroke=[],allStrokes=[];
let templateStrokes=[];

function csrf(){return document.querySelector('meta[name="csrf-token"]').content}

function init(){
  const d=sessionStorage.getItem('quizSession');
  if(!d){window.location.href='/quiz';return}
  const s=JSON.parse(d);
  sessionId=s.session_id;questions=s.questions;
  startTimer();renderQuestion();updateProgress();buildDots();
}

function startTimer(){
  timerInterval=setInterval(()=>{
    elapsedSeconds++;
    const m=String(Math.floor(elapsedSeconds/60)).padStart(2,'0');
    const s=String(elapsedSeconds%60).padStart(2,'0');
    document.getElementById('timerDisplay').textContent='⏱ '+m+':'+s;
  },1000);
}

function updateProgress(){
  const pct=((currentIdx+1)/questions.length)*100;
  document.getElementById('progressBar').style.width=pct+'%';
  document.getElementById('progressText').textContent=(currentIdx+1)+' / '+questions.length+' Soal';
}

function buildDots(){
  const c=document.getElementById('dotIndicators');c.innerHTML='';
  questions.forEach((_,i)=>{
    const d=document.createElement('div');
    d.className='dot-indicator '+(i===currentIdx?'dot-current':(answers[i]?
      (answers[i].is_correct?'dot-correct':'dot-wrong'):'dot-unanswered'));
    c.appendChild(d);
  });
}

function renderQuestion(){
  const q=questions[currentIdx];
  document.getElementById('questionNumber').textContent='Soal '+(currentIdx+1)+' dari '+questions.length;
  // Type badge
  const tb=document.getElementById('typeBadge');
  if(q.question_type==='multiple_choice'){tb.textContent='📝 Pilihan Ganda';tb.className='absolute top-4 right-4 px-3 py-1 rounded-full text-xs font-bold bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400'}
  else if(q.question_type==='drawing'){tb.textContent='✏️ Menggambar';tb.className='absolute top-4 right-4 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400'}
  else{tb.textContent='🔊 Mendengarkan';tb.className='absolute top-4 right-4 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400'}
  // Streak
  const sb=document.getElementById('streakBadge');
  if(streak>=2){sb.classList.remove('hidden');document.getElementById('streakCount').textContent=streak}
  else{sb.classList.add('hidden')}
  // Question display
  const qd=document.getElementById('questionDisplay');
  const qt=document.getElementById('questionText');
  if(q.question_type==='listening'&&!globalTextMode){
    if(q.question_subtype==='listen_to_meaning'||q.question_subtype==='listen_to_character'){
      qd.innerHTML='<div class="text-6xl mb-2">🔊</div>';
      qt.textContent=q.question_text;
    }else if(q.question_subtype==='read_and_listen'){
      qd.innerHTML='<div class="text-6xl font-bold text-slate-800 dark:text-white">'+q.character+'</div>';
      qt.textContent=q.question_text;
    }else{
      qd.innerHTML='<div class="text-6xl font-bold text-slate-800 dark:text-white">'+q.character+'</div>';
      qt.textContent=q.question_text;
    }
  }else if(q.question_type==='drawing'){
    qd.innerHTML='';qt.textContent=q.question_text;
  }else{
    const hasChar=q.question_text.includes('「');
    if(hasChar){qd.innerHTML='<div class="text-6xl font-bold text-slate-800 dark:text-white">'+q.character+'</div>'}
    else{qd.innerHTML=''}
    qt.textContent=q.question_text;
  }
  // Content area
  const cc=document.getElementById('questionContent');
  if(answers[currentIdx]){renderAnswered(q,cc);return}
  if(q.question_type==='multiple_choice'||q.question_type==='listening'){renderMC(q,cc)}
  else if(q.question_type==='drawing'){renderDrawing(q,cc)}
  // Audio
  if(q.question_type==='listening'&&q.audio_url){renderAudioPlayer(cc,q.audio_url)}
  // Hint
  document.getElementById('hintArea').style.display=answers[currentIdx]?'none':'block';
  updateNavButtons();updateProgress();buildDots();
}

function renderMC(q,container){
  let h='<div class="grid gap-3">';
  q.options.forEach((opt,i)=>{
    h+='<button onclick="selectAnswer(\''+opt.replace(/'/g,"\\'")+'\')" class="option-btn w-full text-left px-5 py-4 rounded-xl border-2 border-slate-200 dark:border-gray-600 text-slate-700 dark:text-slate-200 font-medium hover:border-indigo-400 transition-all">';
    h+='<span class="kbd mr-2 border-slate-300 dark:border-gray-500 text-slate-400">'+(i+1)+'</span>'+opt+'</button>';
  });
  h+='</div>';
  container.innerHTML=h;
}

function renderDrawing(q,container){
  templateStrokes=q.strokes||[];
  let h='<div class="flex justify-center mb-4">';
  h+='<div class="relative bg-white dark:bg-gray-900 border-4 border-slate-700 dark:border-slate-500 rounded-lg shadow-inner overflow-hidden" style="width:300px;height:300px">';
  h+='<div class="absolute pointer-events-none border-l-2 border-dashed border-red-300 h-full left-1/2 opacity-60"></div>';
  h+='<div class="absolute pointer-events-none border-t-2 border-dashed border-red-300 w-full top-1/2 opacity-60"></div>';
  h+='<canvas id="guideCanvasQ" width="300" height="300" class="block absolute top-0 left-0 z-0 pointer-events-none" style="width:300px;height:300px"></canvas>';
  h+='<canvas id="drawCanvasQ" width="300" height="300" class="block relative z-10 cursor-crosshair touch-none" style="width:300px;height:300px"></canvas>';
  h+='</div></div>';
  h+='<div class="flex justify-center gap-3 mb-4">';
  h+='<button onclick="clearDrawing()" class="px-4 py-2 text-sm font-semibold rounded-xl border-2 border-slate-200 dark:border-gray-600 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-gray-700 transition-all">Reset</button>';
  h+='<button onclick="undoDrawing()" class="px-4 py-2 text-sm font-semibold rounded-xl border-2 border-slate-200 dark:border-gray-600 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-gray-700 transition-all">Undo</button>';
  h+='<button onclick="submitDrawing()" class="px-6 py-2 text-sm font-bold rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 transition-all shadow-sm">Periksa</button>';
  h+='</div>';
  h+='<div id="drawStatus" class="text-center text-sm font-medium text-slate-500 dark:text-slate-400"></div>';
  container.innerHTML=h;
  setTimeout(()=>initCanvas(),50);
}

function initCanvas(){
  drawingCanvas=document.getElementById('drawCanvasQ');
  if(!drawingCanvas)return;
  drawingCtx=drawingCanvas.getContext('2d');
  drawingCtx.lineWidth=14;drawingCtx.lineCap='round';drawingCtx.lineJoin='round';
  allStrokes=[];currentStroke=[];
  drawGuide();
  drawingCanvas.addEventListener('mousedown',dStart);
  drawingCanvas.addEventListener('mousemove',dMove);
  drawingCanvas.addEventListener('mouseup',dEnd);
  drawingCanvas.addEventListener('mouseout',dEnd);
  drawingCanvas.addEventListener('touchstart',dStart,{passive:false});
  drawingCanvas.addEventListener('touchmove',dMove,{passive:false});
  drawingCanvas.addEventListener('touchend',dEnd);
}

function dGetPos(e){
  const r=drawingCanvas.getBoundingClientRect();
  const sx=drawingCanvas.width/r.width,sy=drawingCanvas.height/r.height;
  const cx=e.clientX||e.touches[0].clientX,cy=e.clientY||e.touches[0].clientY;
  return{x:(cx-r.left)*sx,y:(cy-r.top)*sy};
}
function dStart(e){
  e.preventDefault();isDrawing=true;currentStroke=[];
  const dark=document.documentElement.classList.contains('dark');
  drawingCtx.strokeStyle=dark?'#f8fafc':'#2c3e50';
  const p=dGetPos(e);currentStroke.push(p);
  drawingCtx.beginPath();drawingCtx.moveTo(p.x,p.y);
}
function dMove(e){if(!isDrawing)return;e.preventDefault();const p=dGetPos(e);currentStroke.push(p);drawingCtx.lineTo(p.x,p.y);drawingCtx.stroke()}
function dEnd(){if(!isDrawing)return;isDrawing=false;if(currentStroke.length>2)allStrokes.push(currentStroke)}

function drawGuide(){
  const gc=document.getElementById('guideCanvasQ');if(!gc||!templateStrokes||!templateStrokes.length)return;
  const g=gc.getContext('2d');g.clearRect(0,0,300,300);
  const colors=['rgba(99,102,241,0.18)','rgba(234,179,8,0.18)','rgba(239,68,68,0.18)','rgba(16,185,129,0.18)','rgba(249,115,22,0.18)','rgba(168,85,247,0.18)'];
  templateStrokes.forEach((s,i)=>{
    if(!s||!s.length)return;
    g.beginPath();g.lineWidth=22;g.lineCap='round';g.lineJoin='round';
    g.strokeStyle=colors[i%colors.length];g.moveTo(s[0].x,s[0].y);
    for(let j=1;j<s.length;j++)g.lineTo(s[j].x,s[j].y);
    g.stroke();
    g.fillStyle='rgba(99,102,241,0.45)';g.beginPath();g.arc(s[0].x,s[0].y,8,0,Math.PI*2);g.fill();
    g.fillStyle='rgba(255,255,255,0.95)';g.font='bold 9px sans-serif';g.textAlign='center';g.textBaseline='middle';
    g.fillText(i+1,s[0].x,s[0].y);
  });
}

function clearDrawing(){allStrokes=[];currentStroke=[];if(drawingCtx)drawingCtx.clearRect(0,0,300,300);drawGuide()}
function undoDrawing(){if(allStrokes.length>0){allStrokes.pop();redrawDrawing()}}
function redrawDrawing(){
  if(!drawingCtx)return;drawingCtx.clearRect(0,0,300,300);
  const dark=document.documentElement.classList.contains('dark');
  drawingCtx.strokeStyle=dark?'#f8fafc':'#2c3e50';
  allStrokes.forEach(s=>{if(!s.length)return;drawingCtx.beginPath();drawingCtx.moveTo(s[0].x,s[0].y);for(let i=1;i<s.length;i++)drawingCtx.lineTo(s[i].x,s[i].y);drawingCtx.stroke()});
}
// === QUIZ PLAY PART 2: Answer, Results, Audio, Utilities ===

// Stroke validation math (reused from list.blade.php)
function getBBox(strokes){let a=Infinity,b=Infinity,c=-Infinity,d=-Infinity;strokes.forEach(s=>s.forEach(p=>{if(p.x<a)a=p.x;if(p.y<b)b=p.y;if(p.x>c)c=p.x;if(p.y>d)d=p.y}));return{width:c-a,height:d-b}}
function normStrokes(strokes){if(!strokes||!strokes.length)return[];const b=getBBox(strokes);const m=Math.max(b.width,b.height)||1;const sc=100/m;let cx=0,cy=0,n=0;strokes.forEach(s=>s.forEach(p=>{cx+=p.x;cy+=p.y;n++}));if(!n)return strokes;cx/=n;cy/=n;return strokes.map(s=>s.map(p=>({x:(p.x-cx)*sc,y:(p.y-cy)*sc})))}
function getDist(a,b){return Math.hypot(a.x-b.x,a.y-b.y)}
function pathLen(pts){let d=0;for(let i=1;i<pts.length;i++)d+=getDist(pts[i-1],pts[i]);return d}
function resamplePts(pts,n){if(!pts||!pts.length)return pts;let I=pathLen(pts)/(n-1),D=0,np=[pts[0]];for(let i=1;i<pts.length;i++){let d=getDist(pts[i-1],pts[i]);if((D+d)>=I){let qx=pts[i-1].x+((I-D)/d)*(pts[i].x-pts[i-1].x);let qy=pts[i-1].y+((I-D)/d)*(pts[i].y-pts[i-1].y);let q={x:qx,y:qy};np.push(q);pts.splice(i,0,q);D=0}else{D+=d}}while(np.length<n)np.push(pts[pts.length-1]);return np}

function computeDrawingAccuracy(){
  if(!allStrokes.length||!templateStrokes||!templateStrokes.length)return 0;
  const nu=normStrokes(allStrokes),nt=normStrokes(templateStrokes);
  const N=30,TOL=35,mc=Math.min(templateStrokes.length,allStrokes.length);
  let total=0;
  for(let i=0;i<mc;i++){
    const up=resamplePts(nu[i],N),tp=resamplePts(nt[i],N);
    let cxU=0,cyU=0,cxT=0,cyT=0;
    for(let j=0;j<N;j++){cxU+=up[j].x;cyU+=up[j].y;cxT+=tp[j].x;cyT+=tp[j].y}
    cxU/=N;cyU/=N;cxT/=N;cyT/=N;
    const posErr=getDist({x:cxU,y:cyU},{x:cxT,y:cyT});
    let shapeErr=0;
    for(let j=0;j<N;j++)shapeErr+=getDist({x:up[j].x-cxU+cxT,y:up[j].y-cyU+cyT},tp[j]);
    shapeErr/=N;
    let pct=100-(shapeErr+posErr*0.4)/TOL*100;
    total+=Math.max(0,Math.min(100,pct));
  }
  return total/templateStrokes.length;
}

function highlightWrongStrokes(wrongStrokeIndices){
  redrawDrawing();
  const prevStyle=drawingCtx.strokeStyle;
  const prevWidth=drawingCtx.lineWidth;
  wrongStrokeIndices.forEach(strokeNum=>{
    const stroke=allStrokes[strokeNum-1];
    if(!stroke||!stroke.length)return;
    // Overlay merah semi-transparan
    drawingCtx.strokeStyle='rgba(239,68,68,0.75)';
    drawingCtx.lineWidth=18;
    drawingCtx.beginPath();
    drawingCtx.moveTo(stroke[0].x,stroke[0].y);
    for(let i=1;i<stroke.length;i++)drawingCtx.lineTo(stroke[i].x,stroke[i].y);
    drawingCtx.stroke();
    // Badge lingkaran merah berisi nomor goresan
    const bx=stroke[0].x;
    const by=Math.max(stroke[0].y-16,12);
    drawingCtx.fillStyle='rgba(239,68,68,0.9)';
    drawingCtx.beginPath();drawingCtx.arc(bx,by,10,0,Math.PI*2);drawingCtx.fill();
    drawingCtx.fillStyle='#ffffff';
    drawingCtx.font='bold 11px sans-serif';drawingCtx.textAlign='center';drawingCtx.textBaseline='middle';
    drawingCtx.fillText(strokeNum,bx,by);
  });
  drawingCtx.strokeStyle=prevStyle;
  drawingCtx.lineWidth=prevWidth;
}

// FUNGSI AUTO-SAVE DATASET (sama persis dari list.blade.php)
function quizAutoSaveToDataset(normUser,targetChar){
  try{
    if(!targetChar)return;
    const tempCanvas=document.createElement('canvas');
    tempCanvas.width=64;tempCanvas.height=64;
    const tCtx=tempCanvas.getContext('2d');
    tCtx.fillStyle="#000000";
    tCtx.fillRect(0,0,64,64);
    tCtx.lineWidth=3;tCtx.lineCap='round';tCtx.lineJoin='round';tCtx.strokeStyle='#ffffff';
    normUser.forEach(stroke=>{
      if(!stroke.length)return;
      tCtx.beginPath();
      tCtx.moveTo((stroke[0].x*0.45)+32,(stroke[0].y*0.45)+32);
      for(let i=1;i<stroke.length;i++){
        tCtx.lineTo((stroke[i].x*0.45)+32,(stroke[i].y*0.45)+32);
      }
      tCtx.stroke();
    });
    const imageData=tempCanvas.toDataURL("image/png");
    fetch('/api/dataset/save',{
      method:'POST',
      headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf()},
      body:JSON.stringify({character:targetChar,image_base64:imageData})
    }).then(()=>{console.log("Auto-Save berhasil")}).catch(e=>{console.error('Auto-Save gagal:',e)});
  }catch(err){console.error('Error pada fungsi Auto-Save:',err)}
}

function submitDrawing(){
  const statusEl=document.getElementById('drawStatus');
  if(allStrokes.length===0){
    statusEl.innerHTML='<span class="text-amber-600 dark:text-amber-400 font-bold">Tulis hurufnya dulu!</span>';
    return;
  }
  if(!templateStrokes||!templateStrokes.length){
    statusEl.innerHTML='<span class="text-rose-600 dark:text-rose-400 font-bold">Data template belum tersedia!</span>';
    return;
  }

  const templateCount=templateStrokes.length;
  const userCount=allStrokes.length;

  console.log("%c=== MEMULAI VALIDASI STROKE ===","color: white; background: #4f46e5; font-size: 14px; padding: 4px; border-radius: 4px;");
  console.log('Jumlah Goresan Template: '+templateCount+' | Pengguna: '+userCount);

  const normUser=normStrokes(allStrokes);
  const normTemp=normStrokes(templateStrokes);
  const NUM_POINTS=30;
  const TOLERANCE_ERROR=35;
  const matchedCount=Math.min(templateCount,userCount);
  let totalScore=0;
  let wrongStrokes=[];

  for(let i=0;i<matchedCount;i++){
    const userPts=resamplePts(normUser[i],NUM_POINTS);
    const tempPts=resamplePts(normTemp[i],NUM_POINTS);
    let cxU=0,cyU=0,cxT=0,cyT=0;
    for(let j=0;j<NUM_POINTS;j++){cxU+=userPts[j].x;cyU+=userPts[j].y;cxT+=tempPts[j].x;cyT+=tempPts[j].y}
    cxU/=NUM_POINTS;cyU/=NUM_POINTS;cxT/=NUM_POINTS;cyT/=NUM_POINTS;
    const posError=getDist({x:cxU,y:cyU},{x:cxT,y:cyT});
    let shapeError=0;
    for(let j=0;j<NUM_POINTS;j++){
      const shifted={x:userPts[j].x-cxU+cxT,y:userPts[j].y-cyU+cyT};
      shapeError+=getDist(shifted,tempPts[j]);
    }
    shapeError/=NUM_POINTS;
    const totalError=shapeError+(posError*0.4);
    let strokePct=100-(totalError/TOLERANCE_ERROR)*100;
    strokePct=Math.max(0,Math.min(100,strokePct));

    console.log('[DEBUG] Goresan ke-'+(i+1)+' | Akurasi: '+strokePct.toFixed(2)+'% (Bentuk: '+shapeError.toFixed(2)+', Posisi: '+posError.toFixed(2)+')');

    totalScore+=strokePct;
    if(strokePct<65)wrongStrokes.push(i+1);
  }

  if(userCount<templateCount){
    for(let k=userCount+1;k<=templateCount;k++)wrongStrokes.push(k);
  }

  const overallPct=totalScore/templateCount;
  let msg='Akurasi Urutan: '+overallPct.toFixed(1)+'%';

  if(userCount>templateCount){
    msg+='<br><span class="text-xs font-bold text-rose-600 dark:text-rose-400">Kelebihan '+(userCount-templateCount)+' goresan!</span>';
  }
  if(userCount<templateCount){
    msg+='<br><span class="text-xs font-bold text-rose-600 dark:text-rose-400">Kekurangan '+(templateCount-userCount)+' goresan!</span>';
  }
  if(wrongStrokes.length>0){
    msg+='<br><span class="text-xs font-bold text-rose-600 dark:text-rose-400">Cek lagi goresan ke: '+wrongStrokes.join(', ')+'</span>';
    highlightWrongStrokes(wrongStrokes);
  }

  // KALO URUTAN BENAR, PANGGIL CNN DAN SIMPAN KE DATASET
  if(overallPct>=75&&userCount===templateCount&&wrongStrokes.length===0){
    // Loading
    statusEl.innerHTML='<div class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-indigo-600 mr-2 mb-[-3px]"></div> Validating...';

    const q=questions[currentIdx];
    const targetChar=q.character;

    // Auto-save dataset
    quizAutoSaveToDataset(normUser,targetChar);

    // Buat kanvas bayangan untuk dikirim ke AI (64x64, background hitam)
    const tempCanvas=document.createElement('canvas');
    tempCanvas.width=64;tempCanvas.height=64;
    const tCtx=tempCanvas.getContext('2d');
    tCtx.fillStyle="#000000";
    tCtx.fillRect(0,0,64,64);
    tCtx.lineWidth=3;tCtx.lineCap='round';tCtx.strokeStyle='#ffffff';

    normUser.forEach(stroke=>{
      if(!stroke.length)return;
      tCtx.beginPath();
      tCtx.moveTo((stroke[0].x*0.45)+32,(stroke[0].y*0.45)+32);
      for(let i=1;i<stroke.length;i++){
        tCtx.lineTo((stroke[i].x*0.45)+32,(stroke[i].y*0.45)+32);
      }
      tCtx.stroke();
    });

    const imageData=tempCanvas.toDataURL("image/png");

    // Proses Fetch ke Laravel -> Python AI
    fetch('/api/validate-ai',{
      method:'POST',
      headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf()},
      body:JSON.stringify({character:targetChar,image_base64:imageData})
    })
    .then(res=>res.json())
    .then(data=>{
      if(data.success){
        if(data.is_supported===false){
          statusEl.innerHTML='<b>SEMPURNA!</b><br>Urutan goresan benar ('+overallPct.toFixed(1)+'%).<br><span class="text-xs text-indigo-600 dark:text-indigo-400 font-normal">Sistem AI belum dilatih untuk menilai bentuk huruf ini, namun urutan goresan Anda sudah tepat!</span>';
          // Submit jawaban ke quiz backend
          submitAnswer(targetChar,overallPct);
          return;
        }

        // Buat elemen Diagram Batang (Bar Chart) dari data Top 3
        let chartHtml='<div class="mt-3 pt-2 border-t border-slate-200 dark:border-gray-600 text-left">';
        chartHtml+='<p class="text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide">Analisis Probabilitas AI:</p>';
        data.top_3.forEach((item,index)=>{
          const barColor=index===0?'bg-indigo-500':'bg-slate-300 dark:bg-slate-600';
          chartHtml+='<div class="flex items-center mb-1.5">';
          chartHtml+='<span class="w-6 text-sm font-bold text-slate-700 dark:text-slate-300">'+item.char+'</span>';
          chartHtml+='<div class="flex-1 bg-slate-100 dark:bg-gray-800 h-2.5 rounded-full mx-2 overflow-hidden border border-slate-200 dark:border-gray-700">';
          chartHtml+='<div class="'+barColor+' h-2.5 rounded-full transition-all duration-700" style="width:'+item.prob+'%"></div></div>';
          chartHtml+='<span class="text-xs w-10 text-right font-mono text-slate-500">'+item.prob+'%</span></div>';
        });
        chartHtml+='</div>';

        if(data.is_match){
          statusEl.innerHTML='<b>SEMPURNA!</b><br>Urutan goresan benar ('+overallPct.toFixed(1)+'%).<br>AI yakin ini huruf <b>'+data.predicted_char+'</b>. '+chartHtml;
          submitAnswer(targetChar,overallPct);
        }else{
          statusEl.innerHTML='<b>HAMPIR!</b><br>Urutan benar, tapi AI menebak ini huruf <b>'+data.predicted_char+'</b>.<br>Coba perbaiki proporsi garisnya! '+chartHtml;
          // Tetap submit tapi dengan akurasi lebih rendah karena AI tidak cocok
          submitAnswer(targetChar,Math.max(overallPct*0.6,50));
        }
      }else{
        statusEl.innerHTML='Urutan Benar ('+overallPct.toFixed(1)+'%)!<br><span class="text-xs font-normal text-rose-500">Server AI sedang offline.</span>';
        submitAnswer(targetChar,overallPct);
      }
    })
    .catch(()=>{
      statusEl.innerHTML='Urutan Benar ('+overallPct.toFixed(1)+'%)!<br><span class="text-xs font-normal text-rose-500">Server AI tidak terjangkau.</span>';
      submitAnswer(targetChar,overallPct);
    });

  // JIKA URUTAN MASIH SALAH, JANGAN PANGGIL AI
  }else if(overallPct>=45&&userCount===templateCount){
    statusEl.innerHTML='<span class="text-amber-600 dark:text-amber-400 font-bold">⚠️ Hampir Benar!</span><br>'+msg;
  }else{
    statusEl.innerHTML='<span class="text-rose-600 dark:text-rose-400 font-bold">❌ Coba Perbaiki!</span><br>'+msg;
  }
}

function selectAnswer(ans){submitAnswer(ans,null)}

async function submitAnswer(ans,accuracyScore){
  const q=questions[currentIdx];
  if(answers[currentIdx])return;
  const startTime=answers['_start_'+currentIdx]||elapsedSeconds;
  const timeTaken=elapsedSeconds-startTime;
  const textRevealed=answers['_revealed_'+currentIdx]||false;
  const hintUsed=answers['_hint_'+currentIdx]||false;
  try{
    const res=await fetch('/quiz/answer',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf()},
      body:JSON.stringify({question_id:q.id,user_answer:String(ans),accuracy_score:accuracyScore,time_taken_seconds:timeTaken,text_was_revealed:textRevealed,hint_was_used:hintUsed})});
    const data=await res.json();
    answers[currentIdx]={user_answer:ans,is_correct:data.is_correct,correct_answer:data.correct_answer,points_earned:data.points_earned,explanation:data.explanation,accuracy_score:accuracyScore};
    if(data.is_correct){streak++;if(streak>maxStreak)maxStreak=streak}else{streak=0}
    if(data.points_earned>0)showFloatingPoints(data.points_earned);
    renderQuestion();
  }catch(e){console.error('Answer error:',e)}
}

function showFloatingPoints(pts){
  const el=document.createElement('div');
  el.className='float-points text-emerald-500 text-2xl';
  el.textContent='+'+pts;
  el.style.top='40%';el.style.left='50%';el.style.transform='translateX(-50%)';
  document.getElementById('quizCard').appendChild(el);
  setTimeout(()=>el.remove(),1200);
}

function renderAnswered(q,container){
  document.getElementById('hintArea').style.display='none';
  const a=answers[currentIdx];
  if(q.question_type==='drawing'){
    let h='<div class="text-center p-6 rounded-xl '+(a.is_correct?'bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800':'bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800')+'">';
    h+='<p class="text-lg font-bold '+(a.is_correct?'text-emerald-600 dark:text-emerald-400':'text-rose-600 dark:text-rose-400')+'">'+(a.is_correct?'✅ Benar!':'❌ Kurang Tepat')+'</p>';
    h+='<p class="text-sm text-slate-600 dark:text-slate-300 mt-1">Akurasi: '+Math.round(a.accuracy_score)+'%</p>';
    if(q.stroke_order_image)h+='<img src="'+q.stroke_order_image+'" class="mx-auto mt-3 max-h-32 rounded-lg" alt="stroke order">';
    h+='</div>';
    container.innerHTML=h;
  }else{
    let h='<div class="grid gap-3">';
    q.options.forEach(opt=>{
      let cls='option-btn w-full text-left px-5 py-4 rounded-xl border-2 font-medium cursor-default ';
      if(opt===a.correct_answer)cls+='option-correct';
      else if(opt===a.user_answer&&!a.is_correct)cls+='option-wrong';
      else cls+='border-slate-200 dark:border-gray-600 text-slate-400 dark:text-slate-500';
      h+='<button disabled class="'+cls+'">'+opt;
      if(opt===a.correct_answer)h+=' <span class="ml-2">✅</span>';
      if(opt===a.user_answer&&!a.is_correct)h+=' <span class="ml-2">❌</span>';
      h+='</button>';
    });
    h+='</div>';
    if(a.explanation){
      h+='<div class="mt-4 p-4 rounded-xl bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800">';
      h+='<p class="text-xs font-bold text-indigo-600 dark:text-indigo-400 mb-1">📖 Contoh:</p>';
      if(a.explanation.furigana_html)h+='<p class="text-sm text-slate-700 dark:text-slate-300">'+a.explanation.furigana_html+'</p>';
      else if(a.explanation.japanese_text)h+='<p class="text-sm text-slate-700 dark:text-slate-300">'+a.explanation.japanese_text+'</p>';
      if(a.explanation.meaning)h+='<p class="text-xs text-slate-500 dark:text-slate-400 mt-1">'+a.explanation.meaning+'</p>';
      h+='</div>';
    }
    container.innerHTML=h;
  }
  // Show reading info
  if(q.kunyomi||q.onyomi){
    let info='<div class="mt-3 flex gap-4 justify-center text-xs text-slate-500 dark:text-slate-400">';
    if(q.kunyomi)info+='<span>訓: <b class="text-slate-700 dark:text-slate-200">'+q.kunyomi+'</b></span>';
    if(q.onyomi)info+='<span>音: <b class="text-slate-700 dark:text-slate-200">'+q.onyomi+'</b></span>';
    info+='</div>';
    container.innerHTML+=info;
  }
  updateNavButtons();buildDots();
}

function renderAudioPlayer(container,url){
  let h='<div class="flex justify-center mb-4"><button onclick="playAudio(\''+url+'\')" class="w-16 h-16 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-2xl hover:bg-indigo-200 dark:hover:bg-indigo-900/50 transition-all shadow-sm hover:shadow-md">';
  h+='<i class="fas fa-play"></i></button></div>';
  container.innerHTML=h+container.innerHTML;
}

let currentAudio=null;
function playAudio(url){
  if(currentAudio){currentAudio.pause();currentAudio=null}
  currentAudio=new Audio(url);currentAudio.play().catch(e=>console.error('Audio error:',e));
}

function toggleGlobalTextMode(){
  globalTextMode=!globalTextMode;
  const btn=document.getElementById('globalTextToggle');
  btn.textContent=globalTextMode?'📝':'🔊';
  btn.title=globalTextMode?'Text Mode (ON)':'Text Mode (OFF)';
  renderQuestion();
}

function useHint(){
  const q=questions[currentIdx];
  if(answers[currentIdx])return;
  answers['_hint_'+currentIdx]=true;
  const hintBtn=document.getElementById('hintBtn');
  hintBtn.disabled=true;hintBtn.textContent='💡 Hint: '+q.meaning;
  hintBtn.className='px-4 py-2 text-sm font-medium text-amber-600 dark:text-amber-400 border border-amber-300 dark:border-amber-700 rounded-xl bg-amber-50 dark:bg-amber-900/20';
}

function nextQuestion(){
  if(!answers['_start_'+(currentIdx+1)])answers['_start_'+(currentIdx+1)]=elapsedSeconds;
  if(currentIdx<questions.length-1){currentIdx++;renderQuestion()}
  else if(allAnswered()){finishQuiz()}
}
function prevQuestion(){if(currentIdx>0){currentIdx--;renderQuestion()}}

function updateNavButtons(){
  document.getElementById('prevBtn').disabled=currentIdx===0;
  const nb=document.getElementById('nextBtn');
  if(currentIdx===questions.length-1&&allAnswered()){nb.textContent='🏁 Selesai';nb.onclick=finishQuiz}
  else{nb.innerHTML='Selanjutnya → <span class="kbd ml-1 border-indigo-400 text-indigo-200">→</span>';nb.onclick=nextQuestion}
}

function allAnswered(){for(let i=0;i<questions.length;i++){if(!answers[i])return false}return true}

async function finishQuiz(){
  clearInterval(timerInterval);
  try{
    const res=await fetch('/quiz/finish',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf()},
      body:JSON.stringify({session_id:sessionId})});
    const data=await res.json();
    sessionStorage.removeItem('quizSession');
    showResults(data);
  }catch(e){console.error('Finish error:',e)}
}

function showResults(data){
  document.getElementById('quizContainer').classList.add('hidden');
  document.querySelector('.quiz-bottom').classList.add('hidden');
  const rs=document.getElementById('resultsScreen');rs.classList.remove('hidden');
  const pct=data.score;const passed=data.passed;const grade=data.grade;
  const circ=2*Math.PI*54;const offset=circ-(pct/100)*circ;
  const gradeColor=pct>=90?'text-emerald-500':pct>=70?'text-blue-500':pct>=50?'text-amber-500':'text-rose-500';
  const strokeColor=pct>=90?'stroke-emerald-500':pct>=70?'stroke-blue-500':pct>=50?'stroke-amber-500':'stroke-rose-500';
  let h='<div class="bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 rounded-3xl shadow-2xl p-8 text-center relative overflow-hidden">';
  h+='<div class="absolute top-0 left-0 w-full h-2 '+(passed?'bg-emerald-500':'bg-rose-500')+'"></div>';
  h+='<h2 class="text-2xl font-bold text-slate-800 dark:text-white mt-4 mb-6">'+(passed?'🎉 Selamat!':'😤 Jangan Menyerah!')+'</h2>';
  // Score circle
  h+='<div class="flex justify-center mb-6"><div class="relative w-36 h-36">';
  h+='<svg class="w-36 h-36 -rotate-90" viewBox="0 0 120 120">';
  h+='<circle cx="60" cy="60" r="54" fill="none" stroke-width="8" class="stroke-slate-200 dark:stroke-gray-700"/>';
  h+='<circle cx="60" cy="60" r="54" fill="none" stroke-width="8" stroke-linecap="round" class="score-circle '+strokeColor+'" stroke-dasharray="'+circ+'" stroke-dashoffset="'+circ+'"/>';
  h+='</svg>';
  h+='<div class="absolute inset-0 flex flex-col items-center justify-center">';
  h+='<span class="text-3xl font-black '+gradeColor+'">'+grade+'</span>';
  h+='<span class="text-sm font-bold text-slate-500 dark:text-slate-400">'+Math.round(pct)+'%</span>';
  h+='</div></div></div>';
  // Stats
  h+='<div class="grid grid-cols-3 gap-4 mb-6">';
  h+='<div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/20"><p class="text-2xl font-black text-emerald-600 dark:text-emerald-400">'+data.correct+'</p><p class="text-xs text-slate-500">Benar</p></div>';
  h+='<div class="p-3 rounded-xl bg-rose-50 dark:bg-rose-900/20"><p class="text-2xl font-black text-rose-600 dark:text-rose-400">'+(data.total-data.correct)+'</p><p class="text-xs text-slate-500">Salah</p></div>';
  h+='<div class="p-3 rounded-xl bg-amber-50 dark:bg-amber-900/20"><p class="text-2xl font-black text-amber-600 dark:text-amber-400">'+data.total_points+'</p><p class="text-xs text-slate-500">Poin</p></div>';
  h+='</div>';
  if(maxStreak>=2)h+='<p class="text-sm text-amber-600 dark:text-amber-400 font-bold mb-4">🔥 Streak Terpanjang: '+maxStreak+' beruntun!</p>';
  if(data.questions_with_text_revealed>0)h+='<p class="text-xs text-slate-400 mb-4">📖 '+data.questions_with_text_revealed+' soal menggunakan hint</p>';
  // Question review
  h+='<div class="text-left mt-6 border-t border-slate-200 dark:border-gray-700 pt-6">';
  h+='<h3 class="text-sm font-bold text-slate-800 dark:text-white mb-4">📋 Detail Jawaban</h3>';
  data.results.forEach((r,i)=>{
    const ic=r.is_correct;
    h+='<div class="flex items-center gap-3 py-2 border-b border-slate-100 dark:border-gray-700 last:border-0">';
    h+='<span class="text-lg">'+(ic?'✅':'❌')+'</span>';
    h+='<span class="text-lg font-bold text-slate-800 dark:text-white w-8">'+r.character+'</span>';
    h+='<span class="text-sm text-slate-600 dark:text-slate-300 flex-1">'+r.meaning+'</span>';
    if(!ic)h+='<span class="text-xs text-rose-500">Jawaban: '+r.user_answer+'</span>';
    if(r.points_earned>0)h+='<span class="text-xs font-bold text-emerald-500">+'+r.points_earned+'</span>';
    h+='</div>';
  });
  h+='</div>';
  // Buttons
  h+='<div class="flex gap-3 mt-8">';
  h+='<a href="/quiz" class="flex-1 py-3 rounded-xl text-sm font-bold border-2 border-slate-200 dark:border-gray-600 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-gray-700 transition-all text-center">↩ Menu Quiz</a>';
  h+='<a href="/quiz/history" class="flex-1 py-3 rounded-xl text-sm font-bold border-2 border-indigo-200 dark:border-indigo-800 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-all text-center">📊 Riwayat</a>';
  h+='</div></div>';
  rs.innerHTML=h;
  // Animate score circle
  setTimeout(()=>{const c=rs.querySelector('.score-circle');if(c)c.style.strokeDashoffset=offset},100);
}

// Keyboard shortcuts
document.addEventListener('keydown',e=>{
  if(e.key==='ArrowRight')nextQuestion();
  else if(e.key==='ArrowLeft')prevQuestion();
  else if(e.key.toLowerCase()==='h'&&!answers[currentIdx])useHint();
  else if(e.key>='1'&&e.key<='4'&&!answers[currentIdx]){
    const btns=document.querySelectorAll('.option-btn:not(:disabled)');
    const idx=parseInt(e.key)-1;
    if(btns[idx])btns[idx].click();
  }
});

// Track start time per question
answers['_start_0']=0;

// Init on load
document.addEventListener('DOMContentLoaded',init);
