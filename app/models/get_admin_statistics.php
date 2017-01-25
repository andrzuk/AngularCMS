<?php

include dirname(__FILE__) . '/../db/connection.php';
include dirname(__FILE__) . '/../db/access.php';
include dirname(__FILE__) . '/../db/setting.php';

$result = array();
$admin = array();

$dbc = connect();

if (check_access($dbc)) // if user rights are sufficient, get database content
{
	$query = 'SELECT COUNT(*) AS counter FROM settings';
	$statement = $dbc->prepare($query);
	$statement->execute();
	$result = $statement->fetch(PDO::FETCH_ASSOC);
	$settings = $result['counter'];

	$query = 'SELECT COUNT(*) AS counter FROM users';
	$statement = $dbc->prepare($query);
	$statement->execute();
	$result = $statement->fetch(PDO::FETCH_ASSOC);
	$users = $result['counter'];

	$query = 'SELECT COUNT(*) AS counter FROM access_levels';
	$statement = $dbc->prepare($query);
	$statement->execute();
	$result = $statement->fetch(PDO::FETCH_ASSOC);
	$acl = $result['counter'];

	$query = 'SELECT COUNT(*) AS counter FROM categories';
	$statement = $dbc->prepare($query);
	$statement->execute();
	$result = $statement->fetch(PDO::FETCH_ASSOC);
	$categories = $result['counter'];

	$query = 'SELECT COUNT(*) AS counter FROM pages';
	$statement = $dbc->prepare($query);
	$statement->execute();
	$result = $statement->fetch(PDO::FETCH_ASSOC);
	$pages = $result['counter'];

	$query = 'SELECT COUNT(*) AS counter FROM messages WHERE requested = 1';
	$statement = $dbc->prepare($query);
	$statement->execute();
	$result = $statement->fetch(PDO::FETCH_ASSOC);
	$messages = $result['counter'];

	$query = 'SELECT COUNT(*) AS counter FROM images';
	$statement = $dbc->prepare($query);
	$statement->execute();
	$result = $statement->fetch(PDO::FETCH_ASSOC);
	$images = $result['counter'];

	$visitors_period = get_setting_value($dbc, 'visitors_period');
	$visitors_excluded = get_setting_value($dbc, 'visitors_excluded');
	$visitors_referer = get_setting_value($dbc, 'visitors_referer');
	$visitors_referer = '%' . $visitors_referer . '%';

	$date_period = date("Y-m-d", strtotime($visitors_period));

	$query = 'SELECT COUNT(*) AS counter FROM visitors' .
	'         WHERE visited > :visited AND http_referer LIKE :referer AND visitor_ip NOT IN ('. $visitors_excluded .')';
	$statement = $dbc->prepare($query);
	$statement->bindValue(':visited', $date_period, PDO::PARAM_STR); 
	$statement->bindValue(':referer', $visitors_referer, PDO::PARAM_STR); 
	$statement->execute();
	$result = $statement->fetch(PDO::FETCH_ASSOC);
	$visitors = $result['counter'];
}

$admin = array(
	'settings' => intval($settings),
	'users' => intval($users),
	'acl' => intval($acl),
	'categories' => intval($categories),
	'pages' => intval($pages),
	'messages' => intval($messages),
	'images' => intval($images),
	'visitors' => intval($visitors),
);

echo json_encode($admin);

?>
