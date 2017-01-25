<?php

include dirname(__FILE__) . '/../db/connection.php';
include dirname(__FILE__) . '/../db/token.php';

$dbc = connect();

$token = get_token();

clear_token($dbc, $token);

$success = true;
$message = 'Nastąpiło poprawne wylogowanie z serwisu.';

$result = array('success' => $success, 'message' => $message);

echo json_encode($result);

?>
