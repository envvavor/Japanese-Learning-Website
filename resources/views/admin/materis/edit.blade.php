@extends('layouts.admin')

@section('title', 'Edit Materi')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.materis.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium transition-colors flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Materi
    </a>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden max-w-5xl">
    <div class="border-b border-gray-100 dark:border-gray-700 px-6 py-5 bg-gray-50 dark:bg-gray-800/50 flex justify-between items-center">
        <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100 flex items-center">
            <i class="fas fa-edit text-indigo-500 dark:text-indigo-400 mr-2 border dark:border-gray-600 bg-white dark:bg-gray-700 rounded-full p-2 shadow-sm"></i> Edit Materi: {{ $materi->title }}
        </h3>
    </div>
    <div class="p-6 md:p-8">
        <form action="{{ route('admin.materis.update', $materi) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <label for="title" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Judul Materi <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title', $materi->title) }}" required
                       class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-sm placeholder-gray-400 dark:placeholder-gray-500"
                       placeholder="Contoh: Pengenalan Huruf Hiragana">
                @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="materiEditor" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Konten <span class="text-red-500">*</span></label>
                <textarea id="materiEditor" name="content">{{ old('content', $materi->content) }}</textarea>
                @error('content')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end pt-5 mt-5 border-t border-gray-100 dark:border-gray-700">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-100 text-white font-bold py-3 px-8 rounded-lg shadow-md transition-all flex items-center text-lg transform hover:-translate-y-0.5">
                    <i class="fas fa-save mr-2"></i> Perbarui Materi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>
