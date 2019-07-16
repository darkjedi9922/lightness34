<?php
ini_set('error_reporting', E_ALL | E_STRICT);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once __DIR__.'/autoload.php';
require_once __DIR__.'/lightness.lib.php';

use frame\Core;
use frame\errors\HttpError;
use frame\errors\StrictException;
use frame\errors\handlers\HttpErrorHandler;
use frame\errors\handlers\DefaultErrorHandler;
use frame\errors\handlers\StrictExceptionHandler;
use frame\macros\ActionMacro;
use frame\macros\ValueMacro;
use frame\macros\BlockMacro;
use frame\macros\WidgetMacro;

use globals\config_core;

$app = new Core(config_core::get());
$app->setDefaultHandler(DefaultErrorHandler::class);
$app->setHandler(HttpError::class, HttpErrorHandler::class);
$app->setHandler(StrictException::class, StrictExceptionHandler::class);
$app->setMacro('action', ActionMacro::class);
$app->setMacro('value', ValueMacro::class);
$app->setMacro('block', BlockMacro::class);
$app->setMacro('widget', WidgetMacro::class);
$app->exec();