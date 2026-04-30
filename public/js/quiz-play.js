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

  // Streak badge
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

  if(q.question_type==='listening'&&q.audio_url){renderAudioPlayer(cc,q.audio_url)}

  // Hint area — reset atau restore sesuai state soal ini
  resetHintArea(q);

  updateNavButtons();updateProgress();buildDots();
}

// ── Hint helpers ──────────────────────────────────────────────

function resetHintArea(q){
  const hintArea=document.getElementById('hintArea');
  if(!hintArea)return;

  if(answers[currentIdx]){
    hintArea.style.display='none';
    return;
  }

  hintArea.style.display='block';

  if(answers['_hint_'+currentIdx]){
    // Hint sudah dipakai di soal ini → tampilkan kontennya langsung
    showHintContent(q);
  }else{
    // Belum dipakai → reset tombol ke bentuk semula
    hintArea.innerHTML=`
      <button id="hintBtn" onclick="useHint()"
        class="px-4 py-2 text-sm font-medium text-amber-600 dark:text-amber-400 border border-amber-300 dark:border-amber-700 rounded-xl bg-amber-50 dark:bg-amber-900/20 hover:bg-amber-100 dark:hover:bg-amber-900/40 transition-all">
        💡 Bantuan <span class="kbd ml-2 border-amber-300 text-amber-600 dark:text-amber-400">H</span>
      </button>`;
  }
}

function showHintContent(q){
  const hintArea=document.getElementById('hintArea');
  if(!hintArea)return;

  let html='<div class="inline-flex flex-col items-center px-5 py-3 border border-b-[4px] border-amber-300 dark:border-amber-700 rounded-xl bg-amber-50 dark:bg-amber-900/20 text-center">';
  html+='<span class="text-sm font-bold text-amber-600 dark:text-amber-400">💡 Hint: '+(q.meaning||q.correct_answer||'')+'</span>';

  if(q.question_type==='drawing'){
    if(q.stroke_order_image){
      html+='<img src="'+q.stroke_order_image+'" class="mx-auto mt-3 max-h-28 rounded-lg bg-white p-1 border border-slate-200" alt="Stroke Order">';
    }else if(q.character){
      html+='<span class="text-5xl font-bold text-slate-800 dark:text-white block mt-3">'+q.character+'</span>';
    }
  }
  html+='</div>';
  hintArea.innerHTML=html;
}

function useHint(){
  const q=questions[currentIdx];
  // Guard: soal sudah dijawab atau hint sudah dipakai
  if(answers[currentIdx]||answers['_hint_'+currentIdx])return;

  answers['_hint_'+currentIdx]=true;
  showHintContent(q);

  // Untuk soal gambar, munculkan guide stroke sekarang
  if(q.question_type==='drawing')drawGuide();
}

// ── MC Render ─────────────────────────────────────────────────

function renderMC(q,container){
  let h='<div class="grid gap-3">';
  q.options.forEach((opt,i)=>{
    h+='<button onclick="selectAnswer(\''+opt.replace(/'/g,"\\'")+'\')" class="option-btn w-full text-left px-5 py-4 rounded-xl border-2 border-slate-200 dark:border-gray-600 text-slate-700 dark:text-slate-200 font-medium hover:border-indigo-400 transition-all">';
    h+='<span class="kbd mr-2 border-slate-300 dark:border-gray-500 text-slate-400">'+(i+1)+'</span>'+opt+'</button>';
  });
  h+='</div>';
  container.innerHTML=h;
}

// ── Drawing ───────────────────────────────────────────────────

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
  const cx=e.clientX||(e.touches&&e.touches[0].clientX)||0;
  const cy=e.clientY||(e.touches&&e.touches[0].clientY)||0;
  return{x:(cx-r.left)*sx,y:(cy-r.top)*sy};
}
function dStart(e){
  e.preventDefault();isDrawing=true;currentStroke=[];
  const dark=document.documentElement.classList.contains('dark');
  drawingCtx.strokeStyle=dark?'#f8fafc':'#2c3e50';
  const p=dGetPos(e);currentStroke.push(p);
  drawingCtx.beginPath();drawingCtx.moveTo(p.x,p.y);
}
function dMove(e){if(!isDrawing)return;e.preventDefault();const p=dGetPos(e);currentStroke.push(p);drawingCtx.lineTo(p.x,p.y);drawingCtx.stroke();}
function dEnd(){if(!isDrawing)return;isDrawing=false;if(currentStroke.length>2)allStrokes.push(currentStroke);}

