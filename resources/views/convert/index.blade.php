<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TXT to VCF Converter</title>
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
            max-width: 500px;
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

        .form-group input[type="text"] {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e1e5e9;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-group input[type="text"]:focus {
            outline: none;
            border-color: #667eea;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }

        .file-upload {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .file-upload input[type="file"] {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-upload-label {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            border: 2px dashed #667eea;
            border-radius: 12px;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05), rgba(118, 75, 162, 0.05));
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }

        .file-upload-label:hover {
            border-color: #764ba2;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
            transform: translateY(-2px);
        }

        .file-upload-label i {
            font-size: 24px;
            margin-right: 10px;
            color: #667eea;
        }

        .file-upload-text {
            color: #667eea;
            font-weight: 600;
        }

        .file-name {
            margin-top: 10px;
            padding: 10px;
            background: #e8f4f8;
            border-radius: 8px;
            color: #2c5aa0;
            font-size: 14px;
            display: none;
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

        /* Icon styles */
        .icon {
            width: 24px;
            height: 24px;
            display: inline-block;
            margin-right: 10px;
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

            .header h1 {
                font-size: 24px;
            }

            .header p {
                font-size: 14px;
            }

            .form-group input[type="text"] {
                padding: 12px 15px;
                font-size: 14px;
            }

            .file-upload-label {
                padding: 15px;
            }

            .submit-btn {
                padding: 14px 20px;
                font-size: 16px;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 25px 20px;
            }

            .header h1 {
                font-size: 22px;
            }

            .file-upload-label {
                flex-direction: column;
                gap: 10px;
            }

            .icon {
                margin-right: 0;
                margin-bottom: 5px;
            }
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔄 TXT to VCF Converter</h1>
            <p>Konversi file TXT menjadi format VCF dengan mudah</p>
        </div>

        <form action="{{ route('convert.process') }}" method="POST" enctype="multipart/form-data" id="converterForm">
            @csrf

            <div class="form-group">
                <label for="prefix">
                    <span class="icon">📝</span>
                    Nama Prefix (contoh: Stecu):
                </label>
                <input type="text" name="prefix" id="prefix" required placeholder="Masukkan prefix nama">
            </div>

            <div class="form-group">
                <label for="filename">
                    <span class="icon">📄</span>
                    Nama File Output (tanpa .vcf):
                </label>
                <input type="text" name="filename" id="filename" required placeholder="Masukkan nama file output">
            </div>

            <div class="form-group">
                <label>
                    <span class="icon">📁</span>
                    Upload File TXT:
                </label>
                <div class="file-upload">
                    <input type="file" name="file" id="file" required accept=".txt">
                    <label for="file" class="file-upload-label">
                        <span class="icon">⬆️</span>
                        <span class="file-upload-text">Klik untuk memilih file TXT</span>
                    </label>
                    <div class="file-name" id="fileName"></div>
                </div>
            </div>

            <button type="submit" class="submit-btn">
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
        // File upload handling
        document.getElementById('file').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            const fileNameDiv = document.getElementById('fileName');

            if (fileName) {
                fileNameDiv.textContent = `File dipilih: ${fileName}`;
                fileNameDiv.style.display = 'block';
            } else {
                fileNameDiv.style.display = 'none';
            }
        });

        // Form submission handling
        document.getElementById('converterForm').addEventListener('submit', function(e) {
            document.getElementById('loading').style.display = 'block';
            document.querySelector('.submit-btn').style.display = 'none';
        });

        // Input animations
        document.querySelectorAll('input[type="text"]').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'translateY(-2px)';
            });

            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'translateY(0)';
            });
        });
    </script>
</body>
</html>
