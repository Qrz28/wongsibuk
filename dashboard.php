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
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .hero-section {
            background: linear-gradient(135deg, rgba(44, 62, 80, 0.95) 0%, rgba(52, 73, 94, 0.95) 100%),
                        url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="dashboard" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse"><rect x="2" y="2" width="16" height="16" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1"/><circle cx="6" cy="6" r="1.5" fill="rgba(255,255,255,0.08)"/><circle cx="14" cy="14" r="1.5" fill="rgba(255,255,255,0.06)"/><line x1="6" y1="6" x2="14" y2="14" stroke="rgba(255,255,255,0.05)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23dashboard)"/></svg>');
            padding: 60px 0;
            margin-bottom: 40px;
            border-radius: 0 0 50px 50px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .hero-content {
            text-align: center;
            color: white;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 15px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .hero-subtitle {
            font-size: 1.3rem;
            margin-bottom: 30px;
            opacity: 0.9;
        }

        .hero-user-info {
            display: flex;
            flex-direction: column;
            gap: 10px;
            align-items: center;
        }

        .user-detail {
            display: flex;
            align-items: center;
            font-size: 1rem;
            opacity: 0.9;
            background: rgba(255, 255, 255, 0.1);
            padding: 8px 16px;
            border-radius: 20px;
            backdrop-filter: blur(10px);
        }

        .user-detail i {
            margin-right: 8px;
            opacity: 0.8;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 24px;
            color: #667eea !important;
        }

        .nav-link {
            font-weight: 500;
            color: #555 !important;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            color: #667eea !important;
            transform: translateY(-1px);
        }

        .nav-link.active {
            color: #667eea !important;
            font-weight: 600;
        }

        .main-container {
            background: white;
            border-radius: 20px;
            margin: -20px 20px 40px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .welcome-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 50px;
            color: white;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
            position: relative;
            overflow: hidden;
        }

        .welcome-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translate(-50%, -50%) rotate(0deg); }
            50% { transform: translate(-50%, -50%) rotate(180deg); }
        }

        .welcome-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .welcome-title i {
            font-size: 3rem;
            opacity: 0.9;
        }

        .welcome-info {
            font-size: 1.1rem;
            opacity: 0.9;
            margin: 5px 0;
        }

        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .feature-card {
            background: white;
            border-radius: 20px;
            padding: 35px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: none;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            text-decoration: none;
            color: inherit;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }

        .feature-card.perjalanan::before { background: linear-gradient(90deg, #667eea, #764ba2); }
        .feature-card.tangkapan::before { background: linear-gradient(90deg, #28a745, #20c997); }
        .feature-card.spot::before { background: linear-gradient(90deg, #2196F3, #21CBF3); }
        .feature-card.laporan::before { background: linear-gradient(90deg, #4CAF50, #45a049); }

        .card-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.8;
            transition: all 0.3s ease;
        }

        .feature-card:hover .card-icon {
            transform: scale(1.1);
            opacity: 1;
        }

        .card-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: #333;
        }

        .card-description {
            color: #666;
            font-size: 1rem;
            margin-bottom: 25px;
            line-height: 1.5;
        }

        .btn-feature {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 25px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 14px;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-feature:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            color: white;
            text-decoration: none;
        }

        .stats-overview {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 30px;
            margin-top: 40px;
        }

        .stats-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .stats-title i {
            color: #667eea;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .stat-item {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            text-align: center;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #666;
            font-size: 14px;
            font-weight: 500;
        }

        .modal-content {
            border-radius: 20px;
            border: none;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 20px 20px 0 0;
            border: none;
            padding: 25px;
        }

        .modal-title {
            font-weight: 600;
            font-size: 20px;
        }

        .alert {
            border-radius: 15px;
            border: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .welcome-title {
                font-size: 2rem;
            }

            .main-container {
                margin: -10px 10px 20px;
                padding: 20px;
            }

            .dashboard-cards {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .feature-card {
                padding: 25px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">Fishing Log</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="perjalanan.html">Perjalanan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="tangkapan.html">Tangkapan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="spot_memancing.html">Spot Memancing</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="laporan.html">Laporan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title"><i class="fas fa-tachometer-alt"></i> Selamat Datang, <?php echo htmlspecialchars($user_name); ?>!</h1>
                <p class="hero-subtitle">Kelola dan pantau aktivitas memancing Anda dengan mudah</p>
                <div class="hero-user-info">
                    <div class="user-detail">
                        <i class="fas fa-envelope me-2"></i><?php echo htmlspecialchars($user_email); ?>
                    </div>
                    <div class="user-detail">
                        <i class="fas fa-clock me-2"></i>Login: <?php echo date('d F Y H:i', strtotime($_SESSION['login_time'])); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-container">

        <!-- Dashboard Cards -->
        <div class="dashboard-cards">
            <a href="perjalanan.html" class="feature-card">
                <div class="card-icon">🚢</div>
                <h3 class="card-title">Perjalanan</h3>
                <p class="card-description">Kelola perjalanan memancing Anda</p>
                <span class="btn-feature">Kelola Perjalanan</span>
            </a>

            <a href="tangkapan.html" class="feature-card">
                <div class="card-icon">🐟</div>
                <h3 class="card-title">Tangkapan</h3>
                <p class="card-description">Catat hasil tangkapan Anda</p>
                <span class="btn-feature">Kelola Tangkapan</span>
            </a>

            <a href="spot_memancing.html" class="feature-card">
                <div class="card-icon">📍</div>
                <h3 class="card-title">Spot Memancing</h3>
                <p class="card-description">Temukan spot terbaik</p>
                <span class="btn-feature">Jelajahi Spot</span>
            </a>

            <a href="laporan.html" class="feature-card">
                <div class="card-icon">📊</div>
                <h3 class="card-title">Laporan</h3>
                <p class="card-description">Lihat laporan statistik</p>
                <span class="btn-feature">Lihat Laporan</span>
            </a>
        </div>

        <!-- Quick Stats Overview -->
        <div class="stats-overview">
            <h3 class="stats-title">Ringkasan Aktivitas</h3>
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number" id="totalTrips">0</div>
                    <div class="stat-label">Total Perjalanan</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" id="totalCatches">0</div>
                    <div class="stat-label">Total Tangkapan</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" id="totalSpots">0</div>
                    <div class="stat-label">Total Spot</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" id="activeDays">0</div>
                    <div class="stat-label">Hari Aktif</div>
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
