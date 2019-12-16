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

class StatisticsModule extends Module
{
    public function __construct(string $name, ?Module $parent = null)
    {
        parent::__construct($name, $parent);

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