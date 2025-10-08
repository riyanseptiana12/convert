<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ConvertController extends Controller
{
    public function index()
    {
        return view('convert.index');
    }

        public function nomer()
    {
        return view('convert.nomer');
    }

    public function convert(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:txt',
        'prefix' => 'required|string',
        'filename' => 'required|string',
    ]);

    $prefix = $request->input('prefix');
    $filename = preg_replace('/[^A-Za-z0-9_\-]/', '_', $request->input('filename'));
    // bersihin nama file biar aman

    $txtContent = file($request->file('file')->getRealPath(), FILE_IGNORE_NEW_LINES);

    $vcfContent = "";
    $counter = 1;
    foreach ($txtContent as $line) {
        // Ambil nomor telepon
        if (strpos($line, ':') !== false) {
            $parts = explode(':', $line, 2);
            $phone = trim($parts[1]);
        } else {
            $phone = trim($line);
        }

        // Nama generate dari prefix
        $name = $prefix . " " . str_pad($counter, 3, '0', STR_PAD_LEFT);

        $vcfContent .= "BEGIN:VCARD\n";
        $vcfContent .= "VERSION:3.0\n";
        $vcfContent .= "FN:{$name}\n";
        $vcfContent .= "TEL;TYPE=CELL:{$phone}\n";
        $vcfContent .= "END:VCARD\n";

        $counter++;
    }

    $fileName = $filename . ".vcf";

    return response($vcfContent)
        ->header('Content-Type', 'text/x-vcard')
        ->header('Content-Disposition', "attachment; filename={$fileName}");
}