<script>
    const isDark = document.documentElement.classList.contains('dark');
    
    // Fungsi untuk memutar suara Web Speech API
    function playTTS(text) {
        if ('speechSynthesis' in window) {
            window.speechSynthesis.cancel(); // Hentikan suara sebelumnya
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'ja-JP'; // Set aksen Jepang
            utterance.rate = 0.9;
            window.speechSynthesis.speak(utterance);
        } else {
            alert("Browser Anda tidak mendukung fitur Text-to-Speech.");
        }
    }
    
    tinymce.init({
        selector: '#materiEditor',
        license_key: 'gpl',
        
        extended_valid_elements: 'svg[*],path[*]',
        custom_elements: 'svg,path',

        // Dark mode skin detection
        skin: isDark ? 'oxide-dark' : 'oxide',
        content_css: isDark ? 'dark' : 'default',
        
        plugins: 'autosave image lists link table code wordcount fullscreen',
        // 1. TAMBAH 'add_furigana' dan 'add_tts' DI TOOLBAR (setelah strikethrough)
        toolbar: 'restoredraft | undo redo | blocks | bold italic underline strikethrough | add_furigana add_tts | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image table | code fullscreen | removeformat',
        
        autosave_interval: '15s',
        autosave_restore_when_empty: true,
        autosave_retention: '120m',
        
        menubar: 'file edit view insert format tools table',
        height: 500,
        branding: false,
        promotion: false,
        automatic_uploads: true,
        images_upload_url: '{{ route("admin.materis.uploadImage") }}',
        images_upload_credentials: true,
        file_picker_types: 'image',
        images_reuse_filename: true,
        relative_urls: false,
        remove_script_host: true,
        convert_urls: true,
        
        setup: function (editor) {
            // Pengaturan bawaan Anda
            editor.on('init', function () {
                editor.getBody().style.fontFamily = 'Inter, sans-serif';
                editor.getBody().style.fontSize = '15px';
                editor.getBody().style.lineHeight = '1.7';
            });

            // SUNTIK EVENT CLICK: Bikin tombol TTS bisa diklik langsung di Editor Admin
            editor.on('click', function(e) {
                const btn = e.target.closest('.btn-inline-tts');
                if (btn) {
                    const textToSpeak = btn.getAttribute('data-speech');
                    if (textToSpeak) {
                        playTTS(textToSpeak);
                    }
                }
            });

            // Register Ikon Kustom untuk Furigana (Huruf 'A' Jepang)
            editor.ui.registry.addIcon('furigana-icon', '<svg width="24" height="24" viewBox="0 0 24 24"><text x="4" y="18" font-family="sans-serif" font-size="16" fill="currentColor">あ</text></svg>');
            
            // 2. LOGIKA TOMBOL FURIGANA KUSTOM
            editor.ui.registry.addButton('add_furigana', {
                text: '🇯🇵 Furigana',
                tooltip: 'Tambah Huruf Kecil di atas Kanji',
                onAction: function () {
                    editor.windowManager.open({
                        title: 'Sisipkan Furigana',
                        body: {
                            type: 'panel',
                            items: [
                                { type: 'input', name: 'kanji', label: 'Teks Kanji (Contoh: 漢字)' },
                                { type: 'input', name: 'furigana', label: 'Cara Baca (Contoh: かんじ)' }
                            ]
                        },
                        buttons: [
                            { type: 'cancel', text: 'Batal' },
                            { type: 'submit', text: 'Sisipkan', primary: true }
                        ],
                        onSubmit: function (api) {
                            var data = api.getData();
                            if (data.kanji && data.furigana) {
                                // Masukkan tag ruby ke posisi kursor
                                editor.insertContent('<ruby>' + data.kanji + '<rt>' + data.furigana + '</rt></ruby>&#8203;');
                            }
                            api.close();
                        }
                    });
                }
            });

            // TOMBOL TTS 
            editor.ui.registry.addButton('add_tts', {
                text: 'TTS',
                // Gunakan ikon 'non-existent' agar TinyMCE tidak memaksakan ikon bawaannya
                tooltip: 'Jadikan Teks Bersuara (Klik di editor untuk tes)',
                onAction: function () {
                    const selectedText = editor.selection.getContent({format: 'text'});
                    
                    editor.windowManager.open({
                        title: 'Sisipkan Tombol Suara',
                        body: {
                            type: 'panel',
                            items: [
                                { type: 'input', name: 'displayText', label: 'Teks yang Ditampilkan' },
                                { type: 'input', name: 'speechText', label: 'Teks untuk Dibunyikan' }
                            ]
                        },
                        initialData: {
                            displayText: selectedText,
                            speechText: selectedText
                        },
                        buttons: [
                            { type: 'cancel', text: 'Batal' },
                            { type: 'submit', text: 'Sisipkan Tombol', primary: true }
                        ],
                        onSubmit: function (api) {
                            var data = api.getData();
                            if (data.displayText) {
                                // SVG Speaker Murni
                                const iconSVG = `<svg width="14" height="14" viewBox="0 0 512 512" style="margin-right:6px; vertical-align:middle; fill:currentColor"><path d="M215.03 71.05L126.06 160H24c-13.26 0-24 10.74-24 24v144c0 13.25 10.74 24 24 24h102.06l88.97 88.95c15.03 15.03 40.97 4.47 40.97-16.97V88.02c0-21.46-25.96-31.98-40.97-16.97zm233.32-51.08c-11.17-7.56-26.37-4.6-33.93 6.57l-17.02 25.13c-7.56 11.17-4.6 26.38 6.57 33.94C428.52 101.99 448 126.79 448 156v199.96c0 29.17-19.46 53.97-43.98 70.36-11.17 7.56-14.13 22.77-6.57 33.94l17.02 25.13c7.56 11.17 22.77 14.13 33.93 6.57C486.58 466.58 512 423.82 512 355.96V156c0-67.81-25.4-110.59-63.65-136.03z"/></svg>`;
                                
                                const html = `<button type="button" class="btn-inline-tts" data-speech="${data.speechText || data.displayText}" title="Dengarkan pengucapan">${iconSVG}${data.displayText}</button>&nbsp;`;
                                editor.insertContent(html);
                            }
                            api.close();
                        }
                    });
                }
            });
        },
        
        images_upload_handler: function (blobInfo, progress) {
            return new Promise(function (resolve, reject) {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '{{ route("admin.materis.uploadImage") }}');

                // Set CSRF token in header
                xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

                xhr.upload.onprogress = function (e) {
                    progress(e.loaded / e.total * 100);
                };

                xhr.onload = function () {
                    if (xhr.status === 422) {
                        reject({ message: 'Validasi gagal: ' + xhr.responseText, remove: true });
                        return;
                    }

                    if (xhr.status < 200 || xhr.status >= 300) {
                        reject('HTTP Error: ' + xhr.status);
                        return;
                    }

                    var json = JSON.parse(xhr.responseText);

                    if (!json || typeof json.location !== 'string') {
                        reject('Invalid JSON response: ' + xhr.responseText);
                        return;
                    }

                    resolve(json.location);
                };

                xhr.onerror = function () {
                    reject('Image upload failed. Please check your connection and try again.');
                };

                var formData = new FormData();
                formData.append('file', blobInfo.blob(), blobInfo.filename());
                xhr.send(formData);
            });
        },
        
        // 3. TAMBAHKAN CSS RUBY/RT AGAR RAPI DI DALAM EDITOR
        content_style: isDark 
            ? `body { font-family: Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; font-size: 15px; line-height: 1.7; color: #e2e8f0; background-color: #1e293b; padding: 8px 12px; }
               img { max-width: 100%; height: auto; border-radius: 8px; margin: 1em 0; }
               p { margin: 0.75em 0; }
               ul, ol { padding-left: 1.5em; }
               h1, h2, h3, h4 { font-weight: 700; margin: 1em 0 0.5em; color: #f1f5f9; }
               ruby { margin-right: 0.1em; ruby-align: center; }
               rt { color: #94a3b8; font-size: 0.65em; font-weight: 500; transform: translateY(-10%); }
               a { color: #818cf8; }
               .btn-inline-tts { background: #3730a3; color: #818cf8; border: 1px solid #4f46e5; padding: 3px 10px; border-radius: 6px; cursor: pointer; font-weight: 600; font-family: inherit; font-size: 14px; text-decoration: none; margin: 0 2px; transition: all 0.2s; display: inline-flex; align-items: center;}
               .btn-inline-tts:hover { background: #4f46e5; color: white; }
               .btn-inline-tts svg { pointer-events: none; display: inline-block; width: 14px; height: 14px; flex-shrink: 0; }`
            : `body { font-family: Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; font-size: 15px; line-height: 1.7; color: #1e293b; padding: 8px 12px; }
               img { max-width: 100%; height: auto; border-radius: 8px; margin: 1em 0; }
               p { margin: 0.75em 0; }
               ul, ol { padding-left: 1.5em; }
               h1, h2, h3, h4 { font-weight: 700; margin: 1em 0 0.5em; color: #0f172a; }
               ruby { margin-right: 0.1em; ruby-align: center; }
               rt { color: #64748b; font-size: 0.65em; font-weight: 500; transform: translateY(-10%); }
               .btn-inline-tts { background: #e0e7ff; color: #4f46e5; border: 1px solid #c7d2fe; padding: 3px 10px; border-radius: 6px; cursor: pointer; font-weight: 600; font-family: inherit; font-size: 14px; text-decoration: none; margin: 0 2px; transition: all 0.2s; display: inline-flex; align-items: center;}
               .btn-inline-tts:hover { background: #4f46e5; color: white; }
               .btn-inline-tts svg { pointer-events: none; display: inline-block; width: 14px; height: 14px; flex-shrink: 0; }`
    });
</script>
@endpush
