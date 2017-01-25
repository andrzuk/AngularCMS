<?php

include dirname(__FILE__) . '/../../app/db/connection.php';

$ip = $_SERVER['REMOTE_ADDR'];
$player = $_POST['player'];
$score = intval($_POST['score']);

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
		$last_score = $row_item['score'];
		if ($last_score < $score) 
		{
			$query = 	'UPDATE _game_scores' .
						' SET score = :score, saved = NOW()' .
						' WHERE id = :id';

			$statement = $db_connection->prepare($query);

			$statement->bindValue(':id', $id, PDO::PARAM_INT); 
			$statement->bindValue(':score', $score, PDO::PARAM_INT); 

			$statement->execute();
		}
		else
		{
			$score = $last_score;
		}
	}
}
else
{
	if (strlen($player))
	{
		$query = 	'INSERT INTO _game_scores' .
					' (player, ip, score, saved) VALUES' .
					' (:player, :ip, :score, NOW())';

		$statement = $db_connection->prepare($query);

		$statement->bindValue(':player', $player, PDO::PARAM_STR); 
		$statement->bindValue(':ip', $ip, PDO::PARAM_STR); 
		$statement->bindValue(':score', $score, PDO::PARAM_INT); 

		$statement->execute();
	}
}

$response = array('player' => $player, 'ip' => $ip, 'score' => $score);

echo json_encode($response);

?>
