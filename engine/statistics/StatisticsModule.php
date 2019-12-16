<?php namespace engine\statistics;

use frame\Core;
use frame\modules\Module;
use frame\modules\RightsDesc;
use engine\statistics\stats\RouteStat;
use engine\statistics\macros\CollectRouteStat;
use engine\statistics\macros\InsertStat;

class StatisticsModule extends Module
{
    public function __construct(string $name, ?Module $parent = null)
    {
        parent::__construct($name, $parent);

        $routeStat = new RouteStat;

        $collectRouteStat = new CollectRouteStat($routeStat);
        $insertStatHook = new InsertStat($routeStat);

        Core::$app->on(Core::EVENT_APP_START, $collectRouteStat);
        Core::$app->on(Core::EVENT_APP_END, $insertStatHook);
    }

    public function createRightsDescription(): ?RightsDesc
    {
        return null;
    }
}