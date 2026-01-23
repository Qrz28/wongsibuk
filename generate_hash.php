<?php
/**
 * Password Hash Generator
 * Gunakan tool ini untuk generate password hash bcrypt
 * Jalankan di browser: http://localhost/fishing%20log/generate_hash.php
 */

$page_title = 'Password Hash Generator';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    $password = $_POST['password'];
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $success = true;
} else {
    $success = false;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">🔐 <?php echo $page_title; ?></h4>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-4">Generator password hash bcrypt untuk Fishing Log</p>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="password" class="form-label">Masukkan Password</label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="password" 
                                    name="password" 
                                    placeholder="Contoh: password123"
                                    required
                                >
                                <small class="text-muted d-block mt-2">
                                    Password akan di-hash menggunakan algoritma bcrypt
                                </small>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">
                                Generate Hash
                            </button>
                        </form>
                        
                        <?php if ($success): ?>
                        <div class="mt-4">
                            <div class="alert alert-success">
                                <h5 class="alert-heading">✅ Hash Generated!</h5>
                                <hr>
                                <p><strong>Password:</strong> <code><?php echo htmlspecialchars($password); ?></code></p>
                                <p><strong>Hash:</strong></p>
                                <div class="bg-light p-3 rounded">
                                    <code style="word-break: break-all; font-size: 12px;">
                                        <?php echo htmlspecialchars($hash); ?>
                                    </code>
                                </div>
                                <hr>
                                <button class="btn btn-sm btn-outline-success" onclick="copyToClipboard()">
                                    📋 Copy Hash
                                </button>
                            </div>
                            
                            <div class="alert alert-info">
                                <strong>📝 Cara Pakai:</strong>
                                <ol class="mb-0 mt-2">
                                    <li>Copy hash di atas</li>
                                    <li>Buka phpMyAdmin → Database fishinglog → Tabel pengguna</li>
                                    <li>Edit baris user yang ingin diganti passwordnya</li>
                                    <li>Paste hash ke kolom password</li>
                                    <li>Save perubahan</li>
                                    <li>Gunakan password asli untuk login</li>
                                </ol>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="mt-4 text-center text-white">
                    <small>
                        Untuk testing, gunakan:<br>
                        Email: <code>admin@example.com</code><br>
                        Password: <code>password</code>
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function copyToClipboard() {
            // Find hash code
            const hashCode = document.querySelector('code').textContent;
            
            // Copy to clipboard
            navigator.clipboard.writeText(hashCode).then(function() {
                alert('Hash berhasil dicopy!');
            }).catch(function(err) {
                alert('Gagal copy hash');
            });
        }
    </script>
</body>
</html>