function drawGuide(){
  const gc=document.getElementById('guideCanvasQ');
  if(!gc||!templateStrokes||!templateStrokes.length)return;

  const g=gc.getContext('2d');
  g.clearRect(0,0,300,300);

  // Jangan render guide sebelum hint dipencet
  if(!answers['_hint_'+currentIdx])return;

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

function clearDrawing(){allStrokes=[];currentStroke=[];if(drawingCtx)drawingCtx.clearRect(0,0,300,300);drawGuide();}
function undoDrawing(){if(allStrokes.length>0){allStrokes.pop();redrawDrawing();}}
function redrawDrawing(){
  if(!drawingCtx)return;drawingCtx.clearRect(0,0,300,300);
  const dark=document.documentElement.classList.contains('dark');
  drawingCtx.strokeStyle=dark?'#f8fafc':'#2c3e50';
  allStrokes.forEach(s=>{if(!s.length)return;drawingCtx.beginPath();drawingCtx.moveTo(s[0].x,s[0].y);for(let i=1;i<s.length;i++)drawingCtx.lineTo(s[i].x,s[i].y);drawingCtx.stroke();});
}

// ── Stroke Validation ─────────────────────────────────────────

function getBBox(strokes){let a=Infinity,b=Infinity,c=-Infinity,d=-Infinity;strokes.forEach(s=>s.forEach(p=>{if(p.x<a)a=p.x;if(p.y<b)b=p.y;if(p.x>c)c=p.x;if(p.y>d)d=p.y}));return{width:c-a,height:d-b};}
function normStrokes(strokes){if(!strokes||!strokes.length)return[];const b=getBBox(strokes);const m=Math.max(b.width,b.height)||1;const sc=100/m;let cx=0,cy=0,n=0;strokes.forEach(s=>s.forEach(p=>{cx+=p.x;cy+=p.y;n++;}));if(!n)return strokes;cx/=n;cy/=n;return strokes.map(s=>s.map(p=>({x:(p.x-cx)*sc,y:(p.y-cy)*sc})));}
function getDist(a,b){return Math.hypot(a.x-b.x,a.y-b.y);}
function pathLen(pts){let d=0;for(let i=1;i<pts.length;i++)d+=getDist(pts[i-1],pts[i]);return d;}
function resamplePts(pts,n){if(!pts||!pts.length)return pts;let I=pathLen(pts)/(n-1),D=0,np=[pts[0]];for(let i=1;i<pts.length;i++){let d=getDist(pts[i-1],pts[i]);if((D+d)>=I){let qx=pts[i-1].x+((I-D)/d)*(pts[i].x-pts[i-1].x);let qy=pts[i-1].y+((I-D)/d)*(pts[i].y-pts[i-1].y);let q={x:qx,y:qy};np.push(q);pts.splice(i,0,q);D=0;}else{D+=d;}}while(np.length<n)np.push(pts[pts.length-1]);return np;}

function highlightWrongStrokes(wrongStrokeIndices){
  redrawDrawing();
  const prevStyle=drawingCtx.strokeStyle,prevWidth=drawingCtx.lineWidth;
  wrongStrokeIndices.forEach(strokeNum=>{
    const stroke=allStrokes[strokeNum-1];
    if(!stroke||!stroke.length)return;
    drawingCtx.strokeStyle='rgba(239,68,68,0.75)';drawingCtx.lineWidth=18;
    drawingCtx.beginPath();drawingCtx.moveTo(stroke[0].x,stroke[0].y);
    for(let i=1;i<stroke.length;i++)drawingCtx.lineTo(stroke[i].x,stroke[i].y);
    drawingCtx.stroke();
    const bx=stroke[0].x,by=Math.max(stroke[0].y-16,12);
    drawingCtx.fillStyle='rgba(239,68,68,0.9)';drawingCtx.beginPath();drawingCtx.arc(bx,by,10,0,Math.PI*2);drawingCtx.fill();
    drawingCtx.fillStyle='#ffffff';drawingCtx.font='bold 11px sans-serif';drawingCtx.textAlign='center';drawingCtx.textBaseline='middle';
    drawingCtx.fillText(strokeNum,bx,by);
  });
  drawingCtx.strokeStyle=prevStyle;drawingCtx.lineWidth=prevWidth;
}

function quizAutoSaveToDataset(normUser,targetChar){
  try{
    if(!targetChar)return;
    const tc=document.createElement('canvas');tc.width=64;tc.height=64;
    const tCtx=tc.getContext('2d');tCtx.fillStyle='#000';tCtx.fillRect(0,0,64,64);
    tCtx.lineWidth=3;tCtx.lineCap='round';tCtx.lineJoin='round';tCtx.strokeStyle='#fff';
    normUser.forEach(stroke=>{
      if(!stroke.length)return;
      tCtx.beginPath();tCtx.moveTo((stroke[0].x*0.45)+32,(stroke[0].y*0.45)+32);
      for(let i=1;i<stroke.length;i++)tCtx.lineTo((stroke[i].x*0.45)+32,(stroke[i].y*0.45)+32);
      tCtx.stroke();
    });
    fetch('/api/dataset/save',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf()},
      body:JSON.stringify({character:targetChar,image_base64:tc.toDataURL('image/png')})
    }).catch(()=>{});
  }catch(e){}
}

