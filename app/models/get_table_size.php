<?php

include dirname(__FILE__) . '/../db/connection.php';
include dirname(__FILE__) . '/../db/setting.php';

$result = array();

$dbc = connect();

$table_name = $_GET['name'];

try
{
	if ($table_name == 'visitors')
	{
		$visitors_period = get_setting_value($dbc, 'visitors_period');
		$visitors_excluded = get_setting_value($dbc, 'visitors_excluded');
		$visitors_referer = get_setting_value($dbc, 'visitors_referer');
		$visitors_referer = '%' . $visitors_referer . '%';

		$date_period = date("Y-m-d", strtotime($visitors_period));

		$query = 'SELECT COUNT(*) AS counter FROM ' . $table_name .
		'         WHERE visited > :visited AND http_referer LIKE :referer AND visitor_ip NOT IN ('. $visitors_excluded .')';

		$statement = $dbc->prepare($query);

		$statement->bindValue(':visited', $date_period, PDO::PARAM_STR);
		$statement->bindValue(':referer', $visitors_referer, PDO::PARAM_STR);

		$statement->execute();

		$result = $statement->fetch(PDO::FETCH_ASSOC);

		$result['counter'] = intval($result['counter']);
	}
	else if ($table_name == 'searches')
	{
		$visitors_excluded = get_setting_value($dbc, 'visitors_excluded');

		$query = 'SELECT COUNT(*) AS counter FROM ' . $table_name .
		'         WHERE user_ip NOT IN ('. $visitors_excluded .')';

		$statement = $dbc->prepare($query);

		$statement->execute();

		$result = $statement->fetch(PDO::FETCH_ASSOC);

		$result['counter'] = intval($result['counter']);
	}
	else if ($table_name == '_game_stats')
	{
		$visitors_excluded = get_setting_value($dbc, 'visitors_excluded');

		$query = 'SELECT COUNT(*) AS counter FROM ' . $table_name .
		'         WHERE ip NOT IN ('. $visitors_excluded .')';

		$statement = $dbc->prepare($query);

		$statement->execute();

		$result = $statement->fetch(PDO::FETCH_ASSOC);

		$result['counter'] = intval($result['counter']);
	}
	else
	{
		$query = 'SELECT COUNT(*) AS counter FROM ' . $table_name;

		$statement = $dbc->prepare($query);

		$statement->execute();

		$result = $statement->fetch(PDO::FETCH_ASSOC);

		$result['counter'] = intval($result['counter']);
	}
}
catch (PDOException $e)
{
}

echo json_encode($result);

?>
