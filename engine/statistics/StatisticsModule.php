<?php namespace engine\statistics;

use frame\Core;
use frame\modules\Module;
use frame\modules\RightsDesc;
use engine\statistics\stats\RouteStat;
use engine\statistics\macros\CollectRouteStat;
use engine\statistics\macros\CollectRouteEndStat;
use engine\statistics\macros\InsertStat;
use frame\actions\ActionMacro;
use engine\statistics\macros\CollectActionType;
use frame\cash\config;

class StatisticsModule extends Module
{
    public function __construct(string $name, ?Module $parent = null)
    {
        parent::__construct($name, $parent);

        $router = Core::$app->router;
        $config = config::get('statistics');
        if ($router->isInAnyNamespace($config->ignorePageNamespaces)) return;

        $routeStat = new RouteStat;

        Core::$app->on(Core::EVENT_APP_START, new CollectRouteStat($routeStat));
        Core::$app->on(
            ActionMacro::EVENT_ACTION_TRIGGERED, 
            new CollectActionType($routeStat));
        Core::$app->on(Core::EVENT_APP_END, new CollectRouteEndStat($routeStat));
        Core::$app->on(Core::EVENT_APP_END, new InsertStat($routeStat));
    }

    public function createRightsDescription(): ?RightsDesc
    {
        return null;
    }
}