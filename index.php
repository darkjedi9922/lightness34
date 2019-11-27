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

use engine\admin\AdminModule;
use engine\users\UsersModule;
use engine\articles\ArticlesModule;
use engine\comments\CommentsModule;

$app = new Core;

$app->setDefaultHandler(DefaultErrorHandler::class);
$app->setHandler(HttpError::class, HttpErrorHandler::class);
$app->setHandler(StrictException::class, StrictExceptionHandler::class);

$app->setModule(new AdminModule('admin'));
$app->setModule(new UsersModule('users'));
$app->setModule(new ArticlesModule('articles'));
$app->setModule(new CommentsModule('article-comments'));

$app->setMacro('action', ActionMacro::class);
$app->setMacro('value', ValueMacro::class);
$app->setMacro('block', BlockMacro::class);
$app->setMacro('widget', WidgetMacro::class);

$app->exec();