function submitDrawing(){
  const statusEl=document.getElementById('drawStatus');
  if(allStrokes.length===0){statusEl.innerHTML='<span class="text-amber-600 dark:text-amber-400 font-bold">Tulis hurufnya dulu!</span>';return;}
  if(!templateStrokes||!templateStrokes.length){statusEl.innerHTML='<span class="text-rose-600 dark:text-rose-400 font-bold">Data template belum tersedia!</span>';return;}

  const templateCount=templateStrokes.length,userCount=allStrokes.length;
  const normUser=normStrokes(allStrokes),normTemp=normStrokes(templateStrokes);
  const N=30,TOL=35,mc=Math.min(templateCount,userCount);
  let totalScore=0,wrongStrokes=[];

  for(let i=0;i<mc;i++){
    const up=resamplePts(normUser[i],N),tp=resamplePts(normTemp[i],N);
    let cxU=0,cyU=0,cxT=0,cyT=0;
    for(let j=0;j<N;j++){cxU+=up[j].x;cyU+=up[j].y;cxT+=tp[j].x;cyT+=tp[j].y;}
    cxU/=N;cyU/=N;cxT/=N;cyT/=N;
    const posErr=getDist({x:cxU,y:cyU},{x:cxT,y:cyT});
    let shapeErr=0;
    for(let j=0;j<N;j++)shapeErr+=getDist({x:up[j].x-cxU+cxT,y:up[j].y-cyU+cyT},tp[j]);
    shapeErr/=N;
    let pct=100-(shapeErr+posErr*0.4)/TOL*100;
    pct=Math.max(0,Math.min(100,pct));
    totalScore+=pct;
    if(pct<65)wrongStrokes.push(i+1);
  }
  if(userCount<templateCount){for(let k=userCount+1;k<=templateCount;k++)wrongStrokes.push(k);}

  const overallPct=totalScore/templateCount;
  let msg='Akurasi Urutan: '+overallPct.toFixed(1)+'%';
  if(userCount>templateCount)msg+='<br><span class="text-xs font-bold text-rose-600 dark:text-rose-400">Kelebihan '+(userCount-templateCount)+' goresan!</span>';
  if(userCount<templateCount)msg+='<br><span class="text-xs font-bold text-rose-600 dark:text-rose-400">Kekurangan '+(templateCount-userCount)+' goresan!</span>';
  if(wrongStrokes.length>0){msg+='<br><span class="text-xs font-bold text-rose-600 dark:text-rose-400">Cek lagi goresan ke: '+wrongStrokes.join(', ')+'</span>';highlightWrongStrokes(wrongStrokes);}

  const q=questions[currentIdx];
  const targetChar=q.character;

  if(overallPct>=75&&userCount===templateCount&&wrongStrokes.length===0){
    statusEl.innerHTML='<div class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-indigo-600 mr-2 mb-[-3px]"></div> Validating...';
    quizAutoSaveToDataset(normUser,targetChar);

    const tc=document.createElement('canvas');tc.width=64;tc.height=64;
    const tCtx=tc.getContext('2d');tCtx.fillStyle='#000';tCtx.fillRect(0,0,64,64);
    tCtx.lineWidth=3;tCtx.lineCap='round';tCtx.strokeStyle='#fff';
    normUser.forEach(stroke=>{
      if(!stroke.length)return;
      tCtx.beginPath();tCtx.moveTo((stroke[0].x*0.45)+32,(stroke[0].y*0.45)+32);
      for(let i=1;i<stroke.length;i++)tCtx.lineTo((stroke[i].x*0.45)+32,(stroke[i].y*0.45)+32);
      tCtx.stroke();
    });
    const imageData=tc.toDataURL('image/png');

    fetch('/api/validate-ai',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf()},
      body:JSON.stringify({character:targetChar,image_base64:imageData})})
    .then(res=>res.json())
    .then(data=>{
      if(data.success){
        if(data.is_supported===false){
          statusEl.innerHTML='<b>SEMPURNA!</b><br>Urutan goresan benar ('+overallPct.toFixed(1)+'%).<br><span class="text-xs text-indigo-600 dark:text-indigo-400 font-normal">Sistem AI belum dilatih untuk huruf ini, namun urutan goresan sudah tepat!</span>';
          submitAnswer(targetChar,overallPct);return;
        }
        let chartHtml='<div class="mt-3 pt-2 border-t border-slate-200 dark:border-gray-600 text-left">';
        chartHtml+='<p class="text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide">Analisis Probabilitas AI:</p>';
        data.top_3.forEach((item,index)=>{
          const barColor=index===0?'bg-indigo-500':'bg-slate-300 dark:bg-slate-600';
          chartHtml+='<div class="flex items-center mb-1.5"><span class="w-6 text-sm font-bold text-slate-700 dark:text-slate-300">'+item.char+'</span>';
          chartHtml+='<div class="flex-1 bg-slate-100 dark:bg-gray-800 h-2.5 rounded-full mx-2 overflow-hidden border border-slate-200 dark:border-gray-700">';
          chartHtml+='<div class="'+barColor+' h-2.5 rounded-full transition-all duration-700" style="width:'+item.prob+'%"></div></div>';
          chartHtml+='<span class="text-xs w-10 text-right font-mono text-slate-500">'+item.prob+'%</span></div>';
        });
        chartHtml+='</div>';
        if(data.is_match){
          statusEl.innerHTML='<b>SEMPURNA!</b><br>Urutan goresan benar ('+overallPct.toFixed(1)+'%).<br>AI yakin ini huruf <b>'+data.predicted_char+'</b>. '+chartHtml;
          submitAnswer(targetChar,overallPct);
        }else{
          statusEl.innerHTML='<b>HAMPIR!</b><br>Urutan benar, tapi AI menebak ini huruf <b>'+data.predicted_char+'</b>.<br>Coba perbaiki proporsi! '+chartHtml;
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

  }else if(overallPct>=45&&userCount===templateCount){
    statusEl.innerHTML='<span class="text-amber-600 dark:text-amber-400 font-bold">⚠️ Hampir Benar!</span><br>'+msg;
  }else{
    statusEl.innerHTML='<span class="text-rose-600 dark:text-rose-400 font-bold">❌ Coba Perbaiki!</span><br>'+msg;
  }
}

// ── Answer ────────────────────────────────────────────────────

function selectAnswer(ans){submitAnswer(ans,null);}

async function submitAnswer(ans,accuracyScore){
  const q=questions[currentIdx];
  if(answers[currentIdx])return;
  const startTime=answers['_start_'+currentIdx]||0;
  const timeTaken=elapsedSeconds-startTime;
  const hintUsed=answers['_hint_'+currentIdx]||false;
  try{
    const res=await fetch('/quiz/answer',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf()},
      body:JSON.stringify({question_id:q.id,user_answer:String(ans),accuracy_score:accuracyScore,time_taken_seconds:timeTaken,hint_was_used:hintUsed})});
    const data=await res.json();
    answers[currentIdx]={user_answer:ans,is_correct:data.is_correct,correct_answer:data.correct_answer,points_earned:data.points_earned,explanation:data.explanation,accuracy_score:accuracyScore};
    if(data.is_correct){streak++;if(streak>maxStreak)maxStreak=streak;}else{streak=0;}
    if(data.points_earned>0)showFloatingPoints(data.points_earned);
    renderQuestion();
  }catch(e){console.error('Answer error:',e);}
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
    h+='<p class="text-sm text-slate-600 dark:text-slate-300 mt-1">Akurasi: '+Math.round(a.accuracy_score||0)+'%</p>';
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
  if(q.kunyomi||q.onyomi){
    let info='<div class="mt-3 flex gap-4 justify-center text-xs text-slate-500 dark:text-slate-400">';
    if(q.kunyomi)info+='<span>訓: <b class="text-slate-700 dark:text-slate-200">'+q.kunyomi+'</b></span>';
    if(q.onyomi)info+='<span>音: <b class="text-slate-700 dark:text-slate-200">'+q.onyomi+'</b></span>';
    info+='</div>';
    container.innerHTML+=info;
  }
  updateNavButtons();buildDots();
}

