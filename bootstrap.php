<?php
ini_set('error_reporting', E_ALL | E_STRICT);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

mb_internal_encoding('UTF-8');
define('NONE', -1);
define('endl', '<br>');
define('ROOT_DIR', __DIR__);

require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/lightness.lib.php';