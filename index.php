<?php require_once __DIR__ . '/bootstrap.php';

use frame\core\Core;
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
use frame\modules\Modules;

use frame\actions\ActionMacro;
use frame\macros\ValueMacro;
use frame\macros\BlockMacro;
use frame\macros\WidgetMacro;

use frame\views\View;
use frame\views\macros\ShowPage;
use frame\views\macros\ApplyDefaultLayout;

$app = new Core;
$app->replaceDriver(frame\route\Request::class, drivers\route\UrlRequest::class);
$app->replaceDriver(frame\route\Response::class, drivers\route\UrlResponse::class);
$app->replaceDriver(
    frame\modules\RightsStore::class,
    drivers\modules\DatabaseRightsStore::class
);

$errors = Errors::get();
$errors->setDefaultHandler(DefaultErrorHandler::class);
$errors->setHandler(HttpError::class, HttpErrorHandler::class);
$errors->setHandler(StrictException::class, StrictExceptionHandler::class);

$modules = Modules::get();
$modules->set(new StatisticsModule('stat'));
$modules->set(new AdminModule('admin'));
$modules->set(new UsersModule('users'));
$modules->set(new MessagesModule('messages'));
$modules->set(new ArticlesModule('articles'));
$modules->set(new CommentsModule('comments', $modules->findByName('articles')));

$events = Events::get();
$events->on(Core::EVENT_APP_START, new ActionMacro('action'));
$events->on(Core::EVENT_APP_START, new ValueMacro('value'));
$events->on(Core::EVENT_APP_START, new BlockMacro('block'));
$events->on(Core::EVENT_APP_START, new WidgetMacro('widget'));
$events->on(Core::EVENT_APP_START, new ShowPage);
$events->on(View::EVENT_LOAD_START, new ApplyDefaultLayout);

$app->exec();