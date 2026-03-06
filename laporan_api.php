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

// Check if filtering by specific trip
$trip_filter = isset($_GET['id_perjalanan']) ? (int)$_GET['id_perjalanan'] : null;

try {
    // Get trips for this user (filtered if specified)
    $tripsQuery = "SELECT id_perjalanan, waktu_mulai, waktu_selesai, jarak_lokasi 
                   FROM perjalanan 
                   WHERE id_pengguna = ? 
                   " . ($trip_filter ? "AND id_perjalanan = ?" : "") . "
                   ORDER BY waktu_mulai DESC";
    $tripsStmt = $conn->prepare($tripsQuery);
    if ($trip_filter) {
        $tripsStmt->bind_param('ii', $user_id, $trip_filter);
    } else {
        $tripsStmt->bind_param('i', $user_id);
    }
    $tripsStmt->execute();
    $tripsResult = $tripsStmt->get_result();
    $trips = [];

    while ($trip = $tripsResult->fetch_assoc()) {
        // Get catches for this trip via catatan_memancing
        $catchesQuery = "SELECT t.jenis_ikan 
                         FROM tangkapan t
                         JOIN catatan_memancing c ON t.id_catatan = c.id_catatan
                         WHERE c.id_perjalanan = ?";
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

    // Get catches for this user (filtered by trip if specified)
    $catchesQuery = "SELECT t.id_tangkapan, t.id_catatan, t.jenis_ikan, t.nama_ikan, t.jumlah_ikan, t.tanggal_jawa
                     FROM tangkapan t
                     JOIN catatan_memancing c ON t.id_catatan = c.id_catatan
                     JOIN perjalanan p ON c.id_perjalanan = p.id_perjalanan
                     WHERE p.id_pengguna = ?
                     " . ($trip_filter ? "AND p.id_perjalanan = ?" : "") . "
                     ORDER BY t.id_tangkapan DESC";
    $catchesStmt = $conn->prepare($catchesQuery);
    if ($trip_filter) {
        $catchesStmt->bind_param('ii', $user_id, $trip_filter);
    } else {
        $catchesStmt->bind_param('i', $user_id);
    }
    $catchesStmt->execute();
    $catchesResult = $catchesStmt->get_result();
    $catches = [];

    while ($catch = $catchesResult->fetch_assoc()) {
        $catches[] = $catch;
    }
    $catchesStmt->close();

    // Get spots (filtered by trip if specified, otherwise all spots used by user)
    if ($trip_filter) {
        $spotsQuery = "SELECT DISTINCT s.id_spot, s.alamat, s.deskripsi_spot, s.jenis_spot 
                       FROM spot_memancing s
                       JOIN catatan_memancing c ON s.id_spot = c.id_spot
                       WHERE c.id_perjalanan = ?
                       ORDER BY s.id_spot DESC";
        $spotsStmt = $conn->prepare($spotsQuery);
        $spotsStmt->bind_param('i', $trip_filter);
    } else {
        $spotsQuery = "SELECT DISTINCT s.id_spot, s.alamat, s.deskripsi_spot, s.jenis_spot 
                       FROM spot_memancing s
                       JOIN catatan_memancing c ON s.id_spot = c.id_spot
                       JOIN perjalanan p ON c.id_perjalanan = p.id_perjalanan
                       WHERE p.id_pengguna = ?
                       ORDER BY s.id_spot DESC";
        $spotsStmt = $conn->prepare($spotsQuery);
        $spotsStmt->bind_param('i', $user_id);
    }
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
