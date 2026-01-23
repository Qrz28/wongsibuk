<?php
/**
 * Dashboard
 * Fishing Log Application
 */

session_start();

// Check if user is logged in
if (!isset($_SESSION['id_pengguna'])) {
    header('Location: login.html');
    exit();
}

$user_id = $_SESSION['id_pengguna'];
$user_name = $_SESSION['nama'];
$user_email = $_SESSION['email'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Fishing Log</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f8f9fa;
        }
        
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .navbar-brand {
            font-weight: 600;
            font-size: 20px;
        }
        
        .sidebar {
            background: white;
            border-right: 1px solid #e0e0e0;
            min-height: 100vh;
        }
        
        .sidebar .nav-link {
            color: #333;
            padding: 12px 20px;
            border-left: 3px solid transparent;
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: #f0f0f0;
            border-left-color: #667eea;
            color: #667eea;
        }
        
        .main-content {
            padding: 30px;
        }
        
        .welcome-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.3);
        }
        
        .welcome-card h2 {
            margin-bottom: 10px;
        }
        
        .welcome-card p {
            margin: 5px 0;
            opacity: 0.9;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }
        
        .card-icon {
            font-size: 40px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">🎣 Fishing Log</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <?php echo htmlspecialchars($user_name); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#profileModal">Profil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" id="logoutBtn">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <nav class="nav flex-column">
                    <a class="nav-link active" href="#dashboard">Dashboard</a>
                    <a class="nav-link" href="#perjalanan">Perjalanan</a>
                    <a class="nav-link" href="#tangkapan">Tangkapan</a>
                    <a class="nav-link" href="#spot">Spot Memancing</a>
                    <a class="nav-link" href="#laporan">Laporan</a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <!-- Welcome Section -->
                <div class="welcome-card">
                    <h2>Selamat datang, <?php echo htmlspecialchars($user_name); ?>! 👋</h2>
                    <p>Email: <?php echo htmlspecialchars($user_email); ?></p>
                    <p>Login pada: <?php echo date('d F Y H:i:s', strtotime($_SESSION['login_time'])); ?></p>
                </div>

                <!-- Dashboard Cards -->
                <div class="row">
                    <div class="col-md-3 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-icon">🎣</div>
                                <h5 class="card-title">Perjalanan</h5>
                                <p class="card-text text-muted">Kelola perjalanan memancing Anda</p>
                                <a href="#" class="btn btn-sm btn-primary">Lihat</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-icon">🐟</div>
                                <h5 class="card-title">Tangkapan</h5>
                                <p class="card-text text-muted">Catat hasil tangkapan Anda</p>
                                <a href="#" class="btn btn-sm btn-primary">Lihat</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-icon">📍</div>
                                <h5 class="card-title">Spot Memancing</h5>
                                <p class="card-text text-muted">Temukan spot terbaik</p>
                                <a href="#" class="btn btn-sm btn-primary">Lihat</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-icon">📊</div>
                                <h5 class="card-title">Laporan</h5>
                                <p class="card-text text-muted">Lihat laporan statistik</p>
                                <a href="#" class="btn btn-sm btn-primary">Lihat</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Modal -->
    <div class="modal fade" id="profileModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Profil Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Nama:</strong> <?php echo htmlspecialchars($user_name); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user_email); ?></p>
                    <p><strong>ID Pengguna:</strong> <?php echo htmlspecialchars($user_id); ?></p>
                    <p><strong>Login Terakhir:</strong> <?php echo date('d F Y H:i:s', strtotime($_SESSION['login_time'])); ?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Handle logout
            $('#logoutBtn').on('click', function(e) {
                e.preventDefault();
                
                if (confirm('Apakah Anda yakin ingin logout?')) {
                    $.ajax({
                        type: 'POST',
                        url: 'logout.php',
                        success: function(response) {
                            if (response.success) {
                                window.location.href = response.redirect;
                            }
                        },
                        error: function() {
                            alert('Logout gagal!');
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
