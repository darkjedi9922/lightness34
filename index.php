<?php require_once __DIR__ . '/bootstrap.php';

use frame\core\Core;
use frame\route\Router;
use frame\route\Request;
use frame\errors\HttpError;
use frame\errors\StrictException;
use frame\errors\handlers\HttpErrorHandler;
use frame\errors\handlers\DefaultErrorHandler;
use frame\errors\handlers\StrictExceptionHandler;

use engine\admin\AdminModule;
use engine\users\UsersModule;
use engine\messages\MessagesModule;
use engine\articles\ArticlesModule;
use engine\comments\CommentsModule;
use engine\statistics\StatisticsModule;

use frame\errors\Errors;
use frame\macros\Events;
use frame\actions\ActionMacro;
use frame\macros\ValueMacro;
use frame\macros\BlockMacro;
use frame\macros\WidgetMacro;

use frame\views\View;
use frame\views\macros\ApplyDefaultLayout;

$app = new Core(new Router(Request::getRequest()));

$errors = Errors::get();
$errors->setDefaultHandler(DefaultErrorHandler::class);
$errors->setHandler(HttpError::class, HttpErrorHandler::class);
$errors->setHandler(StrictException::class, StrictExceptionHandler::class);

$app->setModule(new StatisticsModule('stat'));
$app->setModule(new AdminModule('admin'));
$app->setModule(new UsersModule('users'));
$app->setModule(new MessagesModule('messages'));
$app->setModule(new ArticlesModule('articles'));
$app->setModule(new CommentsModule('comments', $app->getModule('articles')));

$events = Events::get();
$events->on(Core::EVENT_APP_START, new ActionMacro('action'));
$events->on(Core::EVENT_APP_START, new ValueMacro('value'));
$events->on(Core::EVENT_APP_START, new BlockMacro('block'));
$events->on(Core::EVENT_APP_START, new WidgetMacro('widget'));
$events->on(View::EVENT_LOAD_START, new ApplyDefaultLayout);

$app->exec();