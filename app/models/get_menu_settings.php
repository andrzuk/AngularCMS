<?php

include dirname(__FILE__) . '/../db/connection.php';
include dirname(__FILE__) . '/../db/access.php';
include dirname(__FILE__) . '/../db/setting.php';

$result = array();

$dbc = connect();

$menu_sidebar_enabled = 'menu_sidebar_enabled';
$submenu_sidebar_enabled = 'submenu_sidebar_enabled';
$menu_navbar_enabled = 'menu_navbar_enabled';
$submenu_navbar_enabled = 'submenu_navbar_enabled';

try
{
	$query = 'SELECT * FROM settings WHERE key_name IN (:menu_sidebar_enabled, :submenu_sidebar_enabled, :menu_navbar_enabled, :submenu_navbar_enabled)';

	$statement = $dbc->prepare($query);

	$statement->bindParam(':menu_sidebar_enabled', $menu_sidebar_enabled, PDO::PARAM_STR);
	$statement->bindParam(':submenu_sidebar_enabled', $submenu_sidebar_enabled, PDO::PARAM_STR);
	$statement->bindParam(':menu_navbar_enabled', $menu_navbar_enabled, PDO::PARAM_STR);
	$statement->bindParam(':submenu_navbar_enabled', $submenu_navbar_enabled, PDO::PARAM_STR);

	$statement->execute();

	$context_list = $statement->fetchAll(PDO::FETCH_ASSOC);

	$result['context_list'] = $context_list;

}
catch (PDOException $e)
{
	$result = array('context_list' => NULL);
}

echo json_encode($result);

?>
