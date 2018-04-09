<?php

/*
 * Database connection
 */

define ('DB_HOST', 'localhost'); // db hostname
define ('DB_NAME', 'dev_angularcms'); // db name
define ('DB_USER', 'root'); // db username
define ('DB_PASS', ''); // db password

/*
 * Application directories
 */

define ('GALLERY_DIR', dirname(__FILE__) . '/../public/img/gallery/');
define ('THUMB_DIR', dirname(__FILE__) . '/../public/img/thumbnail/');
define ('NOT_FOUND', dirname(__FILE__) . '/../public/img/Image-Not-Found.png');
define ('CSS_DIR', dirname(__FILE__) . '/../public/css/');
define ('JS_DIR', dirname(__FILE__) . '/../public/js/');
define ('LIB_DIR', dirname(__FILE__) . '/../lib/');
define ('MAILER_DIR', dirname(__FILE__) . '/../lib/mailer/');

/*
 * Gallery thumbnails
 */

define ('THUMB_WIDTH', 150);
define ('THUMB_HEIGHT', 150);

/*
 * Application install script
 */

define ('INSTALL_SCRIPT', dirname(__FILE__) . '/../install/install_database.php');

?>
