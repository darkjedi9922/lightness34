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
use engine\statistics\stats\DynamicRouteParam;
use frame\database\Records;

class RouteStatisticsSubModule extends BaseStatisticsSubModule
{
    public function clearStats()
    {
        Records::select(DynamicRouteParam::getTable())->delete();
        Records::select(RouteStat::getTable())->delete();
    }

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