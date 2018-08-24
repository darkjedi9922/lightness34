<?php
ini_set('error_reporting', E_ALL | E_STRICT);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once __DIR__.'/autoload.php';
require_once __DIR__.'/lightness.lib.php';

use frame\Application;
use frame\errors\HttpError;
use frame\errors\StrictException;
use frame\errors\handlers\HttpErrorHandler;
use frame\errors\handlers\DefaultErrorHandler;
use frame\errors\handlers\StrictExceptionHandler;

$app = new Application;
$app->setDefaultHandler(DefaultErrorHandler::class);
$app->setHandler(HttpError::class, HttpErrorHandler::class);
$app->setHandler(StrictException::class, StrictExceptionHandler::class);
$app->exec();