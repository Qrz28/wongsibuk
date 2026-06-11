<?php
/**
 * Dashboard
 * Fishing Log Application
 */

session_start();

// Check if user is logged in
if (!isset($_SESSION['id_pengguna'])) {
    header('Location: ../index.php');
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
    <link rel="stylesheet" href="../assets/css/style.css?v=1.2">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar-custom">
        <a href="dashboard.php" class="nav-logo">
            <span style="color: var(--accent);">⚓</span> Fishing Log
        </a>
        <button class="nav-toggle" id="navToggle">
            <i class="fas fa-bars"></i>
        </button>
        <ul class="nav-links" id="navLinks">
            <li><a href="dashboard.php" class="nav-link-custom active">Dashboard</a></li>
            <li><a href="perjalanan.html" class="nav-link-custom">Perjalanan</a></li>
            <li><a href="catatan_memancing.html" class="nav-link-custom">Catatan & Data</a></li>
            <li><a href="laporan.html" class="nav-link-custom">Laporan</a></li>
            <li class="mobile-only">
                <hr style="border: none; border-top: 1px solid var(--border); margin: 0.5rem 0;">
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0;">
                    <span id="themeTextMobile">Tema Malam</span>
                    <button class="themeToggleMobile btn-primary" style="background: var(--secondary); padding: 0.5rem; border-radius: 50%; width: 40px; height: 40px; justify-content: center;">
                        <i class="fas fa-moon"></i>
                    </button>
                </div>
            </li>
            <li class="mobile-only"><a href="../logout.php" class="nav-link-custom" style="color: var(--danger);">Logout</a></li>
        </ul>
        <div class="nav-user-info" style="display: flex; gap: 1rem; align-items: center;">
            <button id="themeToggle" class="btn-primary" style="background: var(--secondary); padding: 0.5rem; border-radius: 50%; width: 40px; height: 40px; justify-content: center;">
                <i class="fas fa-moon"></i>
            </button>
            <span style="font-weight: 500; font-size: 0.875rem; color: var(--text-muted);"><?php echo htmlspecialchars($user_name); ?></span>
            <a href="../logout.php" class="btn-primary" style="background: var(--danger); padding: 0.5rem 1rem;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </nav>

    <main class="main-content">
        <header class="dashboard-header animate-fade">
            <div class="welcome-banner">
                <h1 style="font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem;">Selamat Datang, <?php echo explode(' ', htmlspecialchars($user_name))[0]; ?>!</h1>
                <p style="opacity: 0.9; font-size: 1.1rem;">Siap untuk petualangan memancing hari ini? Berikut ringkasan aktivitasmu.</p>
                <div style="margin-top: 1.5rem; display: flex; gap: 1.5rem; font-size: 0.875rem; opacity: 0.8;">
                    <span><i class="far fa-envelope"></i> <?php echo htmlspecialchars($user_email); ?></span>
                    <span><i class="far fa-clock"></i> Login: <?php echo date('d M Y H:i', strtotime($_SESSION['login_time'])); ?></span>
                </div>
            </div>
        </header>

        <section class="stats-container animate-fade" style="animation-delay: 0.1s;">
            <div class="stat-card">
                <div class="stat-label">Total Perjalanan</div>
                <div class="stat-value" id="totalTrips">0</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Tangkapan</div>
                <div class="stat-value" id="totalCatches">0</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Spot</div>
                <div class="stat-value" id="totalSpots">0</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Hari Aktif</div>
                <div class="stat-value" id="activeDays">0</div>
            </div>
        </section>

        <section class="grid-cards animate-fade" style="animation-delay: 0.2s;">
            <a href="perjalanan.html" class="feature-card-custom">
                <div class="card-image" style="background-image: url('https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&q=80&w=600');"></div>
                <div class="card-content">
                    <h3 class="card-title-custom">Kelola Perjalanan</h3>
                    <p class="card-desc">Catat rute, waktu, dan detail perjalanan memancing Anda secara terorganisir.</p>
                    <span class="btn-primary" style="width: 100%; justify-content: center;">Buka Perjalanan</span>
                </div>
            </a>

            <a href="catatan_memancing.html" class="feature-card-custom">
                <div class="card-image" style="background-image: url('https://images.unsplash.com/photo-1505322101000-19457cff32ba?auto=format&fit=crop&q=80&w=600');"></div>
                <div class="card-content">
                    <h3 class="card-title-custom">Catatan & Data</h3>
                    <p class="card-desc">Simpan detail tangkapan, jenis ikan, dan spot-spot favorit yang telah ditemukan.</p>
                    <span class="btn-primary" style="width: 100%; justify-content: center;">Buka Catatan</span>
                </div>
            </a>

            <a href="laporan.html" class="feature-card-custom">
                <div class="card-image" style="background-image: url('https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&q=80&w=600');"></div>
                <div class="card-content">
                    <h3 class="card-title-custom">Laporan Statistik</h3>
                    <p class="card-desc">Analisis performa memancing Anda melalui grafik dan data statistik yang akurat.</p>
                    <span class="btn-primary" style="width: 100%; justify-content: center;">Lihat Laporan</span>
                </div>
            </a>
        </section>
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Dark Mode Logic
            const body = $('body');
            const themeToggles = $('#themeToggle, .themeToggleMobile');
            
            function updateThemeIcons(isDark) {
                const icons = themeToggles.find('i');
                const text = $('#themeTextMobile');
                if (isDark) {
                    icons.removeClass('fa-moon').addClass('fa-sun');
                    text.text('Tema Terang');
                } else {
                    icons.removeClass('fa-sun').addClass('fa-moon');
                    text.text('Tema Malam');
                }
            }

            if (localStorage.getItem('theme') === 'dark') {
                body.addClass('dark-mode');
                updateThemeIcons(true);
            }

            themeToggles.on('click', function() {
                body.toggleClass('dark-mode');
                const isDark = body.hasClass('dark-mode');
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
                updateThemeIcons(isDark);
            });

            loadDashboardStats();

            $('#navToggle').on('click', function() {
                $('#navLinks').toggleClass('active');
                $(this).find('i').toggleClass('fa-bars fa-times');
            });
        });

        function loadDashboardStats() {
            $.ajax({
                url: '../api/laporan_api.php',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.data) {
                        const stats = response.data.stats;
                        $('#totalTrips').text(stats.total_trips || 0);
                        $('#totalCatches').text(stats.total_catches || 0);
                        $('#totalSpots').text(stats.total_spots || 0);
                        
                        const trips = response.data.trips || [];
                        const uniqueDates = new Set(trips.map(t => t.waktu_mulai?.substring(0, 10)));
                        $('#activeDays').text(uniqueDates.size || 0);
                    }
                },
                error: function(xhr) {
                    console.error('Error loading stats:', xhr);
                }
            });
        }
    </script>
</body>
</html>

