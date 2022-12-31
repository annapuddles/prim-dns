<?php
include 'prim-dns.php';

$headers = array_change_key_case(getallheaders(), CASE_LOWER);

$authorization = isset($headers['authorization']) ? $headers['authorization'] : null;

$conn = create_db_connection();

prune($conn);

switch ($_SERVER['REQUEST_METHOD']) {
	case 'GET':
		$url = get_url($conn, $_GET['name']);

		if ($url == null) {
			http_response_code(404);
			die(json_encode(['error' => 'Not Found: No URL was found for the alias "' . $_GET['name'] . '".']));
		}

		echo json_encode(['url' => $url], JSON_UNESCAPED_SLASHES);

		break;

	case 'POST':
		$last_object = isset($headers['x-secondlife-object-key']) ? $headers['x-secondlife-object-key'] : null;

		$data = json_decode(file_get_contents('php://input'));
		$name = isset($data->name) ? $data->name : $last_object;
		$url = isset($data->url) ? $data->url : null;

		$stmt = $conn->prepare('SELECT auth FROM alias WHERE name = ?');
		$stmt->bind_param('s', $name);
		$stmt->bind_result($auth);
		$stmt->execute();
		$stmt->fetch();
		$stmt->close();

		header('Content-type: application/json');

		if ($auth) {
			if ($authorization == $auth) {
				$stmt = $conn->prepare('UPDATE alias SET url = ?, last_object = ?, last_access = NOW() WHERE name = ?');
				$stmt->bind_param('sss', $url, $last_object, $name);
				$stmt->execute();
				$stmt->close();

				successful_response($name, $auth);
			} else {
				http_response_code(401);
				echo json_encode(['error' => 'Unauthorized: This alias is already in use or you did not provide the correct authorization key in your request.']);
			}

		} else {
			$auth = generate_auth($conn);

			$stmt = $conn->prepare('INSERT INTO alias (name, auth, url, last_object, last_access) VALUES (?, ?, ?, ?, NOW())');
			$stmt->bind_param('ssss', $name, $auth, $url, $last_object);
			$stmt->execute();
			$stmt->close();

			successful_response($name, $auth);
		}

		break;

	case 'DELETE':
		if (!isset($_GET['name'])) {
			http_response_code(400);
			die(json_encode(['error' => 'Bad Request: No alias was provided.']));
		}

		if ($authorization == null) {
			http_response_code(400);
			die(json_encode(['error' => 'Bad Request: No auth was provided.']));
		}

		$stmt = $conn->prepare('DELETE FROM alias WHERE name = ? AND auth = ?');
		$stmt->bind_param('ss', $_GET['name'], $authorization);
		$stmt->execute();

		if ($stmt->affected_rows > 0) {
			echo json_encode(['success' => 'Success: Alias "' . $_GET['name'] . '" was deleted.']);
		} else {
			http_response_code(401);
			echo json_encode(['error' => 'Unauthorized: Alias "' . $_GET['name'] . '" does not exist or the auth code provided was incorrect.']);
		}

		$stmt->close();

		break;
}

$conn->close();
?>
