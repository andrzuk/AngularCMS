<?php

include dirname(__FILE__) . '/../../app/db/connection.php';

$ip = $_SERVER['REMOTE_ADDR'];
$blocks = intval($_POST['blocks']);
$level = intval($_POST['level']);
$scores = intval($_POST['scores']);
$record = intval($_POST['record']);
$achieved = $_POST['achieved'];
$play_time = $_POST['play_time'];

$db_connection = connect();

$query = 	'INSERT INTO _game_stats' .
			' (ip, blocks, level, scores, record, achieved, play_time, saved) VALUES' .
			' (:ip, :blocks, :level, :scores, :record, :achieved, :play_time, NOW())';

$statement = $db_connection->prepare($query);

$statement->bindValue(':ip', $ip, PDO::PARAM_STR); 
$statement->bindValue(':blocks', $blocks, PDO::PARAM_INT); 
$statement->bindValue(':level', $level, PDO::PARAM_INT); 
$statement->bindValue(':scores', $scores, PDO::PARAM_INT); 
$statement->bindValue(':record', $record, PDO::PARAM_INT); 
$statement->bindValue(':achieved', $achieved, PDO::PARAM_STR); 
$statement->bindValue(':play_time', $play_time, PDO::PARAM_STR); 

$statement->execute();

$response = array('record' => $record, 'achieved' => $achieved);

echo json_encode($response);

?>
