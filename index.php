<?php require_once __DIR__ . '/bootstrap.php';

use frame\core\Core;
use frame\views\View;
use frame\errors\HttpError;
use frame\errors\StrictException;
use frame\errors\handlers\HttpErrorHandler;
use frame\errors\handlers\DefaultErrorHandler;
use frame\errors\handlers\StrictExceptionHandler;

$app = new Core;
$app->replaceDriver(
    frame\route\Request::class,
    frame\stdlib\drivers\route\UrlRequest::class
);
$app->replaceDriver(
    frame\route\Response::class,
    frame\stdlib\drivers\route\UrlResponse::class
);
$app->replaceDriver(
    frame\auth\RightsStore::class,
    frame\stdlib\drivers\auth\DatabaseRightsStore::class
);

$configRouter = frame\config\ConfigRouter::getDriver();
$configRouter->addSupport([
    frame\stdlib\configs\JsonConfig::class,
    frame\stdlib\configs\PhpConfig::class
]);

$errors = frame\errors\Errors::getDriver();
$errors->setDefaultHandler(DefaultErrorHandler::class);
$errors->setHandler(HttpError::class, HttpErrorHandler::class);
$errors->setHandler(StrictException::class, StrictExceptionHandler::class);

$modules = frame\modules\Modules::getDriver();
$modules->set(new engine\statistics\StatisticsModule('stat'));
$modules->set(new engine\admin\AdminModule('admin'));
$modules->set(new engine\users\UsersModule('users'));
$modules->set(new engine\messages\MessagesModule('messages'));
$modules->set($articles = new engine\articles\ArticlesModule('articles'));
$modules->set(new engine\comments\CommentsModule('comments', $articles));

$events = frame\events\Events::getDriver();
$events->on(Core::EVENT_APP_START, new frame\actions\ActionMacro('action'));
$events->on(Core::EVENT_APP_START, new frame\views\macros\ValueMacro('value'));
$events->on(Core::EVENT_APP_START, new frame\views\macros\BlockMacro('block'));
$events->on(Core::EVENT_APP_START, new frame\views\macros\WidgetMacro('widget'));
$events->on(Core::EVENT_APP_START, new frame\views\macros\ShowPage);
$events->on(View::EVENT_LOAD_START, new frame\views\macros\ApplyDefaultLayout);

$app->exec();