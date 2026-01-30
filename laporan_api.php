<?php
/**
 * Laporan API Endpoint
 * Aggregates data dari perjalanan, tangkapan, dan spot memancing
 * Fishing Log Application
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'config.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['id_pengguna'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['id_pengguna'];

try {
    // Get all trips for this user
    $tripsQuery = "SELECT id_perjalanan, waktu_mulai, waktu_selesai, jarak_lokasi 
                   FROM perjalanan 
                   WHERE id_pengguna = ? 
                   ORDER BY waktu_mulai DESC";
    $tripsStmt = $conn->prepare($tripsQuery);
    $tripsStmt->bind_param('i', $user_id);
    $tripsStmt->execute();
    $tripsResult = $tripsStmt->get_result();
    $trips = [];

    while ($trip = $tripsResult->fetch_assoc()) {
        // Get catches for this trip
        $catchesQuery = "SELECT jenis_ikan 
                         FROM tangkapan 
                         WHERE id_perjalanan = ?";
        $catchesStmt = $conn->prepare($catchesQuery);
        $catchesStmt->bind_param('i', $trip['id_perjalanan']);
        $catchesStmt->execute();
        $catchesResult = $catchesStmt->get_result();
        
        $fish_types = [];
        $total_catches = 0;
        while ($catch = $catchesResult->fetch_assoc()) {
            $fish_types[] = $catch['jenis_ikan'];
            $total_catches++;
        }
        $catchesStmt->close();

        $trip['fish_types'] = array_unique($fish_types);
        $trip['total_catches'] = $total_catches;
        $trips[] = $trip;
    }
    $tripsStmt->close();

    // Get all catches for this user (via perjalanan)
    $catchesQuery = "SELECT t.id_tangkapan, t.id_perjalanan, t.jenis_ikan, t.nama_ikan, t.jumlah_ikan, t.tanggal_jawa
                     FROM tangkapan t
                     JOIN perjalanan p ON t.id_perjalanan = p.id_perjalanan
                     WHERE p.id_pengguna = ?
                     ORDER BY t.id_tangkapan DESC";
    $catchesStmt = $conn->prepare($catchesQuery);
    $catchesStmt->bind_param('i', $user_id);
    $catchesStmt->execute();
    $catchesResult = $catchesStmt->get_result();
    $catches = [];

    while ($catch = $catchesResult->fetch_assoc()) {
        $catches[] = $catch;
    }
    $catchesStmt->close();

    // Get all spots (public data, not user-specific)
    $spotsQuery = "SELECT id_spot, alamat, deskripsi_spot, jenis_spot 
                   FROM spot_memancing 
                   ORDER BY id_spot DESC";
    $spotsStmt = $conn->prepare($spotsQuery);
    $spotsStmt->execute();
    $spotsResult = $spotsStmt->get_result();
    $spots = [];

    while ($spot = $spotsResult->fetch_assoc()) {
        $spots[] = $spot;
    }
    $spotsStmt->close();

    // Calculate statistics
    $stats = [
        'total_trips' => count($trips),
        'total_catches' => count($catches),
        'total_distance' => 0,
        'total_spots' => count($spots)
    ];

    // Sum total distance
    foreach ($trips as $trip) {
        $stats['total_distance'] += floatval($trip['jarak_lokasi']);
    }

    // Response
    echo json_encode([
        'success' => true,
        'data' => [
            'stats' => $stats,
            'trips' => $trips,
            'catches' => $catches,
            'spots' => $spots
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

$conn->close();
?>
