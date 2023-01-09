<?php
include 'prim-dns.php';

$conn = create_db_connection();

$alias = get_alias($conn, $_GET['name']);

if ($alias == null) {
	http_response_code(404);
	echo json_encode(['error' => 'Not Found: No URL was found for the alias "' . $_GET['name'] . '".']);
} else {
	header('Location: ' . $alias['url'], true, 307);
	echo json_encode($alias, JSON_UNESCAPED_SLASHES);
}
?>
