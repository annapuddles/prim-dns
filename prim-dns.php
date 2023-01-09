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
	$stmt = $conn->prepare("DELETE FROM alias WHERE expires IS NOT NULL AND expires < NOW()");
	$stmt->execute();
	$stmt->close();
}

function successful_response($name, $auth) {
	global $Config;

	$response = [
		'name' => $name,
		'endpoint' => $Config['application']['root'] . '/alias/' . $name,
		'redirect' => $Config['application']['root'] . '/redirect/' . $name
	];

	if (isset($auth)) {
		$response['auth'] = $auth;
	}

	return json_encode($response, JSON_UNESCAPED_SLASHES);
}

function update_expiration($conn, $name, $force) {
	global $Config;

	if ($force) {
		$stmt = $conn->prepare('UPDATE alias SET expires = DATE_ADD(NOW(), INTERVAL ? DAY) WHERE name = ?');
	} else {
		$stmt = $conn->prepare('UPDATE alias SET expires = DATE_ADD(NOW(), INTERVAL ? DAY) WHERE name = ? AND url IS NOT NULL AND expires IS NOT NULL');
	}

	$stmt->bind_param('is', $Config['application']['prune_days'], $name);
	$stmt->execute();
	$stmt->close();
}

function get_alias($conn, $name) {
	update_expiration($conn, $name, false);

	$stmt = $conn->prepare('SELECT url, expires FROM alias WHERE name = ?');
	$stmt->bind_param('s', $name);
	$stmt->bind_result($url, $expires);
	$stmt->execute();
	$stmt->fetch();
	$stmt->close();

	if ($url) {
		return ['url' => $url, 'expires' => $expires];
	} else {
		return null;
	}
}
?>
