<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class DatasetController extends Controller
{
    public function store(Request $request)
    {
        // Validasi data yang masuk
        $request->validate([
            'character' => 'required|string',
            'image_base64' => 'required|string',
        ]);

        $char = $request->character;
        $imageBase64 = $request->image_base64;

        try {
            // Bersihkan string Base64 dari embel-embel "data:image/png;base64,"
            $imageParts = explode(";base64,", $imageBase64);
            
            if (count($imageParts) == 2) {
                $imageBase64Decoded = base64_decode($imageParts[1]);
            } else {
                return response()->json(['success' => false, 'message' => 'Format gambar tidak valid.']);
            }

            // Buat nama file yang unik (Contoh: 日_1709456_abc123.png)
            $fileName = $char . '_' . time() . '_' . uniqid() . '.png';
            
            // Tentukan folder penyimpanan (Otomatis dibuatkan folder per huruf)
            // Path: storage/app/public/dataset/日/日_1709456_abc123.png
            $folderPath = 'dataset/' . $char;
            $filePath = $folderPath . '/' . $fileName;

            // Simpan gambar ke dalam storage
            Storage::disk('public')->put($filePath, $imageBase64Decoded);

            return response()->json([
                'success' => true, 
                'message' => 'Berhasil disimpan ke dataset!',
                'path' => $filePath
            ]);

        } catch (\Exception $e) {
            // Jika error, kembalikan pesan error agar tidak membuat web crash
            return response()->json([
                'success' => false, 
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    // READ: Menampilkan daftar dataset
    public function index()
    {
        // Ambil semua folder di dalam public/dataset
        $directories = Storage::disk('public')->directories('dataset');
        $datasets = [];

        foreach ($directories as $dir) {
            $char = basename($dir); // Mendapatkan nama huruf (contoh: 日)
            // Ambil semua file gambar di dalam folder tersebut
            $files = Storage::disk('public')->files($dir);
            $datasets[$char] = $files;
        }

        return view('admin.dataset.index', compact('datasets'));
    }

    // DELETE: Menghapus gambar
    public function destroy(Request $request)
    {
        $request->validate([
            'path' => 'required|string'
        ]);

        $path = $request->path;

        if (Storage::disk('public')->exists($path)) {
            // Hapus file gambar
            Storage::disk('public')->delete($path);

            // Cek apakah folder tersebut sekarang kosong
            $dir = dirname($path);
            if (empty(Storage::disk('public')->files($dir))) {
                // Jika kosong, hapus foldernya agar tidak nyampah
                Storage::disk('public')->deleteDirectory($dir);
            }

            return back()->with('success', 'Gambar dataset berhasil dihapus!');
        }

        return back()->withErrors(['Gambar tidak ditemukan.']);
    }

    // DOWNLOAD ZIP: Mengunduh 1 folder huruf menjadi ZIP
    public function downloadZip($character)
    {
        $folderPath = 'dataset/' . $character;

        if (!Storage::disk('public')->exists($folderPath)) {
            return back()->withErrors(['Folder dataset tidak ditemukan.']);
        }

        $files = Storage::disk('public')->files($folderPath);

        if (empty($files)) {
            return back()->withErrors(['Tidak ada gambar di dalam dataset ini.']);
        }

        // Nama file ZIP yang akan dihasilkan
        $zipFileName = 'Dataset_' . $character . '_' . time() . '.zip';
        // Path sementara untuk menyimpan ZIP sebelum didownload
        $zipPath = storage_path('app/public/' . $zipFileName);

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($files as $file) {
                // Ambil path absolute dari storage
                $absolutePath = storage_path('app/public/' . $file);
                // Tambahkan file ke dalam zip (menggunakan basename agar tidak menyertakan full folder path di dalam zip)
                $zip->addFile($absolutePath, basename($file));
            }
            $zip->close();
        } else {
            return back()->withErrors(['Gagal membuat file ZIP.']);
        }

        // Return download dan hapus file zip setelah berhasil didownload
        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    public function downloadAllZip()
    {
        $folderPath = 'dataset'; // Folder utama dataset

        if (!Storage::disk('public')->exists($folderPath)) {
            return back()->withErrors(['Folder dataset kosong atau tidak ditemukan.']);
        }

        // Nama file ZIP
        $zipFileName = 'Dataset_All_' . time() . '.zip';
        $zipPath = storage_path('app/public/' . $zipFileName);

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            
            $directories = Storage::disk('public')->directories($folderPath);
            $hasFiles = false;

            foreach ($directories as $dir) {
                $files = Storage::disk('public')->files($dir);
                foreach ($files as $file) {
                    $absolutePath = storage_path('app/public/' . $file);
                    
                    // Agar di dalam ZIP foldernya tetap rapi (misal: 日/gambar.png)
                    $relativePath = str_replace('dataset/', '', $file);
                    
                    $zip->addFile($absolutePath, $relativePath);
                    $hasFiles = true;
                }
            }

            $zip->close();

            // Jika ternyata folder ada tapi isinya kosong semua
            if (!$hasFiles) {
                if (file_exists($zipPath)) {
                    unlink($zipPath); // Hapus zip yang terlanjur terbuat kosong
                }
                return back()->withErrors(['Tidak ada gambar di dalam seluruh dataset.']);
            }

        } else {
            return back()->withErrors(['Gagal membuat file ZIP.']);
        }

        // Download dan hapus zip dari server
        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}