// ── Audio ─────────────────────────────────────────────────────

function renderAudioPlayer(container,url){
  let h='<div class="flex justify-center mb-4"><button onclick="playAudio(\''+url+'\')" class="w-16 h-16 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-2xl hover:bg-indigo-200 dark:hover:bg-indigo-900/50 transition-all shadow-sm hover:shadow-md">';
  h+='<i class="fas fa-play"></i></button></div>';
  container.innerHTML=h+container.innerHTML;
}

let currentAudio=null;
function playAudio(url){
  if(currentAudio){currentAudio.pause();currentAudio=null;}
  currentAudio=new Audio(url);currentAudio.play().catch(e=>console.error('Audio error:',e));
}

function toggleGlobalTextMode(){
  globalTextMode=!globalTextMode;
  const btn=document.getElementById('globalTextToggle');
  btn.textContent=globalTextMode?'📝':'🔊';
  btn.title=globalTextMode?'Text Mode (ON)':'Text Mode (OFF)';
  renderQuestion();
}

// ── Navigation ────────────────────────────────────────────────

function nextQuestion(){
  if(!answers['_start_'+(currentIdx+1)])answers['_start_'+(currentIdx+1)]=elapsedSeconds;
  if(currentIdx<questions.length-1){currentIdx++;renderQuestion();}
  else if(allAnswered()){finishQuiz();}
}
function prevQuestion(){if(currentIdx>0){currentIdx--;renderQuestion();}}