public function convertFromNumbers(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'numbers' => 'required|string',
            'admin_prefix' => 'required|string|max:50',
            'navy_prefix' => 'required|string|max:50',
            'admin_count' => 'required|integer|min:0|max:50',
            'navy_count' => 'required|integer|min:0|max:100',
            'filename' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Parse nomor dari textarea
            $numbersText = $request->input('numbers');
            $lines = explode("\n", $numbersText);

            // Filter dan bersihkan nomor
            $phoneNumbers = [];
            foreach ($lines as $line) {
                $line = trim($line);
                if (!empty($line) && preg_match('/[\d+]/', $line)) {
                    // Bersihkan nomor dari karakter yang tidak perlu
                    $cleanNumber = preg_replace('/[^\d\+\-\(\)\s]/', '', $line);
                    $cleanNumber = preg_replace('/\s+/', ' ', trim($cleanNumber));
                    if (!empty($cleanNumber)) {
                        $phoneNumbers[] = $cleanNumber;
                    }
                }
            }

            if (empty($phoneNumbers)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada nomor telepon yang valid ditemukan'
                ], 400);
            }

            // Ambil parameter
            $adminPrefix = $request->input('admin_prefix');
            $navyPrefix = $request->input('navy_prefix');
            $adminCount = (int) $request->input('admin_count');
            $navyCount = (int) $request->input('navy_count');
            $filename = preg_replace('/[^A-Za-z0-9_\-]/', '_', $request->input('filename'));

            $vcfContent = "";
            $index = 0;

            // Generate Admin contacts
            for ($i = 1; $i <= $adminCount && $index < count($phoneNumbers); $i++) {
                $name = $adminPrefix . " " . str_pad($i, 3, '0', STR_PAD_LEFT);
                $phone = $phoneNumbers[$index];
                $vcfContent .= $this->generateVCardEntry($name, $phone);
                $index++;
            }

            // Generate Navy contacts
            for ($i = 1; $i <= $navyCount && $index < count($phoneNumbers); $i++) {
                $name = $navyPrefix . " " . str_pad($i, 3, '0', STR_PAD_LEFT);
                $phone = $phoneNumbers[$index];
                $vcfContent .= $this->generateVCardEntry($name, $phone);
                $index++;
            }

            // Generate Others contacts (remaining numbers)
            if ($index < count($phoneNumbers)) {
                $otherCount = 1;
                for ($index; $index < count($phoneNumbers); $index++) {
                    $name = "Others " . str_pad($otherCount, 3, '0', STR_PAD_LEFT);
                    $phone = $phoneNumbers[$index];
                    $vcfContent .= $this->generateVCardEntry($name, $phone);
                    $otherCount++;
                }
            }

            $fileName = $filename . ".vcf";

            return response($vcfContent)
                ->header('Content-Type', 'text/x-vcard; charset=utf-8')
                ->header('Content-Disposition', "attachment; filename={$fileName}")
                ->header('Cache-Control', 'no-cache, must-revalidate')
                ->header('Expires', 'Sat, 26 Jul 1997 05:00:00 GMT');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses: ' . $e->getMessage()
            ], 500);
        }
    }
    public function convertMultiGroup(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:txt|max:2048',
            'admin_prefix' => 'required|string|max:50',
            'navy_prefix' => 'required|string|max:50',
            'admin_count' => 'required|integer|min:1|max:50',
            'navy_count' => 'required|integer|min:1|max:100',
            'filename' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Ambil parameter dari form
            $adminPrefix = $request->input('admin_prefix');
            $navyPrefix = $request->input('navy_prefix');
            $adminCount = (int) $request->input('admin_count');
            $navyCount = (int) $request->input('navy_count');
            $filename = preg_replace('/[^A-Za-z0-9_\-]/', '_', $request->input('filename'));

            // Baca isi file TXT
            $txtContent = file($request->file('file')->getRealPath(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            // Filter hanya baris yang tidak kosong
            $txtContent = array_filter($txtContent, function($line) {
                return !empty(trim($line));
            });

            // Reset array index
            $txtContent = array_values($txtContent);

            $vcfContent = "";
            $totalNumbers = count($txtContent);

            // Cek apakah jumlah nomor mencukupi
            $requiredNumbers = $adminCount + $navyCount;
            if ($totalNumbers < $requiredNumbers) {
                return back()->withErrors([
                    'file' => "File hanya berisi {$totalNumbers} nomor, tapi membutuhkan minimal {$requiredNumbers} nomor (Admin: {$adminCount} + Navy: {$navyCount})"
                ])->withInput();
            }

            $lineIndex = 0;

            // Proses nomor untuk Admin
            for ($i = 1; $i <= $adminCount; $i++) {
                if ($lineIndex >= $totalNumbers) break;

                $line = $txtContent[$lineIndex];
                $phone = $this->extractPhoneNumber($line);

                if (!empty($phone)) {
                    $name = $adminPrefix . " " . str_pad($i, 3, '0', STR_PAD_LEFT);
                    $vcfContent .= $this->generateVCardEntry($name, $phone);
                }

                $lineIndex++;
            }

            // Proses nomor untuk Navy
            for ($i = 1; $i <= $navyCount; $i++) {
                if ($lineIndex >= $totalNumbers) break;

                $line = $txtContent[$lineIndex];
                $phone = $this->extractPhoneNumber($line);

                if (!empty($phone)) {
                    $name = $navyPrefix . " " . str_pad($i, 3, '0', STR_PAD_LEFT);
                    $vcfContent .= $this->generateVCardEntry($name, $phone);
                }

                $lineIndex++;
            }

            // Jika masih ada nomor sisa, tambahkan sebagai "Others"
            if ($lineIndex < $totalNumbers) {
                $otherCount = 1;
                for ($lineIndex; $lineIndex < $totalNumbers; $lineIndex++) {
                    $line = $txtContent[$lineIndex];
                    $phone = $this->extractPhoneNumber($line);

                    if (!empty($phone)) {
                        $name = "Others " . str_pad($otherCount, 3, '0', STR_PAD_LEFT);
                        $vcfContent .= $this->generateVCardEntry($name, $phone);
                        $otherCount++;
                    }
                }
            }

            // Jika tidak ada konten VCF yang dihasilkan
            if (empty($vcfContent)) {
                return back()->withErrors([
                    'file' => 'Tidak ada nomor telepon yang valid ditemukan dalam file'
                ])->withInput();
            }

            $fileName = $filename . ".vcf";

            return response($vcfContent)
                ->header('Content-Type', 'text/x-vcard; charset=utf-8')
                ->header('Content-Disposition', "attachment; filename={$fileName}")
                ->header('Cache-Control', 'no-cache, must-revalidate')
                ->header('Expires', 'Sat, 26 Jul 1997 05:00:00 GMT');

        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Terjadi kesalahan saat memproses file: ' . $e->getMessage()
            ])->withInput();
        }
    }

    /**
     * Extract phone number from a line of text
     * Supports various formats like "Name: +62812345678" or just "+62812345678"
     */
    private function extractPhoneNumber($line)
    {
        $line = trim($line);

        // Jika ada format "Nama: Nomor"
        if (strpos($line, ':') !== false) {
            $parts = explode(':', $line, 2);
            $phone = trim($parts[1]);
        } else {
            $phone = $line;
        }

        // Bersihkan nomor telepon - hapus karakter selain angka, +, -, (), dan spasi
        $phone = preg_replace('/[^\d\+\-\(\)\s]/', '', $phone);

        // Hapus spasi berlebihan
        $phone = preg_replace('/\s+/', ' ', trim($phone));

        return $phone;
    }

    /**
     * Generate single vCard entry
     */
    private function generateVCardEntry($name, $phone)
    {
        $vcard = "BEGIN:VCARD\n";
        $vcard .= "VERSION:3.0\n";
        $vcard .= "FN:{$name}\n";
        $vcard .= "N:{$name};;;;\n";
        $vcard .= "TEL;TYPE=CELL:{$phone}\n";
        $vcard .= "END:VCARD\n";

        return $vcard;
    }

    /**
     * Method untuk converter biasa (method lama Anda yang sudah ada)
     */
    public function convertnomer(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:txt',
            'prefix' => 'required|string',
            'filename' => 'required|string',
        ]);

        $prefix = $request->input('prefix');
        $filename = preg_replace('/[^A-Za-z0-9_\-]/', '_', $request->input('filename'));

        $txtContent = file($request->file('file')->getRealPath(), FILE_IGNORE_NEW_LINES);

        $vcfContent = "";
        $counter = 1;
        foreach ($txtContent as $line) {
            if (strpos($line, ':') !== false) {
                $parts = explode(':', $line, 2);
                $phone = trim($parts[1]);
            } else {
                $phone = trim($line);
            }

            $name = $prefix . " " . str_pad($counter, 3, '0', STR_PAD_LEFT);

            $vcfContent .= "BEGIN:VCARD\n";
            $vcfContent .= "VERSION:3.0\n";
            $vcfContent .= "FN:{$name}\n";
            $vcfContent .= "TEL;TYPE=CELL:{$phone}\n";
            $vcfContent .= "END:VCARD\n";

            $counter++;
        }

        $fileName = $filename . ".vcf";

        return response($vcfContent)
            ->header('Content-Type', 'text/x-vcard')
            ->header('Content-Disposition', "attachment; filename={$fileName}");
    }

    /**
 * Tampilkan halaman form bagi nomor menjadi beberapa file
 */
