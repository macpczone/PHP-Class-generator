<?php

// Settings
ini_set('error_reporting', E_ALL ^ E_NOTICE);
ini_set('display_errors', 'on');
ini_set('display_startup_errors', 1);
date_default_timezone_set('Europe/London');

// Constants
define('AUTHOR', 'Ekain Agirrezabal');
define('TAB', chr(9));
define('RET', ' ' . chr(10) . chr(13));
define('NL', chr(13));
define('DOCUMENT_ROOT', dirname(__FILE__) . '/../');
//define('CLASSGENERATOR_DIR', DOCUMENT_ROOT . '_class/');
define('CLASSGENERATOR_DIR', DOCUMENT_ROOT . 'Entities/');

// Database credentials
//include '_db_config.inc.php';


include '_database.class.php';
include '_generator.class.php';