function updateNavButtons(){
  document.getElementById('prevBtn').disabled=currentIdx===0;
  const nb=document.getElementById('nextBtn');
  if(currentIdx===questions.length-1&&allAnswered()){nb.textContent='🏁 Selesai';nb.onclick=finishQuiz;}
  else{nb.innerHTML='Selanjutnya → <span class="kbd ml-1 border-indigo-400 text-indigo-200">→</span>';nb.onclick=nextQuestion;}
}

function allAnswered(){for(let i=0;i<questions.length;i++){if(!answers[i])return false;}return true;}

// ── Finish & Results ──────────────────────────────────────────

async function finishQuiz(){
  clearInterval(timerInterval);
  try{
    const res=await fetch('/quiz/finish',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf()},
      body:JSON.stringify({session_id:sessionId})});
    const data=await res.json();
    sessionStorage.removeItem('quizSession');
    showResults(data);
  }catch(e){console.error('Finish error:',e);}
}

function showResults(data){
  document.getElementById('quizContainer').classList.add('hidden');
  document.querySelector('.quiz-bottom').classList.add('hidden');
  const rs=document.getElementById('resultsScreen');rs.classList.remove('hidden');

  const pct=data.score,passed=data.passed,grade=data.grade;
  const circ=2*Math.PI*54,offset=circ-(pct/100)*circ;
  const gradeColor=pct>=90?'text-emerald-500':pct>=70?'text-indigo-500':pct>=50?'text-amber-500':'text-rose-500';
  const strokeColor=pct>=90?'stroke-emerald-500':pct>=70?'stroke-indigo-500':pct>=50?'stroke-amber-500':'stroke-rose-500';

  const headerIcon=passed?'<i class="fa-solid fa-trophy text-amber-400 text-4xl mb-3 animate-bounce block"></i>':'<i class="fa-solid fa-gamepad text-slate-400 text-4xl mb-3 block"></i>';
  const headerText=passed?'Luar Biasa!':'Jangan Menyerah!';

  let detailsHtml='';
  data.results.forEach(r=>{
    const ic=r.is_correct;
    const icon=ic?'<i class="fas fa-check text-emerald-500"></i>':'<i class="fas fa-times text-rose-500"></i>';
    const bgClass=ic?'bg-emerald-50 dark:bg-emerald-900/10 border-emerald-200 dark:border-emerald-800/50':'bg-rose-50 dark:bg-rose-900/10 border-rose-200 dark:border-rose-800/50';
    detailsHtml+=`
      <div class="flex flex-col sm:flex-row sm:items-center gap-3 p-4 rounded-2xl border-2 border-b-4 ${bgClass} transition-transform hover:-translate-y-1">
        <div class="flex items-center gap-4 flex-1">
          <div class="w-12 h-12 shrink-0 bg-white dark:bg-gray-800 rounded-xl border-2 border-b-4 border-slate-200 dark:border-gray-700 flex items-center justify-center text-xl shadow-sm">${icon}</div>
          <div>
            <p class="text-2xl font-black text-slate-800 dark:text-white leading-none">${r.character}</p>
            <p class="text-[10px] sm:text-xs font-bold text-slate-500 mt-1.5 uppercase tracking-widest">${r.meaning}</p>
          </div>
        </div>
        <div class="flex flex-col sm:items-end gap-2 mt-2 sm:mt-0">
          ${!ic?`<span class="text-[10px] font-black text-rose-600 bg-rose-100 dark:bg-rose-900/30 px-3 py-1.5 rounded-lg border-2 border-rose-200 dark:border-rose-800 uppercase tracking-widest">Jawab: ${r.user_answer}</span>`:''}
          ${r.points_earned>0?`<span class="text-xs font-black text-amber-500 bg-amber-50 dark:bg-amber-900/20 px-3 py-1.5 rounded-lg border-2 border-amber-200 dark:border-amber-800 uppercase tracking-widest"><i class="fas fa-star mr-1"></i>+${r.points_earned} XP</span>`:''}
        </div>
      </div>`;
  });

  let h=`
    <div class="bg-white dark:bg-gray-800 border-2 border-b-[8px] border-slate-200 dark:border-gray-700 rounded-[2rem] shadow-sm p-6 sm:p-10 text-center relative overflow-hidden">
      <div class="absolute top-0 left-0 w-full h-3 ${passed?'bg-emerald-500':'bg-rose-500'}"></div>
      <div class="mt-4 mb-6">${headerIcon}
        <h2 class="text-3xl sm:text-4xl font-black text-slate-800 dark:text-white uppercase tracking-wider mb-1">${headerText}</h2>
        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Hasil Latihan Kamu</p>
      </div>
      <div class="flex justify-center mb-8">
        <div class="relative w-40 h-40">
          <svg class="w-40 h-40 -rotate-90" viewBox="0 0 120 120">
            <circle cx="60" cy="60" r="54" fill="none" stroke-width="12" class="stroke-slate-100 dark:stroke-gray-700"/>
            <circle cx="60" cy="60" r="54" fill="none" stroke-width="12" stroke-linecap="round" class="score-circle ${strokeColor} transition-all duration-1000 ease-out" stroke-dasharray="${circ}" stroke-dashoffset="${circ}"/>
          </svg>
          <div class="absolute inset-0 flex flex-col items-center justify-center">
            <span class="text-4xl font-black ${gradeColor}">${grade}</span>
            <span class="text-sm font-bold text-slate-400 mt-1">${Math.round(pct)}%</span>
          </div>
        </div>
      </div>
      <div class="grid grid-cols-3 gap-3 sm:gap-4 mb-6">
        <div class="p-3 sm:p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 border-2 border-b-4 border-emerald-200 dark:border-emerald-800">
          <p class="text-2xl sm:text-3xl font-black text-emerald-500">${data.correct}</p>
          <p class="text-[10px] sm:text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest mt-1">Benar</p>
        </div>
        <div class="p-3 sm:p-4 rounded-2xl bg-rose-50 dark:bg-rose-900/20 border-2 border-b-4 border-rose-200 dark:border-rose-800">
          <p class="text-2xl sm:text-3xl font-black text-rose-500">${data.total-data.correct}</p>
          <p class="text-[10px] sm:text-xs font-bold text-rose-600 dark:text-rose-400 uppercase tracking-widest mt-1">Salah</p>
        </div>
        <div class="p-3 sm:p-4 rounded-2xl bg-amber-50 dark:bg-amber-900/20 border-2 border-b-4 border-amber-200 dark:border-amber-800">
          <p class="text-2xl sm:text-3xl font-black text-amber-500">${data.total_points}</p>
          <p class="text-[10px] sm:text-xs font-bold text-amber-600 dark:text-amber-400 uppercase tracking-widest mt-1">Poin</p>
        </div>
      </div>
      <div class="flex flex-col sm:flex-row justify-center items-center gap-3 mb-8">
        ${maxStreak>=2?`<div class="inline-flex items-center px-4 py-2 bg-amber-100 dark:bg-amber-900/30 border-2 border-amber-200 dark:border-amber-800 rounded-xl text-xs font-black text-amber-600 dark:text-amber-400 uppercase tracking-widest"><i class="fas fa-fire mr-2"></i> ${maxStreak} Streak beruntun!</div>`:''}
        ${data.questions_with_text_revealed>0?`<div class="inline-flex items-center px-4 py-2 bg-slate-100 dark:bg-gray-700/50 border-2 border-slate-200 dark:border-gray-600 rounded-xl text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest"><i class="fas fa-eye mr-2"></i> ${data.questions_with_text_revealed} Hint Dipakai</div>`:''}
      </div>
      <div class="text-left mt-8 border-t-2 border-dashed border-slate-200 dark:border-gray-700 pt-8">
        <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2"><i class="fas fa-clipboard-list text-lg"></i> Detail Jawaban</h3>
        <div class="space-y-3">${detailsHtml}</div>
      </div>
      <div class="flex flex-col sm:flex-row gap-4 mt-10">
        <a href="/quiz" class="w-full sm:flex-1 py-4 bg-slate-100 dark:bg-gray-800 border-2 border-b-[6px] border-slate-300 dark:border-gray-600 text-slate-600 dark:text-slate-300 font-black uppercase tracking-widest rounded-[1.25rem] hover:bg-slate-200 dark:hover:bg-gray-700 active:border-b-2 active:translate-y-1 transition-all shadow-sm flex items-center justify-center gap-2"><i class="fas fa-arrow-left"></i> Menu Quiz</a>
        <a href="/quiz/history" class="w-full sm:flex-1 py-4 bg-[#1cb0f6] border-2 border-b-[6px] border-[#1899d6] text-white font-black uppercase tracking-widest rounded-[1.25rem] hover:brightness-110 active:border-b-2 active:translate-y-1 transition-all shadow-sm flex items-center justify-center gap-2"><i class="fas fa-chart-bar"></i> Lihat Riwayat</a>
      </div>
    </div>`;

  rs.innerHTML=h;
  setTimeout(()=>{const c=rs.querySelector('.score-circle');if(c)c.style.strokeDashoffset=offset;},100);
}

// ── Keyboard shortcuts ────────────────────────────────────────

document.addEventListener('keydown',e=>{
  if(e.key==='ArrowRight')nextQuestion();
  else if(e.key==='ArrowLeft')prevQuestion();
  // Guard: jangan trigger kalau sudah dijawab atau hint sudah dipakai
  else if(e.key.toLowerCase()==='h'&&!answers[currentIdx]&&!answers['_hint_'+currentIdx])useHint();
  else if(e.key>='1'&&e.key<='4'&&!answers[currentIdx]){
    const btns=document.querySelectorAll('.option-btn:not(:disabled)');
    const idx=parseInt(e.key)-1;
    if(btns[idx])btns[idx].click();
  }
});

// ── Init ──────────────────────────────────────────────────────

answers['_start_0']=0;
document.addEventListener('DOMContentLoaded',init);