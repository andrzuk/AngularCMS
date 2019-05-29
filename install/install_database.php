<?php

// NOTE: Don't echo anything other than $result in this file, unless
// running it directly (specifying post variables manually).
// Otherwise, the JSON will be corrupt (Install will not be able to find
// the success variable, and will stop.)

include dirname(__FILE__) . '/../app/db/connection.php';
header('Content-Type: application/json');

$form_data = $_POST;

$message = NULL;
$success = false;

if (!empty($form_data['brand']) && !empty($form_data['description']) && !empty($form_data['keywords']) && !empty($form_data['domain']) && !empty($form_data['admin_name']) && !empty($form_data['admin_email']) && !empty($form_data['admin_password']))
{
	$brand = $form_data['brand'];
	$description = $form_data['description'];
	$keywords = $form_data['keywords'];
	$domain = $form_data['domain'];
	$admin_name = $form_data['admin_name'];
	$admin_email = $form_data['admin_email'];
	$admin_password = $form_data['admin_password'];
	$password = sha1($admin_password);
	$enableShowIssue3 = false;

	$db_connection = connect();

	$query = '
		SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
		SET time_zone = "+00:00";
	';
	$statement = $db_connection->prepare($query);
	$statement->execute();

	$query = "
		SELECT COUNT(*) AS licznik FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
			WHERE CONSTRAINT_TYPE = 'FOREIGN KEY' AND TABLE_SCHEMA = '". DB_NAME ."'
	";
	$statement = $db_connection->prepare($query);
	$statement->execute();
	$check_item = $statement->fetch(PDO::FETCH_ASSOC);
	$constraints_exist = $check_item['licznik'];

	//echo "Counting constraints...<br/>";
	//var_dump($check_item);

	if ($constraints_exist)
	{
		//echo "Removing existing constraints...<br/>";
		$query = "
			ALTER TABLE `access_rights` DROP FOREIGN KEY `fk_rights_resources`;
			ALTER TABLE `access_rights` DROP FOREIGN KEY `fk_rights_users`;
			ALTER TABLE `access_users` DROP FOREIGN KEY `fk_access_modules`;
			ALTER TABLE `access_users` DROP FOREIGN KEY `fk_access_users`;
			ALTER TABLE `archives` DROP FOREIGN KEY `fk_archives_pages`;
			ALTER TABLE `archives` DROP FOREIGN KEY `fk_archives_users`;
			ALTER TABLE `images` DROP FOREIGN KEY `fk_images_users`;
			ALTER TABLE `pages` DROP FOREIGN KEY `fk_pages_users`;
		";
		$statement = $db_connection->prepare($query);
		$statement->execute();
	}
	//else echo "No constraints exist.";
	//echo "Dropping tables...<br/>";
		$query = "
			DROP TABLE IF EXISTS `access_levels`;
			DROP TABLE IF EXISTS `access_modules`;
			DROP TABLE IF EXISTS `access_rights`;
			DROP TABLE IF EXISTS `access_users`;
			DROP TABLE IF EXISTS `archives`;
			DROP TABLE IF EXISTS `categories`;
			DROP TABLE IF EXISTS `hosts`;
			DROP TABLE IF EXISTS `images`;
			DROP TABLE IF EXISTS `logins`;
			DROP TABLE IF EXISTS `messages`;
			DROP TABLE IF EXISTS `pages`;
			DROP TABLE IF EXISTS `roles`;
			DROP TABLE IF EXISTS `searches`;
			DROP TABLE IF EXISTS `settings`;
			DROP TABLE IF EXISTS `users`;
			DROP TABLE IF EXISTS `visitors`;
			DROP TABLE IF EXISTS `stat_ip`;
			DROP TABLE IF EXISTS `stat_main`;
			DROP TABLE IF EXISTS `_game_scores`;
			DROP TABLE IF EXISTS `_game_stats`;
		";
		$statement = $db_connection->prepare($query);
		$statement->execute();
	//echo "Creating access_levels...<br/>";

	$query = "
		CREATE TABLE `access_levels` (
			`id` int(11) unsigned NOT NULL,
			`resource` varchar(64) NOT NULL,
			`description` varchar(128) NOT NULL,
			`mask_a` tinyint(1) NOT NULL,
			`mask_o` tinyint(1) NOT NULL,
			`mask_u` tinyint(1) NOT NULL,
			`mask_g` tinyint(1) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	";
	$statement = $db_connection->prepare($query);
	$statement->execute();

	//echo "Inserting access_levels...<br/>";
	$query = "
		INSERT INTO `access_levels` (`id`, `resource`, `description`, `mask_a`, `mask_o`, `mask_u`, `mask_g`) VALUES
			(1, 'get_menu', 'Wczytanie głównego menu strony', 1, 1, 1, 1),
			(2, 'get_submenu', 'Wczytanie kontekstowego menu dla kategorii strony', 1, 1, 1, 1),
			(3, 'get_pages_public', 'Pobranie listy stron dla dostępu publicznego', 1, 1, 1, 1),
			(4, 'get_page_public', 'Pobranie informacji o stronie dla dostępu publicznego', 1, 1, 1, 1),
			(5, 'get_page_index', 'Pobranie strony głównej dla dostępu publicznego', 1, 1, 1, 1),
			(6, 'get_page_contact', 'Pobranie strony kontaktowej dla dostępu publicznego', 1, 1, 1, 1),
			(7, 'get_admin_statistics', 'Odczytanie statystyk dla panelu admina', 1, 1, 0, 0),
			(8, 'get_settings_list', 'Lista ustawień', 1, 0, 0, 0),
			(9, 'get_setting', 'Informacja o ustawieniach', 1, 0, 0, 0),
			(10, 'add_setting', 'Dopisywanie ustawień', 1, 0, 0, 0),
			(11, 'update_setting', 'Modyfikacja ustawień', 1, 0, 0, 0),
			(12, 'delete_setting', 'Usuwanie ustawień', 1, 0, 0, 0),
			(13, 'get_settings_filtered', 'Pobieranie listy znalezionych ustawień', 1, 0, 0, 0),
			(14, 'get_users_list', 'Lista użytkowników', 1, 1, 0, 0),
			(15, 'get_user', 'Informacja o użytkowniku', 1, 1, 1, 0),
			(16, 'add_user', 'Dopisywanie użytkowników', 1, 0, 0, 0),
			(17, 'update_user', 'Modyfikacja użytkowników', 1, 0, 0, 0),
			(18, 'delete_user', 'Usuwanie użytkowników', 1, 1, 0, 0),
			(19, 'change_password', 'Zmiana hasła użytkownika', 1, 1, 1, 0),
			(20, 'logout_user', 'Zdalne wylogowanie użytkownika', 1, 1, 0, 0),
			(21, 'get_users_filtered', 'Pobieranie listy znalezionych użytkowników', 1, 1, 1, 0),
			(22, 'get_rights_filtered', 'Pobieranie listy znalezionych praw dostępu dla użytkownika', 1, 1, 1, 0),
			(23, 'get_acl_list', 'Lista praw dostępu', 1, 1, 0, 0),
			(24, 'get_acl', 'Informacja o prawach dostępu', 1, 0, 0, 0),
			(25, 'add_acl', 'Dopisywanie praw dostępu', 1, 0, 0, 0),
			(26, 'update_acl', 'Modyfikacja praw dostępu', 1, 0, 0, 0),
			(27, 'delete_acl', 'Usuwanie praw dostępu', 1, 0, 0, 0),
			(28, 'get_user_rights', 'Informacja o prawach dostępu użytkownika', 1, 0, 0, 0),
			(29, 'update_user_rights', 'Zapis praw dostępu użytkownika', 1, 0, 0, 0),
			(30, 'get_acl_filtered', 'Pobieranie listy znalezionych praw dostępu', 1, 0, 0, 0),
			(31, 'get_user_modules', 'Informacja o dostępie użytkownika do modułów', 1, 1, 1, 0),
			(32, 'get_modules_filtered', 'Pobieranie listy znalezionych dostępów dla usera bez podziału na strony', 1, 1, 1, 0),
			(33, 'update_user_modules', 'Zapis praw dostępu użytkownika do modułów', 1, 0, 0, 0),
			(34, 'get_messages_list', 'Lista wiadomości', 1, 0, 0, 0),
			(35, 'get_message', 'Informacja o wiadomości', 1, 1, 0, 0),
			(36, 'update_message', 'Modyfikacja wiadomości', 1, 1, 0, 0),
			(37, 'delete_message', 'Usuwanie wiadomości', 1, 0, 0, 0),
			(38, 'get_messages_filtered', 'Pobieranie listy znalezionych wiadomości', 1, 1, 0, 0),
			(39, 'get_images_list', 'Lista obrazków galerii', 1, 1, 0, 0),
			(40, 'get_image', 'Pobieranie obrazków z galerii', 1, 1, 1, 1),
			(41, 'get_image_details', 'Pobieranie informacji o obrazku w galerii', 1, 1, 1, 1),
			(42, 'add_image', 'Dopisywanie obrazków do galerii', 1, 1, 0, 0),
			(43, 'update_image', 'Zmiana obrazka w galerii', 1, 1, 0, 0),
			(44, 'delete_image', 'Usuwanie obrazków z galerii', 1, 1, 0, 0),
			(45, 'get_images_filtered', 'Pobieranie listy znalezionych obrazków galerii', 1, 1, 0, 0),
			(46, 'get_categories_list', 'Lista kategorii', 1, 1, 0, 0),
			(47, 'get_category', 'Odczytanie szczegółów kategorii', 1, 1, 0, 0),
			(48, 'add_category', 'Dopisywanie kategorii', 1, 1, 0, 0),
			(49, 'update_category', 'Modyfikacja kategorii', 1, 1, 0, 0),
			(50, 'delete_category', 'Usuwanie kategorii', 1, 0, 0, 0),
			(51, 'move_category', 'Zmiana kolejności kategorii', 1, 0, 0, 0),
			(52, 'get_categories_filtered', 'Pobieranie listy znalezionych kategorii', 1, 1, 0, 0),
			(53, 'get_pages_list', 'Lista podstron serwisu', 1, 1, 0, 0),
			(54, 'get_page', 'Pobieranie danych o stronie', 1, 1, 0, 0),
			(55, 'add_page', 'Dopisywanie stron', 1, 1, 0, 0),
			(56, 'update_page', 'Modyfikacja stron', 1, 1, 0, 0),
			(57, 'delete_page', 'Usuwanie stron', 1, 0, 0, 0),
			(58, 'get_archives_list', 'Pobieranie listy archiwów strony', 1, 1, 0, 0),
			(59, 'get_archive', 'Pobieranie archiwalnej wersji strony', 1, 1, 0, 0),
			(60, 'archive_page', 'Archiwizowanie strony', 1, 1, 0, 0),
			(61, 'restore_page', 'Przywracanie archiwum strony', 1, 1, 0, 0),
			(62, 'get_pages_filtered', 'Pobieranie listy znalezionych stron', 1, 1, 0, 0),
			(63, 'get_visitors_list', 'Lista odwiedzin', 1, 1, 0, 0),
			(64, 'get_visitor', 'Informacja o odwiedzinach', 1, 1, 0, 0),
			(65, 'get_statistics', 'Statystyki odwiedzin', 1, 1, 0, 0),
			(66, 'get_visitors_filtered', 'Pobieranie listy znalezionych odwiedzin', 1, 1, 0, 0),
			(67, 'exclude_visitor', 'Dodanie adresu ip do listy wykluczeń z raportu odwiedzin', 1, 1, 0, 0),
			(68, 'get_logins_list', 'Lista logowań', 1, 0, 0, 0),
			(69, 'get_login', 'Informacja o logowaniu', 1, 0, 0, 0),
			(70, 'get_logins_filtered', 'Pobieranie listy znalezionych logowań', 1, 0, 0, 0),
			(71, 'get_style', 'Pobranie zawartości pliku stylów', 1, 0, 0, 0),
			(72, 'update_style', 'Zapis zawartości stylów do pliku', 1, 0, 0, 0),
			(73, 'get_script', 'Pobranie zawartości pliku skryptów', 1, 0, 0, 0),
			(74, 'update_script', 'Zapis zawartości skryptów do pliku', 1, 0, 0, 0),
			(75, 'get_found_list', 'Pobieranie listy znalezionych stron dla usługi wyszukiwania', 0, 0, 0, 1),
			(76, 'get_searches_list', 'Pobieranie listy wyszukiwanych przez użytkowników fraz', 1, 1, 0, 0),
			(77, 'delete_search', 'Usuwanie wyszukiwanych przez użytkowników fraz', 1, 0, 0, 0),
			(78, 'get_searches_filtered', 'Pobieranie listy znalezionych wyszukiwań fraz użytkowników', 1, 1, 0, 0),
			(79, 'get_games_list', 'Pobieranie listy statystyk granych przez użytkowników gier', 1, 1, 1, 1),
			(80, 'delete_game', 'Usuwanie statystyk granych przez użytkowników gier', 1, 1, 0, 0);
	";
	$statement = $db_connection->prepare($query);
	$statement->execute();

	//echo "Creating access_modules...<br/>";
	$query = "
		CREATE TABLE `access_modules` (
		  `id` int(11) UNSIGNED NOT NULL,
		  `module` varchar(32) NOT NULL,
		  `description` varchar(128) NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	";
	$statement = $db_connection->prepare($query);
	$statement->execute();

	//echo "Inserting access_modules...<br/>";
	$query = "
		INSERT INTO `access_modules` (`id`, `module`, `description`) VALUES
			(1, 'admin', 'Admin Panel'),
			(2, 'acl', 'Access Control List'),
			(3, 'categories', 'Kategorie serwisu'),
			(4, 'images', 'Galeria serwisu'),
			(5, 'logins', 'Logowania użytkowników'),
			(6, 'messages', 'Wiadomości z formularza kontaktowego'),
			(7, 'pages', 'Strony serwisu'),
			(8, 'scripts', 'Kody JavaScript serwisu'),
			(9, 'styles', 'Style CSS serwisu'),
			(10, 'settings', 'Ustawienia serwisu'),
			(11, 'users', 'Zarejestrowani użytkownicy serwisu'),
			(12, 'visitors', 'Odwiedziny serwisu'),
			(13, 'searches', 'Wyszukiwania na stronie'),
			(14, 'games', 'Wyniki gier');
	";
	$statement = $db_connection->prepare($query);
	$statement->execute();

	//echo "Creating access_rights...<br/>";
	$query = "
		CREATE TABLE `access_rights` (
		  `id` int(11) UNSIGNED NOT NULL,
		  `user_id` int(11) UNSIGNED NOT NULL,
		  `resource_id` int(11) UNSIGNED NOT NULL,
		  `access` tinyint(1) NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	";
	$statement = $db_connection->prepare($query);
	$statement->execute();

	//echo "Inserting access_rights...<br/>";
	$query = "
		INSERT INTO `access_rights` (`id`, `user_id`, `resource_id`, `access`) VALUES
		(1, 1, 1, 1),
		(2, 1, 2, 1),
		(3, 1, 3, 1),
		(4, 1, 4, 1),
		(5, 1, 5, 1),
		(6, 1, 6, 1),
		(7, 1, 7, 1),
		(8, 1, 8, 1),
		(9, 1, 9, 1),
		(10, 1, 10, 1),
		(11, 1, 11, 1),
		(12, 1, 12, 1),
		(13, 1, 13, 1),
		(14, 1, 14, 1),
		(15, 1, 15, 1),
		(16, 1, 16, 1),
		(17, 1, 17, 1),
		(18, 1, 18, 1),
		(19, 1, 19, 1),
		(20, 1, 20, 1),
		(21, 1, 21, 1),
		(22, 1, 22, 1),
		(23, 1, 23, 1),
		(24, 1, 24, 1),
		(25, 1, 25, 1),
		(26, 1, 26, 1),
		(27, 1, 27, 1),
		(28, 1, 28, 1),
		(29, 1, 29, 1),
		(30, 1, 30, 1),
		(31, 1, 31, 1),
		(32, 1, 32, 1),
		(33, 1, 33, 1),
		(34, 1, 34, 1),
		(35, 1, 35, 1),
		(36, 1, 36, 1),
		(37, 1, 37, 1),
		(38, 1, 38, 1),
		(39, 1, 39, 1),
		(40, 1, 40, 1),
		(41, 1, 41, 1),
		(42, 1, 42, 1),
		(43, 1, 43, 1),
		(44, 1, 44, 1),
		(45, 1, 45, 1),
		(46, 1, 46, 1),
		(47, 1, 47, 1),
		(48, 1, 48, 1),
		(49, 1, 49, 1),
		(50, 1, 50, 1),
		(51, 1, 51, 1),
		(52, 1, 52, 1),
		(53, 1, 53, 1),
		(54, 1, 54, 1),
		(55, 1, 55, 1),
		(56, 1, 56, 1),
		(57, 1, 57, 1),
		(58, 1, 58, 1),
		(59, 1, 59, 1),
		(60, 1, 60, 1),
		(61, 1, 61, 1),
		(62, 1, 62, 1),
		(63, 1, 63, 1),
		(64, 1, 64, 1),
		(65, 1, 65, 1),
		(66, 1, 66, 1),
		(67, 1, 67, 1),
		(68, 1, 68, 1),
		(69, 1, 69, 1),
		(70, 1, 70, 1),
		(71, 1, 71, 1),
		(72, 1, 72, 1),
		(73, 1, 73, 1),
		(74, 1, 74, 1),
		(75, 1, 75, 1),
		(76, 1, 76, 1),
		(77, 1, 77, 1),
		(78, 1, 78, 1),
		(79, 1, 79, 1),
		(80, 1, 80, 1);
	";
	$statement = $db_connection->prepare($query);
	$statement->execute();

	//echo "Creating access_users...<br/>";
	$query = "
		CREATE TABLE `access_users` (
		  `id` int(11) UNSIGNED NOT NULL,
		  `user_id` int(11) UNSIGNED NOT NULL,
		  `module_id` int(11) UNSIGNED NOT NULL,
		  `access` tinyint(1) NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	";
	$statement = $db_connection->prepare($query);
	$statement->execute();
	//echo "Inserting access_users...<br/>";
	$query = "
		INSERT INTO `access_users` (`id`, `user_id`, `module_id`, `access`) VALUES
			(1, 1, 1, 1),
			(2, 1, 2, 1),
			(3, 1, 3, 1),
			(4, 1, 4, 1),
			(5, 1, 5, 1),
			(6, 1, 6, 1),
			(7, 1, 7, 1),
			(8, 1, 8, 1),
			(9, 1, 9, 1),
			(10, 1, 10, 1),
			(11, 1, 11, 1),
			(12, 1, 12, 1),
			(13, 1, 13, 1),
			(14, 1, 14, 1);
	";
	$statement = $db_connection->prepare($query);
	$statement->execute();

	//echo "Creating archives...<br/>";
	$query = "
		CREATE TABLE `archives` (
			`id` int(11) UNSIGNED NOT NULL,
			`page_id` int(11) UNSIGNED NOT NULL,
			`main_page` tinyint(1) NOT NULL,
			`contact_page` tinyint(1) NOT NULL,
			`category_id` int(11) UNSIGNED NOT NULL,
			`title` varchar(512) CHARACTER SET utf8 NOT NULL,
			`description` text CHARACTER SET utf8 NOT NULL,
			`contents` longtext CHARACTER SET utf8,
			`author_id` int(11) UNSIGNED NOT NULL,
			`visible` tinyint(1) NOT NULL,
			`modified` datetime NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;
	";
	$statement = $db_connection->prepare($query);
	$statement->execute();

	//echo "Inserting archives...<br/>";
	$query = "
		INSERT INTO `archives` (`id`, `page_id`, `main_page`, `contact_page`, `category_id`, `title`, `description`, `contents`, `author_id`, `visible`, `modified`) VALUES
			(1, 1, 1, 0, 0, 'Strona główna', 'Strona główna naszej aplikacji', '<div class=\"row\">\n<div class=\"col-lg-3\">\n<img src=\"public/img/AngularJS_logo_bordered.png\" class=\"img-responsive\" alt=\"angular js logo\">\n</div>\n<div class=\"col-lg-9\">\n<h2>Witaj w systemie Angular CMS!</h2>\n<h4>Wstęp</h4>\n<p>\nJest to projekt aplikacji typu Single Page Application, która działa w ten sposób, że zmiana treści odbywa się bez konieczności przeładowania całej strony. Wygląda to tak, że mamy szablon strony, na którym zmieniają się tylko fragmenty, pewne elementy, w zależności od kontekstu. Na przykład po wywołaniu strony głównej ładowana jest jedynie treść, nagłówek i stopka pozostają nie zmienione. Podobnie jest po otwarciu strony kontaktowej, gdzie jako treść pojawia się mapka, informacje kontaktowe i formularz kontaktowy. To samo ma miejsce po otwarciu dowolnej podstrony - ładowana jest tylko jej treść, reszta się nie zmienia. System jest oparty na frameworku AngularJS, który jest przeznaczony do tworzenia tego typu aplikacji. \n</p>\n<h4>Demo</h4>\n<p>Aby pokazać, jak wygląda zarządzanie stroną, przygotowane zostały następujące filmy demo:</p>\n<p>\n<a href=\"https://youtu.be/x92qVUeWr9k\" target=\"_blank\" class=\"btn btn-primary\">Tworzenie strony głównej</a>\n<a href=\"https://youtu.be/yl4FjIGSYYU\" target=\"_blank\" class=\"btn btn-primary\">Tworzenie strony kontaktowej</a>\n<a href=\"https://youtu.be/3wvaLt5X-V4\" target=\"_blank\" class=\"btn btn-primary\">Nawigacja, podstrony, obrazki i slidery</a>\n</p>\n</div>\n</div>', 1, 1, NOW()),
			(2, 2, 0, 1, 0, 'Strona kontaktowa', 'Strona kontaktowa naszej aplikacji', '<style type=\"text/css\">\ndiv.map { padding: 0px; }\ndiv.adr { padding: 7px 0px; }\ndiv.adr span.glyphicon { padding-right: 10px; }\n</style>\n\n<div class=\"map\">\n<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4867.701649478604!2d16.91226795315743!3d52.40937977092189!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0000000000000000%3A0x6201d17fe9c37f41!2sDZ+Bank+Polska+S.A.+Centrum+Bankowo%C5%9Bci+Korporacyjnej!5e0!3m2!1spl!2sus!4v1417506974043\" width=\"100%\" height=\"450\" frameborder=\"0\" style=\"border: #aaa 1px solid;\"></iframe>\n</div>\n\n<div class=\"adr\"><span class=\"glyphicon glyphicon-envelope\"></span><a href=\"mailto:andrzuk@tlen.pl\" class=\"social-link\">andrzuk@tlen.pl</a></div>\n<div class=\"adr\"><span class=\"glyphicon glyphicon-cloud\"></span><a href=\"https://twitter.com/andy_zukowski\" class=\"social-link\" target=\"_blank\">@andy_zukowski</a></div>\n<div class=\"adr\"><span class=\"glyphicon glyphicon-user\"></span><a href=\"https://www.facebook.com/zukowski.andrzej\" class=\"social-link\" target=\"_blank\">/zukowski.andrzej</a></div>', 1, 1, NOW()),
			(3, 3, 0, 0, 1, 'DEMO - Snake game', 'Snake Xenzia - game on-line for website guests', '<style>\ndiv#game-container { display: inline-flex; width: 1140px; }\ncanvas#game-canvas { width: 900px; height: 420px; background: #eee; border: 1px solid #999; }\nspan#game-panel { display: inline-block; width: 230px; height: 420px; border: 1px solid #999; background: #ddd; margin-left: 5px; }\nbutton.move-buttons { width: 50px; height: 50px; position: relative; font-size: 1.5em; margin: 5px 0; }\nbutton.play-buttons { width: 80px; position: relative; margin: 20px 0; }\nspan#score { font-weight: bold; font-size: 2.5em; margin-right: 15px; }\nspan#time-period { margin-right: 15px; }\np.separator { line-height: 20px; text-align: center; font-size: 0.95em; }\np.separator a#show-scores { line-height: 10px; }\ntable#scores-list { width: 100%; }\ntable#scores-list tbody tr th { padding: 0 10px 15px 10px; border-bottom: 1px solid #999; }\ntable#scores-list tbody tr td { padding: 0 10px; border-bottom: 1px dotted #ccc; }\ntable#scores-list tbody tr td.scores-saved { text-align: center; }\ntable#scores-list tbody tr td.scores-score { text-align: right; font-weight: bold; font-size: 1.2em; color: blue; }\n</style>\n\n<div id=\"game-container\">\n<canvas id=\"game-canvas\">Canvas nie jest obsługiwany przez tą przeglądarkę.</canvas>\n<span id=\"game-panel\">\n<table width=\"100%\">\n<tr>\n<td colspan=\"2\" style=\"text-align: center; font-size: 1.2em; font-weight: bold; color: #c00;\">\n<div id=\"player-caption\"></div>\n</td>\n</tr>\n<tr>\n<td style=\"text-align: left; padding-left: 15px;\"><b>Score:</b></td>\n<td style=\"text-align: right;\"><span id=\"score\">0</span></td>\n</tr>\n</table>\n<p class=\"separator\"><a id=\"show-scores\" href=\"#\">Scores List</a><hr style=\"margin: 0;\"></p>\n<table width=\"100%\">\n<tr>\n<td style=\"text-align: left; padding-left: 15px;\"><b>Speed:</b></td>\n<td style=\"text-align: right; padding-right: 15px;\">\n<span id=\"time-period\">\n<select id=\"period\" class=\"form-control\">\n<option value=\"100\">Super fast (20x)</option>\n<option value=\"200\">Very fast (10x)</option>\n<option value=\"300\" selected>Faster (5x)</option>\n<option value=\"400\">Little faster (4x)</option>\n<option value=\"500\">Medium (3x)</option>\n<option value=\"750\">Slow (2x)</option>\n<option value=\"1000\">Very slow (1x)</option>\n</select>\n</span>\n</td>\n</tr>\n</table>\n<table width=\"100%\">\n<tr>\n<td width=\"50%\" style=\"text-align: center;\"><button id=\"play-start\" class=\"play-buttons btn btn-success\">Play</button></td>\n<td width=\"50%\" style=\"text-align: center;\"><button id=\"play-pause\" class=\"play-buttons btn btn-danger\">Pause</button></td>\n</tr>\n</table>\n<table width=\"100%\">\n<tr>\n<td colspan=\"3\" style=\"text-align: center;\"><button id=\"move-up\" class=\"move-buttons btn btn-primary\">&#9650;</button></td>\n</tr>\n<tr>\n<td width=\"33%\" style=\"text-align: right;\"><button id=\"move-left\" class=\"move-buttons btn btn-primary\">&#9668;</button></td>\n<td width=\"34%\" style=\"text-align: center;\"><button id=\"move-down\" class=\"move-buttons btn btn-primary\">&#9660;</button></td>\n<td width=\"33%\" style=\"text-align: left;\"><button id=\"move-right\" class=\"move-buttons btn btn-primary\">&#9658;</button></td>\n</tr>\n</table>\n</span>\n</div>\n\n<div class=\"modal fade screen-centered\" id=\"player-modal\" role=\"dialog\">\n<div class=\"modal-dialog\" style=\"max-width: 300px;\">\n<div class=\"modal-content\">\n<div class=\"modal-header\">\n<button type=\"button\" class=\"close\" data-dismiss=\"modal\">&times;</button>\n<h4 class=\"modal-title\">Witaj!</h4>\n</div>\n<div class=\"modal-body\">\n<p>Podaj swoje imię:</p>\n<input type=\"text\" class=\"form-control\" id=\"player-name\">\n</div>\n<div class=\"modal-footer\">\n<button type=\"button\" id=\"save-player-name\" class=\"btn btn-success\" data-dismiss=\"modal\">Zapisz</button>\n<button type=\"button\" id=\"cancel-player-name\" class=\"btn btn-warning\" data-dismiss=\"modal\">Anuluj</button>\n</div>\n</div>\n</div>\n</div>\n\n<div class=\"modal fade\" id=\"scores-modal\" role=\"dialog\">\n<div class=\"modal-dialog\">\n<div class=\"modal-content\">\n<div class=\"modal-header\">\n<button type=\"button\" class=\"close\" data-dismiss=\"modal\">&times;</button>\n<h4 class=\"modal-title\">Najlepsze wyniki (Top 20)</h4>\n</div>\n<div class=\"modal-body\">\n<table id=\"scores-list\" align=\"center\">\n<tbody>\n<tr><td style=\"text-align: center;\"><img src=\"public/img/loader.gif\"></td></tr>\n</tbody>\n</table>\n</div>\n<div class=\"modal-footer\">\n<button type=\"button\" class=\"btn btn-warning\" data-dismiss=\"modal\">Zamknij</button>\n</div>\n</div>\n</div>\n</div>\n\n<script src=\"public/js/snake.js\"></script>\n<script>\ngameInterval = null;\n</script>', 1, 1, NOW()),
			(4, 4, 0, 0, 1, 'DEMO - Tetris game', 'Tetris - game on-line for website guests', '<style>\n	div#game-information { margin: 5px; text-align: center; }\n	div#game-information > span { padding: 5px; margin: 0 10px; width: 100px; height: 20px; color: #555; }\n	div#game-information > span#record { color: #090; font-size: 1.1em; font-weight: bold; }\n	div#game-information > span#date { color: #090; font-size: 0.85em; display: none; }\n	div#game-container { margin: 20px; text-align: center; }\n	canvas#game-canvas { background: url(public/img/tetris.jpg) no-repeat center; }\n	span#panel-left, span#panel-right { display: inline-block; padding: 0 10px; position: fixed; vertical-align: top; top: 150px; }\n	span#panel-left { left: 10px; } \n	span#panel-right { right: 10px; } \n	button.game-button { width: 70px; }\n	button#start { width: 100px; }\n	div#game-control { margin: 0; padding: 0; text-align: center; }\n	div#game-message { font-size: 1.0em; font-weight: bold; color: #090; }\n	div#game-button { padding: 10px; display: block; }\n	canvas#game-canvas { width: 400px; height: 500px; border: 1px solid #aaa; padding: 1px; }\n	div#game-legend { display: block; position: fixed; width: 200px; bottom: 100px; right: 50px; font-size: 0.75em; }\n	@media (max-width: 968px) {\n		div#game-legend { display: none; }\n	}\n	div#editor-button, div#close-button { position: fixed; top: 80px; right: 20px; }\n	div#game-map { display: none; }\n	div#game-map > h4 { text-align: center; }\n	button.map-element { width: 30px; height: 30px; margin: 1px; padding: 1px; }\n	table#maps-edit { max-width: 500px; }\n	div#alert-info, div#alert-danger { display: none; text-align: center; margin: 10px 20% 0 20%; }\n	div#maps-title { margin: 0 0 30px 0; text-align: center; font-size: 1.2em; font-weight: bold; color: #369; border-bottom: 1px solid #369; }\n	div#maps-container { text-align: center; margin: 10px 50px; }\n	div.item-container { display: inline-block; margin: 4px; width: 150px; height: 220px; border: 1px solid #ccc; background: #fff; }\n	div.lp, div.map, div.color, div.actions { margin: 10px; text-align: center; }\n	div.lp { margin: 0; font-size: 0.75em; }\n</style>\n\n<div id=\"game-play\">\n	<div id=\"editor-button\"><button id=\"map-editor\" class=\"btn btn-info\">Map editor</button></div>\n\n	<div id=\"game-container\">\n		<span id=\"panel-left\">\n			<hr>\n			<button id=\"rotate-left\" class=\"btn btn-warning game-button\"><i class=\"fa fa-undo\" aria-hidden=\"true\"></i></button>\n			<hr>\n			<button id=\"move-left\" class=\"btn btn-success game-button\"><i class=\"fa fa-arrow-left\" aria-hidden=\"true\"></i></button>\n			<hr>\n			<button id=\"drop-left\" class=\"btn btn-danger game-button drop-down\"><i class=\"fa fa-level-down\" aria-hidden=\"true\"></i></button>\n		</span>\n		<canvas id=\"game-canvas\">Canvas is not supported in your browser.</canvas>\n		<span id=\"panel-right\">\n			<hr>\n			<button id=\"rotate-right\" class=\"btn btn-warning game-button\"><i class=\"fa fa-repeat\" aria-hidden=\"true\"></i></button>\n			<hr>\n			<button id=\"move-right\" class=\"btn btn-success game-button\"><i class=\"fa fa-arrow-right\" aria-hidden=\"true\"></i></button>\n			<hr>\n			<button id=\"drop-right\" class=\"btn btn-danger game-button drop-down\"><i class=\"fa fa-level-down\" aria-hidden=\"true\"></i></button>\n		</span>\n	</div>\n\n	<div id=\"game-information\">\n		<span id=\"position\"></span>\n		<span id=\"blocks\"></span>\n		<span id=\"level\"></span>\n		<span id=\"scores\"></span>\n		<span id=\"time\"></span>\n		<span id=\"record\"></span>\n		<span id=\"date\"></span>\n	</div>\n\n	<div id=\"game-control\">\n		<div id=\"game-message\">Aby rozpocząć grę, naciśnij przycisk...</div>\n		<div id=\"game-button\"><button id=\"start\" class=\"btn btn-success game-button\">Start</button></div>\n	</div>\n\n	<div id=\"game-legend\">\n		<b><u>Keyboard control:</u></b><br><br>\n		Rotation: <b>PageUp</b>, <b>PageDown</b><br>Move: <b>Left</b>, <b>Right</b>, <b>Down</b><br>Drop: <b>Space Bar</b>\n	</div>\n</div>\n\n<div id=\"game-map\">\n	<div id=\"close-button\"><button id=\"save-maps\" class=\"btn btn-success\">Save &amp; exit</button>&nbsp;<button id=\"close-editor\" class=\"btn btn-danger\">Close</button></div>			\n	<div id=\"maps-title\">Tetris Maps Editor</div>\n	<div id=\"maps-table\"></div>\n	<div class=\"alert alert-info\" id=\"alert-info\"></div>\n	<div class=\"alert alert-danger\" id=\"alert-danger\"></div>\n</div>\n\n<script src=\"public/js/tetris.js\"></script>\n\n<script>\n/*\n	shapes.push({ map: [[1, 1, 0], [0, 1, 0], [0, 1, 1]], color: \'#009900\' });\n	shapes.push({ map: [[0, 1, 1], [0, 1, 0], [1, 1, 0]], color: \'#0080c0\' });\n	mapEditor.loadMaps();\n*/\n</script>', 1, 1, NOW());
	";
	$statement = $db_connection->prepare($query);
	$statement->execute();

	//echo "Creating categories...<br/>";
	$query = "
		CREATE TABLE `categories` (
			`id` int(11) unsigned NOT NULL,
			`parent_id` int(11) unsigned NOT NULL,
			`item_order` int(11) NOT NULL,
			`caption` varchar(128) CHARACTER SET utf8 NOT NULL,
			`item_link` varchar(1024) CHARACTER SET utf8 NOT NULL,
			`visible` tinyint(1) NOT NULL,
			`target` tinyint(1) NOT NULL,
			`modified` datetime NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;
	";
	$statement = $db_connection->prepare($query);
	$statement->execute();

	//echo "Inserting categories...<br/>";
	$query = "
		INSERT INTO `categories` (`id`, `parent_id`, `item_order`, `caption`, `item_link`, `visible`, `target`, `modified`) VALUES
			(1, 0, 1, 'DEMO', '/category/1', 1, 0, NOW());
	";
	$statement = $db_connection->prepare($query);
	$statement->execute();

	//echo "Creating images...<br/>";
	$query = "
		CREATE TABLE `hosts` (
			`id` int(11) UNSIGNED NOT NULL,
			`server_ip` varchar(20) NOT NULL,
			`server_name` varchar(256) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	";
	$statement = $db_connection->prepare($query);
	$statement->execute();

	//echo "Inserting images...<br/>";
	$query = "
		CREATE TABLE `images` (
			`id` int(11) unsigned NOT NULL,
			`owner_id` int(11) unsigned NOT NULL,
			`file_format` varchar(32) NOT NULL,
			`file_name` varchar(512) NOT NULL,
			`file_size` int(11) NOT NULL,
			`picture_width` int(11) NOT NULL,
			`picture_height` int(11) NOT NULL,
			`modified` datetime NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	";
	$statement = $db_connection->prepare($query);
	$statement->execute();

	$query = "
		INSERT INTO `images` (`id`, `owner_id`, `file_format`, `file_name`, `file_size`, `picture_width`, `picture_height`, `modified`) VALUES
			(1, 1, 'image/png', 'Animal Tales.png', 35907, 1200, 400, NOW()),
			(2, 1, 'image/jpeg', 'Blue Ribbon.jpg', 125856, 1200, 400, NOW()),
			(3, 1, 'image/jpeg', 'chi health.jpg', 47931, 1200, 400, NOW()),
			(4, 1, 'image/jpeg', 'DA Defence.jpg', 65506, 1200, 400, NOW()),
			(5, 1, 'image/png', 'IBS.png', 77105, 1200, 400, NOW()),
			(6, 1, 'image/jpeg', 'LAGB.jpg', 55275, 1200, 400, NOW()),
			(7, 1, 'image/jpeg', 'logo TANSS.jpg', 58555, 1200, 400, NOW()),
			(8, 1, 'image/jpeg', 'mike delta aviation.jpg', 85864, 1200, 400, NOW()),
			(9, 1, 'image/png', 'NALED.png', 65907, 1200, 400, NOW()),
			(10, 1, 'image/png', 'Oasis4he.png', 18762, 1200, 400, NOW()),
			(11, 1, 'image/png', 'PEPANZ.png', 44828, 1200, 400, NOW()),
			(12, 1, 'image/png', 'SprintGround Logo Light.png', 119031, 1200, 400, NOW()),
			(13, 1, 'image/jpeg', 'support intro.jpg', 32373, 1200, 400, NOW());
	";
	$statement = $db_connection->prepare($query);
	$statement->execute();

	//echo "Creating logins...<br/>";
	$query = "
		CREATE TABLE `logins` (
			`id` int(11) UNSIGNED NOT NULL,
			`agent` varchar(256) DEFAULT NULL,
			`user_ip` varchar(20) NOT NULL,
			`user_id` int(11) UNSIGNED NOT NULL,
			`login` varchar(128) NOT NULL,
			`password` varchar(128) NOT NULL,
			`token` varchar(128) DEFAULT NULL,
			`login_time` datetime NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	";
	$statement = $db_connection->prepare($query);
	$statement->execute();

	//echo "Inserting logins...<br/>";
	$query = "
		CREATE TABLE `messages` (
			`id` int(11) unsigned NOT NULL,
			`client_ip` varchar(20) NOT NULL,
			`client_name` varchar(128) NOT NULL,
			`client_email` varchar(256) NOT NULL,
			`message_content` longtext NOT NULL,
			`requested` tinyint(1) NOT NULL,
			`send_date` datetime NOT NULL,
			`close_date` datetime NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	";
	$statement = $db_connection->prepare($query);
	$statement->execute();

	//echo "Creating pages...<br/>";
	$query = "
		CREATE TABLE `pages` (
			`id` int(11) unsigned NOT NULL,
			`main_page` tinyint(1) NOT NULL,
			`contact_page` tinyint(1) NOT NULL,
			`category_id` int(11) unsigned NOT NULL,
			`title` varchar(512) CHARACTER SET utf8 NOT NULL,
			`description` text CHARACTER SET utf8 NOT NULL,
			`contents` longtext CHARACTER SET utf8,
			`author_id` int(11) unsigned NOT NULL,
			`visible` tinyint(1) NOT NULL,
			`modified` datetime NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;
	";
	$statement = $db_connection->prepare($query);
	$statement->execute();

	//echo "Inserting pages...<br/>";
	$query = "
		INSERT INTO `pages` (`id`, `main_page`, `contact_page`, `category_id`, `title`, `description`, `contents`, `author_id`, `visible`, `modified`) VALUES
			(1, 1, 0, 0, 'Strona główna', 'Strona główna naszej aplikacji', '<div class=\"row\">\n<div class=\"col-lg-3\">\n<img src=\"public/img/AngularJS_logo_bordered.png\" class=\"img-responsive\" alt=\"angular js logo\">\n</div>\n<div class=\"col-lg-9\">\n<h2>Witaj w systemie Angular CMS!</h2>\n<h4>Wstęp</h4>\n<p>\nJest to projekt aplikacji typu Single Page Application, która działa w ten sposób, że zmiana treści odbywa się bez konieczności przeładowania całej strony. Wygląda to tak, że mamy szablon strony, na którym zmieniają się tylko fragmenty, pewne elementy, w zależności od kontekstu. Na przykład po wywołaniu strony głównej ładowana jest jedynie treść, nagłówek i stopka pozostają nie zmienione. Podobnie jest po otwarciu strony kontaktowej, gdzie jako treść pojawia się mapka, informacje kontaktowe i formularz kontaktowy. To samo ma miejsce po otwarciu dowolnej podstrony - ładowana jest tylko jej treść, reszta się nie zmienia. System jest oparty na frameworku AngularJS, który jest przeznaczony do tworzenia tego typu aplikacji. \n</p>\n<h4>Demo</h4>\n<p>Aby pokazać, jak wygląda zarządzanie stroną, przygotowane zostały następujące filmy demo:</p>\n<p>\n<a href=\"https://youtu.be/x92qVUeWr9k\" target=\"_blank\" class=\"btn btn-primary\">Tworzenie strony głównej</a>\n<a href=\"https://youtu.be/yl4FjIGSYYU\" target=\"_blank\" class=\"btn btn-primary\">Tworzenie strony kontaktowej</a>\n<a href=\"https://youtu.be/3wvaLt5X-V4\" target=\"_blank\" class=\"btn btn-primary\">Nawigacja, podstrony, obrazki i slidery</a>\n</p>\n</div>\n</div>', 1, 1, NOW()),
			(2, 0, 1, 0, 'Strona kontaktowa', 'Strona kontaktowa naszej aplikacji', '<style type=\"text/css\">\ndiv.map { padding: 0px; }\ndiv.adr { padding: 7px 0px; }\ndiv.adr span.glyphicon { padding-right: 10px; }\n</style>\n\n<div class=\"map\">\n<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4867.701649478604!2d16.91226795315743!3d52.40937977092189!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0000000000000000%3A0x6201d17fe9c37f41!2sDZ+Bank+Polska+S.A.+Centrum+Bankowo%C5%9Bci+Korporacyjnej!5e0!3m2!1spl!2sus!4v1417506974043\" width=\"100%\" height=\"450\" frameborder=\"0\" style=\"border: #aaa 1px solid;\"></iframe>\n</div>\n\n<div class=\"adr\"><span class=\"glyphicon glyphicon-envelope\"></span><a href=\"mailto:andrzuk@tlen.pl\" class=\"social-link\">andrzuk@tlen.pl</a></div>\n<div class=\"adr\"><span class=\"glyphicon glyphicon-cloud\"></span><a href=\"https://twitter.com/andy_zukowski\" class=\"social-link\" target=\"_blank\">@andy_zukowski</a></div>\n<div class=\"adr\"><span class=\"glyphicon glyphicon-user\"></span><a href=\"https://www.facebook.com/zukowski.andrzej\" class=\"social-link\" target=\"_blank\">/zukowski.andrzej</a></div>', 1, 1, NOW()),
			(3, 0, 0, 1, 'DEMO - Snake game', 'Snake Xenzia - game on-line for website guests', '<style>\ndiv#game-container { display: inline-flex; width: 1140px; }\ncanvas#game-canvas { width: 900px; height: 420px; background: #eee; border: 1px solid #999; }\nspan#game-panel { display: inline-block; width: 230px; height: 420px; border: 1px solid #999; background: #ddd; margin-left: 5px; }\nbutton.move-buttons { width: 50px; height: 50px; position: relative; font-size: 1.5em; margin: 5px 0; }\nbutton.play-buttons { width: 80px; position: relative; margin: 20px 0; }\nspan#score { font-weight: bold; font-size: 2.5em; margin-right: 15px; }\nspan#time-period { margin-right: 15px; }\np.separator { line-height: 20px; text-align: center; font-size: 0.95em; }\np.separator a#show-scores { line-height: 10px; }\ntable#scores-list { width: 100%; }\ntable#scores-list tbody tr th { padding: 0 10px 15px 10px; border-bottom: 1px solid #999; }\ntable#scores-list tbody tr td { padding: 0 10px; border-bottom: 1px dotted #ccc; }\ntable#scores-list tbody tr td.scores-saved { text-align: center; }\ntable#scores-list tbody tr td.scores-score { text-align: right; font-weight: bold; font-size: 1.2em; color: blue; }\n</style>\n\n<div id=\"game-container\">\n<canvas id=\"game-canvas\">Canvas nie jest obsługiwany przez tą przeglądarkę.</canvas>\n<span id=\"game-panel\">\n<table width=\"100%\">\n<tr>\n<td colspan=\"2\" style=\"text-align: center; font-size: 1.2em; font-weight: bold; color: #c00;\">\n<div id=\"player-caption\"></div>\n</td>\n</tr>\n<tr>\n<td style=\"text-align: left; padding-left: 15px;\"><b>Score:</b></td>\n<td style=\"text-align: right;\"><span id=\"score\">0</span></td>\n</tr>\n</table>\n<p class=\"separator\"><a id=\"show-scores\" href=\"#\">Scores List</a><hr style=\"margin: 0;\"></p>\n<table width=\"100%\">\n<tr>\n<td style=\"text-align: left; padding-left: 15px;\"><b>Speed:</b></td>\n<td style=\"text-align: right; padding-right: 15px;\">\n<span id=\"time-period\">\n<select id=\"period\" class=\"form-control\">\n<option value=\"100\">Super fast (20x)</option>\n<option value=\"200\">Very fast (10x)</option>\n<option value=\"300\" selected>Faster (5x)</option>\n<option value=\"400\">Little faster (4x)</option>\n<option value=\"500\">Medium (3x)</option>\n<option value=\"750\">Slow (2x)</option>\n<option value=\"1000\">Very slow (1x)</option>\n</select>\n</span>\n</td>\n</tr>\n</table>\n<table width=\"100%\">\n<tr>\n<td width=\"50%\" style=\"text-align: center;\"><button id=\"play-start\" class=\"play-buttons btn btn-success\">Play</button></td>\n<td width=\"50%\" style=\"text-align: center;\"><button id=\"play-pause\" class=\"play-buttons btn btn-danger\">Pause</button></td>\n</tr>\n</table>\n<table width=\"100%\">\n<tr>\n<td colspan=\"3\" style=\"text-align: center;\"><button id=\"move-up\" class=\"move-buttons btn btn-primary\">&#9650;</button></td>\n</tr>\n<tr>\n<td width=\"33%\" style=\"text-align: right;\"><button id=\"move-left\" class=\"move-buttons btn btn-primary\">&#9668;</button></td>\n<td width=\"34%\" style=\"text-align: center;\"><button id=\"move-down\" class=\"move-buttons btn btn-primary\">&#9660;</button></td>\n<td width=\"33%\" style=\"text-align: left;\"><button id=\"move-right\" class=\"move-buttons btn btn-primary\">&#9658;</button></td>\n</tr>\n</table>\n</span>\n</div>\n\n<div class=\"modal fade screen-centered\" id=\"player-modal\" role=\"dialog\">\n<div class=\"modal-dialog\" style=\"max-width: 300px;\">\n<div class=\"modal-content\">\n<div class=\"modal-header\">\n<button type=\"button\" class=\"close\" data-dismiss=\"modal\">&times;</button>\n<h4 class=\"modal-title\">Witaj!</h4>\n</div>\n<div class=\"modal-body\">\n<p>Podaj swoje imię:</p>\n<input type=\"text\" class=\"form-control\" id=\"player-name\">\n</div>\n<div class=\"modal-footer\">\n<button type=\"button\" id=\"save-player-name\" class=\"btn btn-success\" data-dismiss=\"modal\">Zapisz</button>\n<button type=\"button\" id=\"cancel-player-name\" class=\"btn btn-warning\" data-dismiss=\"modal\">Anuluj</button>\n</div>\n</div>\n</div>\n</div>\n\n<div class=\"modal fade\" id=\"scores-modal\" role=\"dialog\">\n<div class=\"modal-dialog\">\n<div class=\"modal-content\">\n<div class=\"modal-header\">\n<button type=\"button\" class=\"close\" data-dismiss=\"modal\">&times;</button>\n<h4 class=\"modal-title\">Najlepsze wyniki (Top 20)</h4>\n</div>\n<div class=\"modal-body\">\n<table id=\"scores-list\" align=\"center\">\n<tbody>\n<tr><td style=\"text-align: center;\"><img src=\"public/img/loader.gif\"></td></tr>\n</tbody>\n</table>\n</div>\n<div class=\"modal-footer\">\n<button type=\"button\" class=\"btn btn-warning\" data-dismiss=\"modal\">Zamknij</button>\n</div>\n</div>\n</div>\n</div>\n\n<script src=\"public/js/snake.js\"></script>\n<script>\ngameInterval = null;\n</script>', 1, 1, NOW()),
			(4, 0, 0, 1, 'DEMO - Tetris game', 'Tetris - game on-line for website guests', '<style>\n	div#game-information { margin: 5px; text-align: center; }\n	div#game-information > span { padding: 5px; margin: 0 10px; width: 100px; height: 20px; color: #555; }\n	div#game-information > span#record { color: #090; font-size: 1.1em; font-weight: bold; }\n	div#game-information > span#date { color: #090; font-size: 0.85em; display: none; }\n	div#game-container { margin: 20px; text-align: center; }\n	canvas#game-canvas { background: url(public/img/tetris.jpg) no-repeat center; }\n	span#panel-left, span#panel-right { display: inline-block; padding: 0 10px; position: fixed; vertical-align: top; top: 150px; }\n	span#panel-left { left: 10px; } \n	span#panel-right { right: 10px; } \n	button.game-button { width: 70px; }\n	button#start { width: 100px; }\n	div#game-control { margin: 0; padding: 0; text-align: center; }\n	div#game-message { font-size: 1.0em; font-weight: bold; color: #090; }\n	div#game-button { padding: 10px; display: block; }\n	canvas#game-canvas { width: 400px; height: 500px; border: 1px solid #aaa; padding: 1px; }\n	div#game-legend { display: block; position: fixed; width: 200px; bottom: 100px; right: 50px; font-size: 0.75em; }\n	@media (max-width: 968px) {\n		div#game-legend { display: none; }\n	}\n	div#editor-button, div#close-button { position: fixed; top: 80px; right: 20px; }\n	div#game-map { display: none; }\n	div#game-map > h4 { text-align: center; }\n	button.map-element { width: 30px; height: 30px; margin: 1px; padding: 1px; }\n	table#maps-edit { max-width: 500px; }\n	div#alert-info, div#alert-danger { display: none; text-align: center; margin: 10px 20% 0 20%; }\n	div#maps-title { margin: 0 0 30px 0; text-align: center; font-size: 1.2em; font-weight: bold; color: #369; border-bottom: 1px solid #369; }\n	div#maps-container { text-align: center; margin: 10px 50px; }\n	div.item-container { display: inline-block; margin: 4px; width: 150px; height: 220px; border: 1px solid #ccc; background: #fff; }\n	div.lp, div.map, div.color, div.actions { margin: 10px; text-align: center; }\n	div.lp { margin: 0; font-size: 0.75em; }\n</style>\n\n<div id=\"game-play\">\n	<div id=\"editor-button\"><button id=\"map-editor\" class=\"btn btn-info\">Map editor</button></div>\n\n	<div id=\"game-container\">\n		<span id=\"panel-left\">\n			<hr>\n			<button id=\"rotate-left\" class=\"btn btn-warning game-button\"><i class=\"fa fa-undo\" aria-hidden=\"true\"></i></button>\n			<hr>\n			<button id=\"move-left\" class=\"btn btn-success game-button\"><i class=\"fa fa-arrow-left\" aria-hidden=\"true\"></i></button>\n			<hr>\n			<button id=\"drop-left\" class=\"btn btn-danger game-button drop-down\"><i class=\"fa fa-level-down\" aria-hidden=\"true\"></i></button>\n		</span>\n		<canvas id=\"game-canvas\">Canvas is not supported in your browser.</canvas>\n		<span id=\"panel-right\">\n			<hr>\n			<button id=\"rotate-right\" class=\"btn btn-warning game-button\"><i class=\"fa fa-repeat\" aria-hidden=\"true\"></i></button>\n			<hr>\n			<button id=\"move-right\" class=\"btn btn-success game-button\"><i class=\"fa fa-arrow-right\" aria-hidden=\"true\"></i></button>\n			<hr>\n			<button id=\"drop-right\" class=\"btn btn-danger game-button drop-down\"><i class=\"fa fa-level-down\" aria-hidden=\"true\"></i></button>\n		</span>\n	</div>\n\n	<div id=\"game-information\">\n		<span id=\"position\"></span>\n		<span id=\"blocks\"></span>\n		<span id=\"level\"></span>\n		<span id=\"scores\"></span>\n		<span id=\"time\"></span>\n		<span id=\"record\"></span>\n		<span id=\"date\"></span>\n	</div>\n\n	<div id=\"game-control\">\n		<div id=\"game-message\">Aby rozpocząć grę, naciśnij przycisk...</div>\n		<div id=\"game-button\"><button id=\"start\" class=\"btn btn-success game-button\">Start</button></div>\n	</div>\n\n	<div id=\"game-legend\">\n		<b><u>Keyboard control:</u></b><br><br>\n		Rotation: <b>PageUp</b>, <b>PageDown</b><br>Move: <b>Left</b>, <b>Right</b>, <b>Down</b><br>Drop: <b>Space Bar</b>\n	</div>\n</div>\n\n<div id=\"game-map\">\n	<div id=\"close-button\"><button id=\"save-maps\" class=\"btn btn-success\">Save &amp; exit</button>&nbsp;<button id=\"close-editor\" class=\"btn btn-danger\">Close</button></div>			\n	<div id=\"maps-title\">Tetris Maps Editor</div>\n	<div id=\"maps-table\"></div>\n	<div class=\"alert alert-info\" id=\"alert-info\"></div>\n	<div class=\"alert alert-danger\" id=\"alert-danger\"></div>\n</div>\n\n<script src=\"public/js/tetris.js\"></script>\n\n<script>\n/*\n	shapes.push({ map: [[1, 1, 0], [0, 1, 0], [0, 1, 1]], color: \'#009900\' });\n	shapes.push({ map: [[0, 1, 1], [0, 1, 0], [1, 1, 0]], color: \'#0080c0\' });\n	mapEditor.loadMaps();\n*/\n</script>', 1, 1, NOW());
	";
	$statement = $db_connection->prepare($query);
	$statement->execute();

	//echo "Creating roles...<br/>";
	$query = "
		CREATE TABLE `roles` (
			`id` int(11) unsigned NOT NULL,
			`name` varchar(16) NOT NULL,
			`mask_a` tinyint(1) NOT NULL,
			`mask_o` tinyint(1) NOT NULL,
			`mask_u` tinyint(1) NOT NULL,
			`mask_g` tinyint(1) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	";
	$statement = $db_connection->prepare($query);
	$statement->execute();

	//echo "Inserting roles...<br/>";
	$query = "
		INSERT INTO `roles` (`id`, `name`, `mask_a`, `mask_o`, `mask_u`, `mask_g`) VALUES
			(1, 'admin', 1, 0, 0, 0),
			(2, 'operator', 0, 1, 0, 0),
			(3, 'user', 0, 0, 1, 0),
			(4, 'guest', 0, 0, 0, 1);
	";
	$statement = $db_connection->prepare($query);
	$statement->execute();

	$query = "
		CREATE TABLE `searches` (
			`id` int(11) unsigned NOT NULL,
			`user_ip` varchar(20) NOT NULL,
			`search_text` text CHARACTER SET utf8 NOT NULL,
			`results` int(11) NOT NULL,
			`search_date` datetime NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	";
	$statement = $db_connection->prepare($query);
	$statement->execute();

	//echo "Creating settings...<br/>";
	$query = "
		CREATE TABLE `settings` (
			`id` int(11) unsigned NOT NULL,
			`key_name` varchar(64) NOT NULL,
			`key_value` text NOT NULL,
			`meaning` varchar(128) DEFAULT NULL,
			`modified` datetime NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	";
	$statement = $db_connection->prepare($query);
	$statement->execute();

	//echo "Inserting settings...<br/>";
	$query = "
		INSERT INTO `settings` (`id`, `key_name`, `key_value`, `meaning`, `modified`) VALUES
			(1, 'app_title', :title, 'meta title dla strony', NOW()),
			(2, 'app_suffix', :suffix, 'druga część meta title dla strony', NOW()),
			(3, 'app_description', :description, 'meta description dla strony', NOW()),
			(4, 'app_logoicon', 'public/img/angularjs-logo.png', 'ikona logo w nagłówku strony', NOW()),
			(5, 'app_brand', :brand, 'tytuł aplikacji obok logo w nagłówku strony', NOW()),
			(6, 'app_keywords', :keywords, 'meta keywords dla strony', NOW()),
			(7, 'app_author', 'Andrzej Żukowski &copy; 2016', 'autor aplikacji internetowej', NOW()),
			(8, 'app_domain', :domain, 'domena internetowa serwisu', NOW()),
			(9, 'app_footer', '<a href=\"http://swoja-strona.eu\" target=\"_blank\" class=\"footer-link\">&copy; 2016 MyCMS</a>', 'treść stopki serwisu', NOW()),
			(10, 'list_rows_per_page', '[\n{\"module\": \"settings\", \"lines\": 10},\n{\"module\": \"categories\", \"lines\": 13},\n{\"module\": \"pages\", \"lines\": 8},\n{\"module\": \"images\", \"lines\": 6},\n{\"module\": \"gallery\", \"lines\": 21},\n{\"module\": \"users\", \"lines\": 13},\n{\"module\": \"access_levels\", \"lines\": 13},\n{\"module\": \"messages\", \"lines\": 10},\n{\"module\": \"visitors\", \"lines\": 13},\n{\"module\": \"logins\", \"lines\": 13},\n{\"module\": \"searches\", \"lines\": 15},\n{\"module\": \"games\", \"lines\": 15}\n]', 'liczba wierszy na stronę na listach systemowych', NOW()),
			(11, 'paginator_pointer_band', '5', 'zakres wskaźników paginatora na każdą stronę', NOW()),
			(12, 'visitors_excluded', '''192.168.0.1'', ''192.168.0.100''', 'Adresy IP wykluczone z wyświetlania w raporcie odwiedzin', NOW()),
			(13, 'visitors_period', '-180 days', 'Liczba ostatnich dni wczytywanych do raportu odwiedzin', NOW()),
			(14, 'visitors_referer', 'http', 'Filtr HTTP Referer przeglądarki użytkownika w raporcie odwiedzin', NOW()),
			(15, 'carousel_images', '[\n{\"image\": \"1\", \"text\": \"Angular CMS slider\"}, \n{\"image\": \"2\", \"text\": \"Angular CMS slider\"},\n{\"image\": \"3\", \"text\": \"Angular CMS slider\"}, \n{\"image\": \"4\", \"text\": \"Angular CMS slider\"}, \n{\"image\": \"5\", \"text\": \"Angular CMS slider\"}, \n{\"image\": \"6\", \"text\": \"Angular CMS slider\"}, \n{\"image\": \"7\", \"text\": \"Angular CMS slider\"}, \n{\"image\": \"8\", \"text\": \"Angular CMS slider\"}, \n{\"image\": \"9\", \"text\": \"Angular CMS slider\"}, \n{\"image\": \"10\", \"text\": \"Angular CMS slider\"}, \n{\"image\": \"11\", \"text\": \"Angular CMS slider\"}, \n{\"image\": \"12\", \"text\": \"Angular CMS slider\"}, \n{\"image\": \"13\", \"text\": \"Angular CMS slider\"} \n]', 'indeksy obrazków z galerii, które będą pokazywane w karuzeli slajdów', NOW()),
			(16, 'carousel_interval', '5000', 'interwał czasowy wyświetlania obrazków w karuzeli slajdów', NOW()),
			(17, 'carousel_index_enabled', 'true', 'czy ma być włączona karuzela slajdów na stronie głównej', NOW()),
			(18, 'carousel_page_enabled', 'true', 'czy ma być włączona karuzela slajdów na podstronie', NOW()),
			(19, 'carousel_category_enabled', 'true', 'czy ma być włączona karuzela slajdów na kategorii', NOW()),
			(20, 'carousel_contact_enabled', 'true', 'czy ma być włączona karuzela slajdów na stronie kontaktowej', NOW()),
			(21, 'carousel_search_enabled', 'true', 'czy ma być włączona karuzela slajdów na stronie wyszukiwania', NOW()),
			(22, 'send_new_message_report', 'true', 'wysyłanie e-mailem raportów o pojawieniu się nowej wiadomości', NOW()),
			(23, 'email_host', 'mail.domain.pl', 'host wysyłania maili', NOW()),
			(24, 'email_port', '587', 'port smtp', NOW()),
			(25, 'email_password', '', 'hasło konta mailingowego', NOW()),
			(26, 'email_sender_name', 'Mail Manager', 'nazwa konta e-mailowego serwisu', NOW()),
			(27, 'email_sender_address', 'sender@domain.pl', 'adres konta e-mailowego serwisu', NOW()),
			(28, 'email_admin_name', :admin_name, 'nazwa konta e-mailowego administratora serwisu', NOW()),
			(29, 'email_admin_address', :admin_email, 'adres e-mail administratora serwisu', NOW()),
			(30, 'email_report_address', :admin_email, 'adres e-mail odbiorcy raportów', NOW()),
			(31, 'email_report_subject', 'Raport serwisu', 'temat maila raportującego zdarzenie', NOW()),
			(32, 'email_report_body', 'Raport o zdarzeniu w serwisie', 'treść maila rapotującego', NOW()),
			(33, 'email_password_subject', 'Nowe hasło do konta', 'temat generowanego maila z nowym hasłem', NOW()),
			(34, 'email_password_body', 'Na Twoją prośbę przesyłamy Ci nowe hasło logowania. Oto Twoje dane:', 'treść generowanego maila z nowym hasłem', NOW()),
            (35, 'sidebar_index_enabled', 'true', 'czy ma być włączona sidebar na stronie głównej', NOW()),
			(36, 'sidebar_page_enabled', 'true', 'czy ma być włączona sidebar na podstronie', NOW()),
			(37, 'sidebar_category_enabled', 'true', 'czy ma być włączona sidebar na kategorii', NOW()),
			(38, 'sidebar_contact_enabled', 'true', 'czy ma być włączona sidebar na stronie kontaktowej', NOW()),
			(39, 'sidebar_search_enabled', 'true', 'czy ma być włączona sidebar na stronie wyszukiwania', NOW()),
            (40, 'menu_sidebar_enabled', 'true', 'czy ma być włączone menu głowe z kategoriami na sidebarze', NOW()),
            (41, 'submenu_sidebar_enabled', 'true', 'czy ma być włączone podmenu (submenu) na sidebarze', NOW()),
            (42, 'menu_navbar_enabled', 'true', 'czy ma być włączone menu na listwie nawigacyjnej', NOW()),
            (42, 'submenu_navbar_enabled', 'true', 'czy ma być włączone podmenu (submenu) na listwie nawigacyjnej', NOW());
	";
	$statement = $db_connection->prepare($query);
	$statement->bindParam(':brand', $brand, PDO::PARAM_STR);
	$statement->bindParam(':title', $brand, PDO::PARAM_STR);
	$statement->bindParam(':suffix', $brand, PDO::PARAM_STR);
	$statement->bindParam(':description', $description, PDO::PARAM_STR);
	$statement->bindParam(':keywords', $keywords, PDO::PARAM_STR);
	$statement->bindParam(':domain', $domain, PDO::PARAM_STR);
	$statement->bindParam(':admin_name', $admin_name, PDO::PARAM_STR);
	$statement->bindParam(':admin_email', $admin_email, PDO::PARAM_STR);
	$statement->execute();

	//echo "Creating users...<br/>";
	$query = "
		CREATE TABLE `users` (
			`id` int(11) unsigned NOT NULL,
			`login` varchar(100) NOT NULL,
			`password` varchar(100) NOT NULL,
			`email` varchar(128) NOT NULL,
			`role` smallint(3) unsigned NOT NULL,
			`active` tinyint(1) NOT NULL,
			`registered` datetime NOT NULL,
			`logged_in` datetime NOT NULL,
			`token` varchar(255) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	";
	$statement = $db_connection->prepare($query);
	$statement->execute();

	//echo "Inserting users...<br/>";
	$query = "
		INSERT INTO `users` (`id`, `login`, `password`, `email`, `role`, `active`, `registered`, `logged_in`, `token`) VALUES
			(1, :admin_name, :admin_password, :admin_email, 1, 1, NOW(), NOW(), 'Installed');
	";
	$statement = $db_connection->prepare($query);
	$statement->bindParam(':admin_name', $admin_name, PDO::PARAM_STR);
	$statement->bindParam(':admin_password', $password, PDO::PARAM_STR);
	$statement->bindParam(':admin_email', $admin_email, PDO::PARAM_STR);
	$statement->execute();

	//echo "Creating visitors...<br/>";
	$query = "
		CREATE TABLE `visitors` (
			`id` int(11) unsigned NOT NULL,
			`visitor_ip` varchar(20) NOT NULL,
			`http_referer` text,
			`request_uri` text NOT NULL,
			`visited` datetime NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	";
	$statement = $db_connection->prepare($query);
	$statement->execute();

	//echo "Creating stat_ip...<br/>";
	$query = "
		CREATE TABLE `stat_ip` (
		  `id` int(11) UNSIGNED NOT NULL,
		  `date` date NOT NULL,
		  `ip` varchar(15) NOT NULL,
		  `counter` int(11) UNSIGNED NOT NULL,
		  `seconds` int(11) UNSIGNED NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	";
	$statement = $db_connection->prepare($query);
	$statement->execute();

	//echo "Creating stat_main...<br/>";
	$query = "
		CREATE TABLE `stat_main` (
		  `id` int(11) UNSIGNED NOT NULL,
		  `date` date NOT NULL,
		  `start` int(11) UNSIGNED NOT NULL,
		  `contact` int(11) UNSIGNED NOT NULL,
		  `admin` int(11) UNSIGNED NOT NULL,
		  `login` int(11) UNSIGNED NOT NULL,
		  `register` int(11) UNSIGNED NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	";
	$statement = $db_connection->prepare($query);
	$statement->execute();

	//echo "Creating _game_scores...<br/>";
	$query = "
		CREATE TABLE `_game_scores` (
		  `id` int(11) unsigned NOT NULL,
		  `player` varchar(64) CHARACTER SET utf8 NOT NULL,
		  `ip` varchar(15) CHARACTER SET utf8 NOT NULL,
		  `score` int(11) NOT NULL,
		  `saved` datetime NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	";
	$statement = $db_connection->prepare($query);
	$statement->execute();

	//echo "Creating _game_stats...<br/>";
	$query = "
		CREATE TABLE `_game_stats` (
		  `id` int(11) UNSIGNED NOT NULL,
		  `ip` varchar(15) NOT NULL,
		  `blocks` int(11) NOT NULL,
		  `maps` int(11) NOT NULL,
		  `level` int(11) NOT NULL,
		  `scores` int(11) NOT NULL,
		  `record` int(11) NOT NULL,
		  `achieved` datetime NOT NULL,
		  `play_time` varchar(6) NOT NULL,
		  `saved` datetime NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	";
	$statement = $db_connection->prepare($query);
	$statement->execute();

	//echo "Adding keys...<br/>";
	$query = "
		ALTER TABLE `access_levels`
		  ADD PRIMARY KEY (`id`),
		  ADD UNIQUE KEY `resource` (`resource`);

		ALTER TABLE `access_modules`
		  ADD PRIMARY KEY (`id`),
		  ADD UNIQUE KEY `module` (`module`);

		ALTER TABLE `access_rights`
		  ADD PRIMARY KEY (`id`),
		  ADD UNIQUE KEY `user_resource` (`user_id`, `resource_id`),
		  ADD KEY `fk_rights_resources` (`resource_id`),
		  ADD KEY `fk_rights_users` (`user_id`);

		ALTER TABLE `access_users`
		  ADD PRIMARY KEY (`id`),
		  ADD UNIQUE KEY `user_module` (`user_id`, `module_id`),
		  ADD KEY `fk_access_modules` (`module_id`),
		  ADD KEY `fk_access_users` (`user_id`);

		ALTER TABLE `archives`
		  ADD PRIMARY KEY (`id`),
		  ADD KEY `page_id` (`page_id`),
		  ADD KEY `category_id` (`category_id`),
		  ADD KEY `fk_archives_pages` (`page_id`),
		  ADD KEY `fk_archives_users` (`author_id`);

		ALTER TABLE `categories`
		  ADD PRIMARY KEY (`id`);

		ALTER TABLE `hosts`
		  ADD PRIMARY KEY (`id`),
		  ADD UNIQUE KEY `server_ip` (`server_ip`);

		ALTER TABLE `images`
		  ADD PRIMARY KEY (`id`),
		  ADD KEY `fk_images_users` (`owner_id`);

		ALTER TABLE `logins`
		  ADD PRIMARY KEY (`id`);

		ALTER TABLE `messages`
		  ADD PRIMARY KEY (`id`);

		ALTER TABLE `pages`
		  ADD PRIMARY KEY (`id`),
		  ADD KEY `category_id` (`category_id`),
		  ADD KEY `fk_pages_users` (`author_id`);

		ALTER TABLE `roles`
		  ADD PRIMARY KEY (`id`),
		  ADD UNIQUE KEY `name` (`name`);

		ALTER TABLE `searches`
		  ADD PRIMARY KEY (`id`);

		ALTER TABLE `settings`
		  ADD PRIMARY KEY (`id`),
		  ADD UNIQUE KEY `key_name` (`key_name`);

		ALTER TABLE `users`
		  ADD PRIMARY KEY (`id`),
		  ADD UNIQUE KEY `login` (`login`),
		  ADD UNIQUE KEY `email` (`email`);

		ALTER TABLE `visitors`
		  ADD PRIMARY KEY (`id`);

		ALTER TABLE `stat_ip`
		  ADD PRIMARY KEY (`id`),
		  ADD KEY `date` (`date`);

		ALTER TABLE `stat_main`
		  ADD PRIMARY KEY (`id`),
		  ADD KEY `date` (`date`);

		ALTER TABLE `_game_scores`
		  ADD PRIMARY KEY (`id`),
		  ADD UNIQUE KEY `player` (`player`,`ip`);

		ALTER TABLE `_game_stats`
		  ADD PRIMARY KEY (`id`);
	";
	$statement = $db_connection->prepare($query);
	$statement->execute();

	//echo "Adding AUTO_INCREMENT to ids...<br/>";
	$query = "
		ALTER TABLE `access_levels`
		  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=80;

		ALTER TABLE `access_modules`
		  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

		ALTER TABLE `access_rights`
		  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=80;

		ALTER TABLE `access_users`
		  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

		ALTER TABLE `archives`
		  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;

		ALTER TABLE `categories`
		  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;

		ALTER TABLE `hosts`
		  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

		ALTER TABLE `images`
		  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=14;

		ALTER TABLE `logins`
		  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

		ALTER TABLE `messages`
		  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;

		ALTER TABLE `pages`
		  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;

		ALTER TABLE `roles`
		  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;

		ALTER TABLE `searches`
		  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;

		ALTER TABLE `settings`
		  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=35;

		ALTER TABLE `users`
		  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;

		ALTER TABLE `visitors`
		  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;

		ALTER TABLE `stat_ip`
		  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;

		ALTER TABLE `stat_main`
		  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;

		ALTER TABLE `_game_scores`
		  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;

		ALTER TABLE `_game_stats`
		  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
	";
	$statement = $db_connection->prepare($query);
	$statement->execute();

	//echo "Checking for InnoDB support...<br/>";

	$query = "SELECT SUPPORT FROM INFORMATION_SCHEMA.ENGINES WHERE ENGINE = 'InnoDB';";
	$statement = $db_connection->prepare($query);
	//$statement->bindValue(1, $innodb_support, PDO::PARAM_STR);
	//$statement->bind_result($innodb_support);  // "bind_result() is a mysqli_statement method, not needed (or valid) for PDO" -  Berkowski, M. 2013. https://stackoverflow.com/questions/15514217/call-to-undefined-method-pdostatementbind-result
	//$statement->fetch(); // mysqli not PDO (?)
	$innodb_support = null;
	$statement->execute();
	$rows = $statement->fetchAll(PDO::FETCH_ASSOC);
	if (count($rows) > 0) {
		if (array_key_exists("SUPPORT", $rows[0])) {
			$innodb_support = $rows[0]["SUPPORT"];
			//echo "InnoDB SUPPORT: $innodb_support<br/>";  // ok: "YES" or "DEFAULT"; bad: "NO"
		}
		//else echo "InnoDB SUPPORT: (bad result) " . json_encode($rows) . "<br/>";  // ok: "YES" or "DEFAULT"; bad: "NO"
	}
	//else echo "InnoDB SUPPORT: (bad result) " . json_encode($rows) . "<br/>";  // ok: "YES" or "DEFAULT"; bad: "NO"
	//echo "InnoDB SUPPORT, all rows:" . json_encode($rows) . "<br/>";  // ok: "YES" or "DEFAULT"; bad: "NO"

	//echo "Adding foreign key constraints...<br/>";
	//$query = "
		//ALTER TABLE `access_rights`
		  //DROP FOREIGN KEY `fk_rights_resources`;
	//";
	//$statement = $db_connection->prepare($query);
	//$statement->execute();  // Fatal error: Uncaught PDOException: SQLSTATE[42000]: Syntax error or access violation: 1091 Can't DROP 'fk_rights_resources'; check that column/key exists
	//$query = "
		//ALTER TABLE `access_rights`
		  //DROP FOREIGN KEY `fk_rights_users`;
	//";
	//$statement = $db_connection->prepare($query);
	//$statement->execute();  // Fatal error: Uncaught PDOException: SQLSTATE[42000]: Syntax error or access violation: 1091 Can't DROP 'fk_rights_resources'; check that column/key exists

	//$query = "
		//ALTER TABLE `access_rights`
		  //ADD CONSTRAINT `fk_rights_resources` FOREIGN KEY (`resource_id`) REFERENCES `access_levels` (`id`),
		  //ADD CONSTRAINT `fk_rights_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
	//";
	//$statement = $db_connection->prepare($query);
	//$statement->execute();

	$query = "SELECT user_id FROM access_rights;";
	$statement = $db_connection->prepare($query);
	$this_id = null;
	$statement->execute();
	$rows = $statement->fetchAll(PDO::FETCH_ASSOC);
	$tried = array();
	if (count($rows) > 0) {
		for ($row_i=0; $row_i<count($rows); $row_i++) {
			if (array_key_exists("user_id", $rows[$row_i])) {
				$this_id = $rows[$row_i]["user_id"];
				if (!array_key_exists($this_id, $tried)) {
					$tried[$this_id] = true;
					//echo "this_id: $this_id<br/>";  // ok: "YES" or "DEFAULT"; bad: "NO"
					$this_query = "SELECT id FROM users WHERE id = $this_id;";
					// echo "$query...<br/>";
					$this_statement = $db_connection->prepare($this_query);
					$id1 = null;
					$this_statement->execute();
					$this_rows = $this_statement->fetchAll(PDO::FETCH_ASSOC);
					if (count($this_rows) > 0) {
						if (array_key_exists("id", $this_rows[0])) {
							$id1 = $this_rows[0]["id"];
							//echo "- found in users: $id1<br/>";  // ok: "YES" or "DEFAULT"; bad: "NO"
						}
						//else echo "- not found in users: (bad result) " . json_encode($this_rows) . "<br/>";  // ok: "YES" or "DEFAULT"; bad: "NO"
					}
					//else echo "- not found in users: (bad result) " . json_encode($this_rows) . "<br/>";  // ok: "YES" or "DEFAULT"; bad: "NO"
				}
			}
			//else echo "access_rights.user_id [$row_i]: (bad result) " . json_encode($rows[$row_i]) . "<br/>";  // ok: "YES" or "DEFAULT"; bad: "NO"
		}
	}
	//else echo "access_rights.user_id list: (bad result) " . json_encode($rows) . "<br/>";  // ok: "YES" or "DEFAULT"; bad: "NO"


	//$query = "SELECT id FROM users WHERE id = 1;";
	//$statement = $db_connection->prepare($query);
	//$id1 = null;
	//$statement->execute();
	//$rows = $statement->fetchAll(PDO::FETCH_ASSOC);
	//if (count($rows) > 0) {
		//if (array_key_exists("id", $rows[0])) {
			//$id1 = $rows[0]["id"];
			//echo "admin id: $id1<br/>";  // ok: "YES" or "DEFAULT"; bad: "NO"
		//}
		//else echo "admin id: (bad result) " . json_encode($rows) . "<br/>";  // ok: "YES" or "DEFAULT"; bad: "NO"
	//}
	//else echo "admin id: (bad result) " . json_encode($rows) . "<br/>";  // ok: "YES" or "DEFAULT"; bad: "NO"



	//echo "Creating fk_rights_resources...";
	$query = "
		ALTER TABLE `access_rights`
		  ADD CONSTRAINT `fk_rights_resources` FOREIGN KEY (`resource_id`) REFERENCES `access_levels` (`id`);
	";
	$statement = $db_connection->prepare($query);
	$statement->execute(); //if (!$statement->execute()) echo "FAIL<br/>";
	//else echo "OK<br/>";

	//echo "Creating fk_rights_users...";
	$query = "
		ALTER TABLE `access_rights`
		  ADD CONSTRAINT `fk_rights_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
	";
	$statement = $db_connection->prepare($query);
	try {
		$statement->execute(); //if (!$statement->execute()) echo "FAIL<br/>";
		//else echo "OK<br/>";
	}
	catch( PDOException $Exception ) {
		$enableShowIssue3 = true;
		//echo "FAIL*: " . $Exception->getMessage() . $Exception->getCode() . "<br/>";
	}
	// Fatal error: Uncaught PDOException: SQLSTATE[HY000]: General error: 1215 Cannot add foreign key constraint
	// https://www.rathishkumar.in/2016/01/solved-how-to-solve-mysql-error-code.html says reason could be:
	// * [checked] Key does not exist in the parent table.
	// * [checked] When the definition of the foreign key is different from the reference key...The size and sign of the integer must be the same. The character string columns, the character set and collation must be the same.
	// * [checked] When you are using composite primary key or implementing one-to-one relationships you may using foreign key as a primary key in your child table. In that case, definition of foreign key should not define as ON DELETE SET NULL. Since primary key cannot be NULL
	// * When you specify SET NULL action and you defined the columns in the child table as NOT NULL
	// same error occurs on fk_rights_resources but only if done without the former--why not if first??


	//echo "Creating fk_access_modules, fk_access_users...";
	$query = "
		ALTER TABLE `access_users`
		  ADD CONSTRAINT `fk_access_modules` FOREIGN KEY (`module_id`) REFERENCES `access_modules` (`id`),
		  ADD CONSTRAINT `fk_access_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
	";
	$statement = $db_connection->prepare($query);
	try {
		$statement->execute(); //if (!$statement->execute()) echo "FAIL<br/>";
		//else echo "OK<br/>";
	}
	catch( PDOException $Exception ) {
		$enableShowIssue3 = true;
		//echo "FAIL*: " . $Exception->getMessage() . $Exception->getCode() . "<br/>";
	}

	//echo "Creating fk_archives_pages, fk_archives_users...";
	$query = "

		ALTER TABLE `archives`
		  ADD CONSTRAINT `fk_archives_pages` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`),
		  ADD CONSTRAINT `fk_archives_users` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`);
	";
	$statement = $db_connection->prepare($query);
	try {
		$statement->execute();//if (!$statement->execute()) echo "FAIL<br/>";
		//else echo "OK<br/>";
	}
	catch( PDOException $Exception ) {
		$enableShowIssue3 = true;
		//echo "FAIL*: " . $Exception->getMessage() . $Exception->getCode() . "<br/>";
	}

	//echo "Creating fk_images_users...";
	$query = "

		ALTER TABLE `images`
		  ADD CONSTRAINT `fk_images_users` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`);
	";
	$statement = $db_connection->prepare($query);
	try {
		$statement->execute();//if (!$statement->execute()) echo "FAIL<br/>";
		//else echo "OK<br/>";
	}
	catch( PDOException $Exception ) {
		$enableShowIssue3 = true;
		//echo "FAIL*: " . $Exception->getMessage() . $Exception->getCode() . "<br/>";
	}

	//echo "Creating fk_pages_users...";
	$query = "

		ALTER TABLE `pages`
		  ADD CONSTRAINT `fk_pages_users` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`);
	";
	$statement = $db_connection->prepare($query);
	try {
		$statement->execute(); //if (!$statement->execute()) echo "FAIL<br/>";
		//else echo "OK<br/>";
	}
	catch( PDOException $Exception ) {
		$enableShowIssue3 = true;
		//echo "FAIL*: " . $Exception->getMessage() . $Exception->getCode() . "<br/>";
	}
	$issue3Msg = '*Failure to create foreign keys is <a href="https://github.com/andrzuk/AngularCMS/issues/3">not important</a>.';
	$issue3PlainMsg = '*Creating foreign keys failed (unimportant: github.com/andrzuk/AngularCMS/issues/3)';
	if ($enableShowIssue3) {
		//echo '<br/><br/>*Failure to create foreign keys is <a href="https://github.com/andrzuk/AngularCMS/issues/3">not important according to the author</a>.<br/>';
	}

	$settings = array(
		'brand' => $brand,
		'description' => $description,
		'keywords' => $keywords,
		'domain' => $domain,
		//'admin_name' => $admin_name,
		//'admin_email' => $admin_email,
		//'admin_password' => $admin_password,
		);

	$message = "Serwis został pomyślnie zainstalowany. The service has been successfully installed. $issue3PlainMsg";
	$success = true;
}
else
{
	$settings = NULL;
	$message = 'Nie wprowadzono wymaganych danych. The required data has not been entered.';
	$success = false;
}

$result = array('settings' => $settings, 'success' => $success, 'message' => $message);

echo json_encode($result);

?>
