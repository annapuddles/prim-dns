<?php
$Config = parse_ini_file('config.ini', true);

function create_db_connection() {
	global $Config;

	return new mysqli($Config['database']['host'], $Config['database']['user'], $Config['database']['password'], $Config['database']['name'], $Config['database']['port']);
}

function generate_auth($conn) {
	$stmt = $conn->prepare('SELECT SHA1(UUID())');
	$stmt->bind_result($uuid);
	$stmt->execute();
	$stmt->fetch();
	$stmt->close();

	return $uuid;
}

function prune($conn) {
	$stmt = $conn->prepare("DELETE FROM alias WHERE last_access < DATE_SUB(NOW(), INTERVAL 1 DAY)");
	$stmt->execute();
	$stmt->close();
}

function successful_response($name, $auth) {
	echo json_encode([
		'name' => $name,
		'auth' => $auth,
		'endpoint' => 'https://annapuddles.com/prim-dns/alias/' . $name,
		'redirect' => 'https://annapuddles.com/prim-dns/redirect/' . $name
	], JSON_UNESCAPED_SLASHES);
}

function get_url($conn, $name) {
	$stmt = $conn->prepare('SELECT url FROM alias WHERE name = ?');
	$stmt->bind_param('s', $name);
	$stmt->bind_result($url);
	$stmt->execute();
	$stmt->fetch();
	$stmt->close();

	if ($url) {
		$stmt = $conn->prepare('UPDATE alias SET last_access = NOW() WHERE name = ?');
		$stmt->bind_param('s', $name);
		$stmt->execute();
		$stmt->close();
	}

	return $url;
}
?>
