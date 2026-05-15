<?php
/**
 * Test Database Connection
 */

require_once 'config.php';

echo "<h2>Database Connection Test</h2>";

// Check if connected
if ($conn->connect_error) {
    echo "<div style='color: red;'><strong>❌ Connection Failed:</strong> " . $conn->connect_error . "</div>";
    exit();
}

echo "<div style='color: green;'><strong>✅ Connected to database successfully!</strong></div>";

// Check if tables exist
$tables = ['pengguna', 'perjalanan', 'spot_memancing', 'catatan_memancing', 'tangkapan', 'foto'];
echo "<h3>Checking Tables:</h3>";

foreach ($tables as $table) {
    $query = "SHOW TABLES LIKE '$table'";
    $result = $conn->query($query);
    
    if ($result && $result->num_rows > 0) {
        echo "<div style='color: green;'>✅ Table <strong>$table</strong> exists</div>";
    } else {
        echo "<div style='color: red;'>❌ Table <strong>$table</strong> NOT found</div>";
    }
}

// Check users in pengguna table
echo "<h3>Users in pengguna table:</h3>";
$query = "SELECT id_pengguna, nama, email FROM pengguna LIMIT 5";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; padding: 10px;'>";
    echo "<tr><th>ID</th><th>Nama</th><th>Email</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row['id_pengguna'] . "</td><td>" . $row['nama'] . "</td><td>" . $row['email'] . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "<div style='color: orange;'>⚠️ No users found or table doesn't exist</div>";
}

$conn->close();
?>
