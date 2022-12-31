<?php
include 'prim-dns.php';

$conn = create_db_connection();

$url = get_url($conn, $_GET['name']);

if ($url == null) {
	http_response_code(404);
	echo json_encode(['error' => 'Not Found: No URL was found for the alias "' . $_GET['name'] . '".']);
} else {
	header('Location: ' . $url, true, 307);
	echo json_encode(['url' => $url], JSON_UNESCAPED_SLASHES);
}
?>
