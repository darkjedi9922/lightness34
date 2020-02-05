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
use engine\articles\ArticlesModule;
use engine\comments\CommentsModule;
use engine\statistics\StatisticsModule;

use frame\actions\ActionMacro;
use frame\macros\ValueMacro;
use frame\macros\BlockMacro;
use frame\macros\WidgetMacro;

use frame\views\View;
use frame\views\macros\ApplyDefaultLayout;
use engine\users\macros\UpdateUserStatus;
use engine\users\macros\UpdateOfflineUsers;

$app = new Core(new Router(Request::getRequest()));

$app->setDefaultHandler(DefaultErrorHandler::class);
$app->setHandler(HttpError::class, HttpErrorHandler::class);
$app->setHandler(StrictException::class, StrictExceptionHandler::class);

$app->setModule(new StatisticsModule('stat'));
$app->setModule(new AdminModule('admin'));
$app->setModule(new UsersModule('users'));
$app->setModule(new ArticlesModule('articles'));
$app->setModule(new CommentsModule('comments', $app->getModule('articles')));

$app->on(Core::EVENT_APP_START, new ActionMacro('action'));
$app->on(Core::EVENT_APP_START, new ValueMacro('value'));
$app->on(Core::EVENT_APP_START, new BlockMacro('block'));
$app->on(Core::EVENT_APP_START, new WidgetMacro('widget'));
$app->on(View::EVENT_LOAD_START, new ApplyDefaultLayout);
$app->on(Core::EVENT_APP_START, new UpdateUserStatus);
$app->on(Core::EVENT_APP_END, new UpdateOfflineUsers);

$app->exec();