public function nomerbagan()
{
    return view('convert.nomerbagan');
}

/**
 * Proses pembagian nomor menjadi beberapa file VCF
 * Otomatis membagi nomor secara merata ke sejumlah file yang ditentukan
 */
public function convertnomerbagan(Request $request)
{
    // Validasi input
    $validator = Validator::make($request->all(), [
        'file' => 'required|mimes:txt|max:2048',
        'prefix' => 'required|string|max:50',
        'file_count' => 'required|integer|min:1|max:50',
        'base_filename' => 'required|string|max:100',
    ]);

    if ($validator->fails()) {
        return back()->withErrors($validator)->withInput();
    }

    try {
        // Ambil parameter dari form
        $prefix = $request->input('prefix');
        $fileCount = (int) $request->input('file_count');
        $baseFilename = preg_replace('/[^A-Za-z0-9_\-]/', '_', $request->input('base_filename'));

        // Baca isi file TXT
        $txtContent = file($request->file('file')->getRealPath(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        // Filter dan bersihkan nomor
        $phoneNumbers = [];
        foreach ($txtContent as $line) {
            $line = trim($line);
            if (!empty($line)) {
                $phone = $this->extractPhoneNumber($line);
                if (!empty($phone)) {
                    $phoneNumbers[] = $phone;
                }
            }
        }

        $totalNumbers = count($phoneNumbers);

        if ($totalNumbers === 0) {
            return back()->withErrors([
                'file' => 'Tidak ada nomor telepon yang valid ditemukan dalam file'
            ])->withInput();
        }

        // Validasi minimal jumlah nomor
        if ($totalNumbers < $fileCount) {
            return back()->withErrors([
                'file' => "File hanya berisi {$totalNumbers} nomor. Minimal harus ada {$fileCount} nomor untuk dibagi ke {$fileCount} file (minimal 1 nomor per file)."
            ])->withInput();
        }

        // Hitung pembagian otomatis
        $numbersPerFile = floor($totalNumbers / $fileCount);
        $remainder = $totalNumbers % $fileCount;

        // Siapkan ZIP archive
        $zipFileName = $baseFilename . '_' . date('YmdHis') . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);

        // Pastikan direktori temp ada
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $zip = new \ZipArchive();

        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return back()->withErrors([
                'error' => 'Gagal membuat file ZIP'
            ])->withInput();
        }

        $phoneIndex = 0;
        $globalCounter = 1;

        // Generate file VCF untuk setiap grup
        for ($fileNum = 1; $fileNum <= $fileCount; $fileNum++) {
            $vcfContent = "";

            // Hitung jumlah nomor untuk file ini
            // File pertama sampai sisa (remainder) mendapat 1 nomor ekstra
            $currentFileNumbers = $numbersPerFile;
            if ($fileNum <= $remainder) {
                $currentFileNumbers++;
            }

            // Generate kontak untuk file ini
            for ($i = 0; $i < $currentFileNumbers && $phoneIndex < $totalNumbers; $i++) {
                $phone = $phoneNumbers[$phoneIndex];
                $name = $prefix . " " . str_pad($globalCounter, 3, '0', STR_PAD_LEFT);

                $vcfContent .= $this->generateVCardEntry($name, $phone);

                $phoneIndex++;
                $globalCounter++;
            }

            // Tambahkan file ke ZIP
            $vcfFileName = $baseFilename . "_part" . $fileNum . ".vcf";
            $zip->addFromString($vcfFileName, $vcfContent);
        }

        $zip->close();

        // Download ZIP file
        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);

    } catch (\Exception $e) {
        return back()->withErrors([
            'error' => 'Terjadi kesalahan saat memproses file: ' . $e->getMessage()
        ])->withInput();
    }
}
}
