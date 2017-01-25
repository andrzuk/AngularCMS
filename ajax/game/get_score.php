<?php

include dirname(__FILE__) . '/../../app/db/connection.php';

$ip = $_SERVER['REMOTE_ADDR'];
$player = $_POST['player'];
$score = 0;

$player = preg_replace("/[^a-zA-Z0-9]+/", "", $player);
$player = substr($player, 0, 32);

$db_connection = connect();

$query = ' SELECT id, score FROM _game_scores' .
'          WHERE player = :player AND ip = :ip';

$statement = $db_connection->prepare($query);

$statement->bindParam(':player', $player, PDO::PARAM_STR);
$statement->bindParam(':ip', $ip, PDO::PARAM_STR);

$statement->execute();

$row_item = $statement->fetch(PDO::FETCH_ASSOC);

if (is_array($row_item))
{
	if (array_key_exists('id', $row_item) && array_key_exists('score', $row_item))
	{
		$id = $row_item['id'];
		$score = $row_item['score'];
	}
}

$response = array('player' => $player, 'ip' => $ip, 'score' => $score);

echo json_encode($response);

?>
