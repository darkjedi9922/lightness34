<?php namespace engine\statistics;

use frame\Core;
use frame\views\View;
use frame\actions\ActionMacro;
use engine\statistics\stats\RouteStat;
use engine\statistics\stats\TimeStat;
use engine\statistics\macros\routes\StartCollectRouteStat;
use engine\statistics\macros\routes\CollectPageRouteStat;
use engine\statistics\macros\routes\CollectActionRouteStat;
use engine\statistics\macros\routes\CollectErrorRouteStat;
use engine\statistics\macros\routes\EndCollectRouteStat;

class RouteStatisticsSubModule extends BaseStatisticsSubModule
{
    protected function getAppEventHandlers(): array
    {
        $route = new RouteStat;
        $routeTimer = new TimeStat;

        $collectPage = new CollectPageRouteStat;
        $end = new EndCollectRouteStat($route, $collectPage, $routeTimer);

        return [
            Core::EVENT_APP_START => new StartCollectRouteStat($route, $routeTimer),
            ActionMacro::EVENT_ACTION_TRIGGERED => new CollectActionRouteStat($route),
            Core::EVENT_APP_ERROR => new CollectErrorRouteStat($route),
            View::EVENT_LOAD_START => $collectPage,
            Core::EVENT_APP_END => $end
        ];
    }
}