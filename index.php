<?php require_once __DIR__ . '/bootstrap.php';

use engine\admin\AdminModule;
use engine\articles\ArticlesModule;
use engine\comments\CommentsModule;
use engine\messages\MessagesModule;
use engine\statistics\StatisticsModule;
use engine\users\UsersModule;
use frame\actions\ActionMacro;
use frame\auth\RightsStore;
use frame\config\ConfigRouter;
use frame\core\Core;
use frame\errors\Errors;
use frame\errors\handlers\DefaultErrorHandler;
use frame\errors\handlers\HttpErrorHandler;
use frame\errors\handlers\StrictExceptionHandler;
use frame\errors\HttpError;
use frame\errors\StrictException;
use frame\events\Events;
use frame\modules\Modules;
use frame\route\Request;
use frame\route\Response;
use frame\stdlib\configs\JsonConfig;
use frame\stdlib\configs\PhpConfig;
use frame\stdlib\drivers\auth\DatabaseRightsStore;
use frame\stdlib\drivers\route\UrlRequest;
use frame\stdlib\drivers\route\UrlResponse;
use frame\views\macros\ApplyDefaultLayout;
use frame\views\macros\BlockMacro;
use frame\views\macros\ShowPage;
use frame\views\macros\WidgetMacro;
use frame\views\View;
use frame\database\SqlDriver;
use frame\stdlib\drivers\database\MySqlDriver;

$app = new Core;
$app->replaceDriver(Request::class, UrlRequest::class);
$app->replaceDriver(Response::class, UrlResponse::class);
$app->replaceDriver(SqlDriver::class, MySqlDriver::class);
$app->replaceDriver(RightsStore::class, DatabaseRightsStore::class);

$configRouter = ConfigRouter::getDriver();
$configRouter->addSupport([JsonConfig::class, PhpConfig::class]);

$errors = Errors::getDriver();
$errors->setDefaultHandler(DefaultErrorHandler::class);
$errors->setHandler(HttpError::class, HttpErrorHandler::class);
$errors->setHandler(StrictException::class, StrictExceptionHandler::class);

$modules = Modules::getDriver();
$modules->set(new StatisticsModule('stat'));
$modules->set(new AdminModule('admin'));
$modules->set(new UsersModule('users'));
$modules->set(new MessagesModule('messages'));
$modules->set($articles = new ArticlesModule('articles'));
$modules->set(new CommentsModule('comments', $articles));

$events = Events::getDriver();
$events->on(Core::EVENT_APP_START, new ActionMacro('action'));
$events->on(Core::EVENT_APP_START, new BlockMacro('block'));
$events->on(Core::EVENT_APP_START, new WidgetMacro('widget'));
$events->on(Core::EVENT_APP_START, new ShowPage);
$events->on(View::EVENT_BEFORE_RENDER, new ApplyDefaultLayout);

$app->exec();