<?php

include dirname(__FILE__) . '/../../db/connection.php';
include dirname(__FILE__) . '/../../db/access.php';
include dirname(__FILE__) . '/../../db/setting.php';

$result = array();

$dbc = connect();

if (check_access($dbc)) // if user rights are sufficient, get database content
{
	$visitors_period = get_setting_value($dbc, 'visitors_period');
	$visitors_excluded = get_setting_value($dbc, 'visitors_excluded');

	$date_from = date("Y-m-d", strtotime($visitors_period));
	$date_to = date("Y-m-d", time());

	$query = 	"SELECT date AS date, COUNT(*) AS count, SUM(counter) AS sum" .
				" FROM stat_ip" .
				" WHERE date BETWEEN '".$date_from."' AND '".$date_to."'" . 
				" AND ip NOT IN (". $visitors_excluded .")" .
				" GROUP BY date" .
				" ORDER BY date LIMIT 1000";

	$statement = $dbc->prepare($query);

	$statement->execute();

	$result = $statement->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($result);

?>
