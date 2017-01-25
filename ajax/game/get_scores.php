<?php

include dirname(__FILE__) . '/../../app/db/connection.php';

$result = array();

$db_connection = connect();

$query = ' SELECT id, player, ip, score, saved FROM _game_scores' .
'          ORDER BY score DESC LIMIT 20';

$statement = $db_connection->prepare($query);

$statement->execute();

$result = $statement->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($result);

?>
