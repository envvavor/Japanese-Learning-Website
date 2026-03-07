@extends('layouts.admin')

@section('title', 'Tambah Materi')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.materis.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium transition-colors flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Materi
    </a>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden max-w-5xl">
    <div class="border-b border-gray-100 dark:border-gray-700 px-6 py-5 bg-gray-50 dark:bg-gray-800/50 flex justify-between items-center">
        <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100 flex items-center">
            <i class="fas fa-plus-circle text-indigo-500 dark:text-indigo-400 mr-2 border dark:border-gray-600 bg-white dark:bg-gray-700 rounded-full p-2 shadow-sm"></i> Tambah Materi Baru
        </h3>
    </div>
    <div class="p-6 md:p-8">
        <form action="{{ route('admin.materis.store') }}" method="POST">
            @csrf

            <div class="mb-6">
                <label for="title" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Judul Materi <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required
                       class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-sm placeholder-gray-400 dark:placeholder-gray-500"
                       placeholder="Contoh: Pengenalan Huruf Hiragana">
                @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="materiEditor" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Konten <span class="text-red-500">*</span></label>
                <textarea id="materiEditor" name="content">{{ old('content') }}</textarea>
                @error('content')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end pt-5 mt-5 border-t border-gray-100 dark:border-gray-700">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-100 text-white font-bold py-3 px-8 rounded-lg shadow-md transition-all flex items-center text-lg transform hover:-translate-y-0.5">
                    <i class="fas fa-save mr-2"></i> Simpan Materi
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
    
    tinymce.init({
        selector: '#materiEditor',
        license_key: 'gpl',
        
        // Dark mode skin detection
        skin: isDark ? 'oxide-dark' : 'oxide',
        content_css: isDark ? 'dark' : 'default',
        
        // 1. TAMBAH 'autosave' DI PLUGINS
        plugins: 'autosave image lists link table code wordcount fullscreen',
        
        // 2. TAMBAH 'restoredraft' DI TOOLBAR PALING DEPAN
        toolbar: 'restoredraft | undo redo | blocks | bold italic underline strikethrough | add_furigana | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image table | code fullscreen | removeformat',
        
        // 3. KONFIGURASI AUTOSAVE
        autosave_interval: '15s', // Simpan setiap 15 detik
        autosave_restore_when_empty: true, // Pulihkan otomatis jika kosong saat refresh
        autosave_retention: '60m', // Simpan di browser selama 60 menit
        
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
            editor.on('init', function () {
                editor.getBody().style.fontFamily = 'Inter, sans-serif';
                editor.getBody().style.fontSize = '15px';
                editor.getBody().style.lineHeight = '1.7';
            });

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
                                editor.insertContent('<ruby>' + data.kanji + '<rt>' + data.furigana + '</rt></ruby>&nbsp;');
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
        
        content_style: isDark 
            ? `body { font-family: Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; font-size: 15px; line-height: 1.7; color: #e2e8f0; background-color: #1e293b; padding: 8px 12px; }
               img { max-width: 100%; height: auto; border-radius: 8px; margin: 1em 0; }
               p { margin: 0.75em 0; }
               ul, ol { padding-left: 1.5em; }
               h1, h2, h3, h4 { font-weight: 700; margin: 1em 0 0.5em; color: #f1f5f9; }
               ruby { margin-right: 0.1em; ruby-align: center; }
               rt { color: #94a3b8; font-size: 0.65em; font-weight: 500; transform: translateY(-10%); }
               a { color: #818cf8; }`
            : `body { font-family: Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; font-size: 15px; line-height: 1.7; color: #1e293b; padding: 8px 12px; }
               img { max-width: 100%; height: auto; border-radius: 8px; margin: 1em 0; }
               p { margin: 0.75em 0; }
               ul, ol { padding-left: 1.5em; }
               h1, h2, h3, h4 { font-weight: 700; margin: 1em 0 0.5em; color: #0f172a; }
               ruby { margin-right: 0.1em; ruby-align: center; }
               rt { color: #64748b; font-size: 0.65em; font-weight: 500; transform: translateY(-10%); }`
    });

    // 4. SCRIPT PENYIMPANAN JUDUL (AUTOSAVE TITLE)
    document.addEventListener('DOMContentLoaded', function () {
        const titleInput = document.getElementById('title');
        const form = document.querySelector('form');

        // Cek dan kembalikan judul dari memori saat halaman dimuat
        if (localStorage.getItem('draft_materi_title')) {
            // Jangan timpa jika old('title') dari error validasi Laravel sudah ada
            if (!titleInput.value) {
                titleInput.value = localStorage.getItem('draft_materi_title');
            }
        }

        // Simpan setiap kali ada ketikan
        titleInput.addEventListener('input', function() {
            localStorage.setItem('draft_materi_title', this.value);
        });

        // Hapus draf dari memori ketika tombol submit ditekan
        form.addEventListener('submit', function() {
            localStorage.removeItem('draft_materi_title');
        });
    });
</script>
@endpush