<?php
/**
 * Basic Test - Tanpa Database
 */

echo "<h2>Testing Basic PHP...</h2>\n";
echo "<pre>\n";

// Test 1: PHP berjalan
echo "✓ PHP berjalan\n";

// Test 2: File config ada
if (file_exists('config/config.php')) {
    echo "✓ config/config.php ada\n";
} else {
    echo "✗ config/config.php tidak ada\n";
}

if (file_exists('config/config_local.php')) {
    echo "✓ config/config_local.php ada (untuk localhost)\n";
} else {
    echo "✗ config/config_local.php tidak ada\n";
}

// Test 3: File classes ada
$classes = ['Database', 'User', 'Perjalanan', 'Spot', 'Catatan', 'Tangkapan', 'Foto'];
foreach ($classes as $class) {
    $file = 'classes/' . $class . '.php';
    if (file_exists($file)) {
        echo "✓ classes/$class.php ada\n";
    } else {
        echo "✗ classes/$class.php tidak ada\n";
    }
}

// Test 4: File API ada
$apis = ['user_api', 'perjalanan_api', 'spot_api', 'catatan_api', 'tangkapan_api', 'foto_api', 'laporan_api'];
foreach ($apis as $api) {
    $file = 'api/' . $api . '.php';
    if (file_exists($file)) {
        echo "✓ api/$api.php ada\n";
    } else {
        echo "✗ api/$api.php tidak ada\n";
    }
}

echo "\nSelesai!</pre>\n";

echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>Buka <a href='test_connection_oop.php'>test_connection_oop.php</a> untuk test koneksi database</li>";
echo "<li>Jika database connection gagal, gunakan config_local.php untuk localhost</li>";
echo "<li>Buat database 'wongsibuk' di phpMyAdmin</li>";
echo "<li>Import SQL dari database_setup.sql</li>";
echo "</ol>";
