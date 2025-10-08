<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nomor to VCF Converter</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 700px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .header h1 {
            color: #333;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .header p {
            color: #666;
            font-size: 16px;
            font-weight: 400;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }

        .form-group input[type="text"], .form-group input[type="number"], .form-group textarea {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e1e5e9;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8f9fa;
            font-family: inherit;
        }

        .form-group textarea {
            min-height: 150px;
            resize: vertical;
            line-height: 1.5;
        }

        .form-group input:focus, .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }

        .group-config {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05), rgba(118, 75, 162, 0.05));
            border: 2px solid rgba(102, 126, 234, 0.2);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .group-config h3 {
            color: #667eea;
            font-size: 18px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }

        .group-row {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 15px;
            align-items: end;
        }

        .submit-btn {
            width: 100%;
            padding: 16px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4);
        }

        .submit-btn:active {
            transform: translateY(-1px);
        }

        .submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .icon {
            width: 24px;
            height: 24px;
            display: inline-block;
            margin-right: 10px;
        }

        .info-box {
            background: linear-gradient(135deg, rgba(52, 152, 219, 0.1), rgba(155, 89, 182, 0.1));
            border: 1px solid rgba(52, 152, 219, 0.3);
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 25px;
        }

        .info-box p {
            color: #2c5aa0;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .info-box ul {
            color: #2c5aa0;
            font-size: 14px;
            margin-left: 20px;
        }

        .preview-section {
            background: linear-gradient(135deg, rgba(46, 204, 113, 0.1), rgba(52, 152, 219, 0.1));
            border: 1px solid rgba(46, 204, 113, 0.3);
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 25px;
            display: none;
        }

        .preview-section h4 {
            color: #27ae60;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }

        .preview-content {
            background: rgba(255, 255, 255, 0.8);
            border-radius: 8px;
            padding: 10px;
            font-family: monospace;
            font-size: 14px;
            color: #333;
            max-height: 200px;
            overflow-y: auto;
        }

        .number-counter {
            background: rgba(102, 126, 234, 0.1);
            border-radius: 8px;
            padding: 10px;
            margin-top: 10px;
            font-size: 14px;
            color: #667eea;
            font-weight: 600;
        }

        /* Loading animation */
        .loading {
            display: none;
            text-align: center;
            margin-top: 20px;
        }

        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Responsive design */
        @media (max-width: 768px) {
            body {
                padding: 15px;
            }

            .container {
                padding: 30px 25px;
                border-radius: 15px;
            }

            .group-row {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .header h1 {
                font-size: 24px;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 25px 20px;
            }

            .header h1 {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📱 Nomor to VCF Converter</h1>
            <p>Konversi daftar nomor telepon menjadi file VCF dengan grouping otomatis</p>
        </div>

        <div class="info-box">
            <p><strong>📋 Cara Penggunaan:</strong></p>
            <ul>
                <li>Masukkan nomor telepon (satu nomor per baris)</li>
                <li>Atur berapa nomor untuk Admin dan Navy</li>
                <li>Nomor akan otomatis diberi nama sesuai urutan</li>
                <li>Contoh: Admin 001, Admin 002, Navy 001, Navy 002, dst</li>
            </ul>
        </div>

        <form id="converterForm">
            <div class="form-group">
                <label for="numbers">
                    <span class="icon">📞</span>
                    Daftar Nomor Telepon (satu nomor per baris):
                </label>
                <textarea name="numbers" id="numbers" required placeholder="Contoh:
+6281234567890
08123456789
+6285678901234
08567890123
..."></textarea>
                <div class="number-counter" id="numberCounter">
                    <span id="totalNumbers">0</span> nomor terdeteksi
                </div>
            </div>

            <div class="group-config">
                <h3><span class="icon">👥</span>Konfigurasi Group</h3>
                <div class="group-row">
                    <div class="form-group">
                        <label>Nama Group Admin:</label>
                        <input type="text" name="admin_prefix" id="adminPrefix" value="Admin" required>
                    </div>
                    <div class="form-group">
                        <label>Jumlah Admin:</label>
                        <input type="number" name="admin_count" id="adminCount" value="3" min="0" max="50" required>
                    </div>
                </div>
                <div class="group-row">
                    <div class="form-group">
                        <label>Nama Group Navy:</label>
                        <input type="text" name="navy_prefix" id="navyPrefix" value="Navy" required>
                    </div>
                    <div class="form-group">
                        <label>Jumlah Navy:</label>
                        <input type="number" name="navy_count" id="navyCount" value="10" min="0" max="100" required>
                    </div>
                </div>
            </div>

            <div class="preview-section" id="previewSection">
                <h4><span class="icon">👁️</span>Preview Nama Kontak</h4>
                <div class="preview-content" id="previewContent"></div>
            </div>

            <div class="form-group">
                <label for="filename">
                    <span class="icon">📄</span>
                    Nama File Output (tanpa .vcf):
                </label>
                <input type="text" name="filename" id="filename" value="contacts_combined" required placeholder="Masukkan nama file output">
            </div>

            <button type="submit" class="submit-btn" id="submitBtn">
                <span class="icon">🚀</span>
                Convert ke VCF
            </button>

            <div class="loading" id="loading">
                <div class="spinner"></div>
                <p style="margin-top: 10px; color: #667eea;">Sedang memproses...</p>
            </div>
        </form>
    </div>

    <script>
        let phoneNumbers = [];

        // Parse nomor telepon dari textarea
        function parsePhoneNumbers() {
            const numbersText = document.getElementById('numbers').value;
            const lines = numbersText.split('\n');

            phoneNumbers = lines
                .map(line => line.trim())
                .filter(line => line.length > 0)
                .filter(line => /[\d+]/.test(line)); // Harus mengandung angka atau +

            document.getElementById('totalNumbers').textContent = phoneNumbers.length;

            updatePreview();
        }

        // Update preview nama kontak
        function updatePreview() {
            const adminPrefix = document.getElementById('adminPrefix').value;
            const adminCount = parseInt(document.getElementById('adminCount').value) || 0;
            const navyPrefix = document.getElementById('navyPrefix').value;
            const navyCount = parseInt(document.getElementById('navyCount').value) || 0;

            let preview = '';
            let index = 0;

            // Preview Admin
            if (adminCount > 0 && index < phoneNumbers.length) {
                preview += '<strong>👔 Admin:</strong><br>';
                for (let i = 1; i <= adminCount && index < phoneNumbers.length; i++) {
                    const name = adminPrefix + " " + String(i).padStart(3, '0');
                    preview += `${name} → ${phoneNumbers[index]}<br>`;
                    index++;
                }
                preview += '<br>';
            }

            // Preview Navy
            if (navyCount > 0 && index < phoneNumbers.length) {
                preview += '<strong>⚓ Navy:</strong><br>';
                for (let i = 1; i <= navyCount && index < phoneNumbers.length; i++) {
                    const name = navyPrefix + " " + String(i).padStart(3, '0');
                    preview += `${name} → ${phoneNumbers[index]}<br>`;
                    index++;
                }
                preview += '<br>';
            }

            // Preview Others (sisa nomor)
            if (index < phoneNumbers.length) {
                preview += '<strong>📋 Others:</strong><br>';
                let otherCount = 1;
                while (index < phoneNumbers.length) {
                    const name = "Others " + String(otherCount).padStart(3, '0');
                    preview += `${name} → ${phoneNumbers[index]}<br>`;
                    index++;
                    otherCount++;
                }
            }

            const previewSection = document.getElementById('previewSection');
            const previewContent = document.getElementById('previewContent');

            if (preview && phoneNumbers.length > 0) {
                previewContent.innerHTML = preview;
                previewSection.style.display = 'block';
            } else {
                previewSection.style.display = 'none';
            }
        }

        // Generate VCF content
        function generateVCF() {
            const adminPrefix = document.getElementById('adminPrefix').value;
            const adminCount = parseInt(document.getElementById('adminCount').value) || 0;
            const navyPrefix = document.getElementById('navyPrefix').value;
            const navyCount = parseInt(document.getElementById('navyCount').value) || 0;

            let vcfContent = '';
            let index = 0;

            // Generate Admin contacts
            for (let i = 1; i <= adminCount && index < phoneNumbers.length; i++) {
                const name = adminPrefix + " " + String(i).padStart(3, '0');
                const phone = phoneNumbers[index];
                vcfContent += generateVCardEntry(name, phone);
                index++;
            }

            // Generate Navy contacts
            for (let i = 1; i <= navyCount && index < phoneNumbers.length; i++) {
                const name = navyPrefix + " " + String(i).padStart(3, '0');
                const phone = phoneNumbers[index];
                vcfContent += generateVCardEntry(name, phone);
                index++;
            }

            // Generate Others contacts (remaining numbers)
            let otherCount = 1;
            while (index < phoneNumbers.length) {
                const name = "Others " + String(otherCount).padStart(3, '0');
                const phone = phoneNumbers[index];
                vcfContent += generateVCardEntry(name, phone);
                index++;
                otherCount++;
            }

            return vcfContent;
        }

        // Generate single vCard entry
        function generateVCardEntry(name, phone) {
            return `BEGIN:VCARD
VERSION:3.0
FN:${name}
N:${name};;;;
TEL;TYPE=CELL:${phone}
END:VCARD
`;
        }

        // Download VCF file
        function downloadVCF(content, filename) {
            const blob = new Blob([content], { type: 'text/x-vcard;charset=utf-8' });
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = filename + '.vcf';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(url);
        }

        // Event listeners
        document.getElementById('numbers').addEventListener('input', parsePhoneNumbers);
        document.getElementById('adminPrefix').addEventListener('input', updatePreview);
        document.getElementById('adminCount').addEventListener('input', updatePreview);
        document.getElementById('navyPrefix').addEventListener('input', updatePreview);
        document.getElementById('navyCount').addEventListener('input', updatePreview);

        // Form submission
        document.getElementById('converterForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const numbersInput = document.getElementById('numbers').value.trim();
            const filename = document.getElementById('filename').value.trim();

            if (!numbersInput) {
                alert('Silakan masukkan nomor telepon');
                return;
            }

            if (!filename) {
                alert('Silakan masukkan nama file');
                return;
            }

            if (phoneNumbers.length === 0) {
                alert('Tidak ada nomor telepon yang valid ditemukan');
                return;
            }

            // Show loading
            document.getElementById('loading').style.display = 'block';
            document.getElementById('submitBtn').style.display = 'none';

            // Simulate processing delay
            setTimeout(() => {
                try {
                    const vcfContent = generateVCF();
                    downloadVCF(vcfContent, filename);

                    // Hide loading
                    document.getElementById('loading').style.display = 'none';
                    document.getElementById('submitBtn').style.display = 'block';

                    alert(`Berhasil! File ${filename}.vcf telah diunduh dengan ${phoneNumbers.length} kontak.`);
                } catch (error) {
                    alert('Terjadi kesalahan saat membuat file VCF: ' + error.message);
                    document.getElementById('loading').style.display = 'none';
                    document.getElementById('submitBtn').style.display = 'block';
                }
            }, 1000);
        });

        // Input animations
        document.querySelectorAll('input[type="text"], input[type="number"], textarea').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'translateY(-2px)';
            });

            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'translateY(0)';
            });
        });

        // Initialize
        parsePhoneNumbers();
    </script>
</body>
</html>
