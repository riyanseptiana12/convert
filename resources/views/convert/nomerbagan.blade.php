<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bagi Nomor ke Beberapa File VCF</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('convert.index') }}" class="text-xl font-bold text-blue-600">
                        VCF Converter
                    </a>
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('convert.index') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                        Home
                    </a>
                    <a href="{{ route('convert.nomer') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                        Multi Group
                    </a>
                    <a href="{{ route('convert.nomerbagan') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                        Bagi File
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="py-8">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Bagi Nomor ke Beberapa File VCF</h1>
                    <p class="text-gray-600">Upload file TXT dan tentukan mau dibagi jadi berapa file VCF</p>
                </div>

                <!-- Alert Error -->
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        <div class="font-bold mb-2">Terjadi kesalahan:</div>
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Example Box -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500 p-6 rounded-lg mb-6 shadow-sm">
                    <div class="flex items-start">
                        <div class="text-3xl mr-4">💡</div>
                        <div>
                            <h3 class="font-bold text-blue-900 mb-2">Contoh Penggunaan:</h3>
                            <p class="text-blue-800 mb-2">Anda punya file TXT dengan <strong>500 nomor</strong>, ingin dibagi jadi <strong>4 file VCF</strong></p>
                            <p class="text-blue-700 text-sm">
                                <strong>Hasil:</strong><br>
                                • File 1: 125 kontak (Customer 001 - 125)<br>
                                • File 2: 125 kontak (Customer 126 - 250)<br>
                                • File 3: 125 kontak (Customer 251 - 375)<br>
                                • File 4: 125 kontak (Customer 376 - 500)<br>
                                <span class="text-xs text-blue-600 mt-1 block">*Sistem akan membagi secara otomatis dan merata</span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Form Card -->
                <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                        <h2 class="text-xl font-semibold text-white">Form Input</h2>
                    </div>

                    <form action="{{ route('convert.convertnomerbagan') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                        @csrf

                        <!-- Upload File -->
                        <div>
                            <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                                Upload File TXT <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="file"
                                name="file"
                                id="file"
                                accept=".txt"
                                required
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-gray-300 rounded-lg cursor-pointer"
                            >
                            <p class="mt-2 text-sm text-gray-500">Format: satu nomor per baris (contoh: +628123456789)</p>
                        </div>

                        <!-- Prefix -->
                        <div>
                            <label for="prefix" class="block text-sm font-medium text-gray-700 mb-2">
                                Prefix Nama Kontak <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="prefix"
                                id="prefix"
                                value="{{ old('prefix', 'Customer') }}"
                                placeholder="Contoh: Customer"
                                required
                                maxlength="50"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                            <p class="mt-2 text-sm text-gray-500">Nama kontak akan menjadi: "Customer 001", "Customer 002", dst (berurutan di semua file)</p>
                        </div>

                        <!-- Jumlah File -->
                        <div>
                            <label for="file_count" class="block text-sm font-medium text-gray-700 mb-2">
                                Jumlah File yang Diinginkan <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="number"
                                name="file_count"
                                id="file_count"
                                value="{{ old('file_count', 4) }}"
                                min="1"
                                max="50"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-lg font-semibold"
                            >
                            <p class="mt-2 text-sm text-gray-500">Semua nomor akan dibagi secara merata ke jumlah file ini (Maks: 50 file)</p>
                        </div>

                        <!-- Nama File -->
                        <div>
                            <label for="base_filename" class="block text-sm font-medium text-gray-700 mb-2">
                                Nama File Dasar <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="base_filename"
                                id="base_filename"
                                value="{{ old('base_filename', 'contacts') }}"
                                placeholder="Contoh: contacts"
                                required
                                maxlength="100"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                            <p class="mt-2 text-sm text-gray-500">File hasil: contacts_part1.vcf, contacts_part2.vcf, dst. (dalam ZIP)</p>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-between pt-4 border-t">
                            <a href="{{ route('convert.index') }}" class="text-gray-600 hover:text-gray-800 text-sm font-medium">
                                ← Kembali
                            </a>
                            <button
                                type="submit"
                                class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-md hover:shadow-lg"
                            >
                                🚀 Bagi & Download ZIP
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Info Section -->
                <div class="mt-8 bg-white rounded-lg p-6 shadow-md">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">📋 Cara Kerja Sistem:</h3>
                    <div class="space-y-4 text-gray-700">
                        <div class="flex items-start">
                            <span class="text-2xl mr-3">1️⃣</span>
                            <div>
                                <strong>Upload File TXT</strong>
                                <p class="text-sm text-gray-600">Upload file yang berisi daftar nomor (misal: 500 nomor)</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <span class="text-2xl mr-3">2️⃣</span>
                            <div>
                                <strong>Tentukan Jumlah File</strong>
                                <p class="text-sm text-gray-600">Misal: 4 file → Sistem akan bagi 500 nomor menjadi 4 bagian (125 per file)</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <span class="text-2xl mr-3">3️⃣</span>
                            <div>
                                <strong>Download ZIP</strong>
                                <p class="text-sm text-gray-600">Semua file VCF akan dikemas dalam satu file ZIP</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <h4 class="font-semibold text-green-800 mb-2 flex items-center">
                            <span class="text-xl mr-2">✨</span> Keunggulan
                        </h4>
                        <ul class="list-disc list-inside space-y-1 text-sm text-green-700">
                            <li><strong>Otomatis</strong> - Sistem hitung pembagian sendiri, Anda tinggal tentukan mau berapa file</li>
                            <li><strong>Merata</strong> - Nomor dibagi merata ke semua file secara adil</li>
                            <li><strong>Berurutan</strong> - Penomoran kontak tetap berurutan dari file 1 sampai terakhir</li>
                            <li><strong>Praktis</strong> - Semua file langsung dalam bentuk ZIP, tinggal download sekali</li>
                        </ul>
                    </div>

                    <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <h4 class="font-semibold text-yellow-800 mb-2">⚠️ Catatan Penting:</h4>
                        <ul class="list-disc list-inside space-y-1 text-sm text-yellow-700">
                            <li>Jumlah nomor di file TXT harus lebih dari atau sama dengan jumlah file yang diminta</li>
                            <li>Jika ada sisa pembagian, file pertama akan mendapat nomor ekstra</li>
                            <li>Format nomor: +628123456789 atau 08123456789</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="container mx-auto px-4 py-6">
            <p class="text-center text-gray-600 text-sm">
                © {{ date('Y') }} VCF Converter. All rights reserved.
            </p>
        </div>
    </footer>
</body>
</html>
