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
use frame\modules\Module;

class RouteStatisticsSubModule extends BaseStatisticsSubModule
{
    private $routeStat;
    private $routeTimer;
    private $collectPage;

    public function __construct(string $name, ?Module $parent = null)
    {
        parent::__construct($name, $parent);
        $this->routeStat = new RouteStat;
        $this->routeTimer = new TimeStat;
        $this->collectPage = new CollectPageRouteStat;
    }

    public function clearStats()
    {
        Records::from(DynamicRouteParam::getTable())->delete();
        Records::from(RouteStat::getTable())->delete();
    }

    public function endCollecting()
    {
        (new EndCollectRouteStat(
            $this->routeStat, 
            $this->collectPage, 
            $this->routeTimer)
        )->exec();
    }

    public function getAppEventHandlers(): array
    {
        return [
            Core::EVENT_APP_START => new StartCollectRouteStat(
                $this->routeStat,
                $this->routeTimer
            ),
            ActionMacro::EVENT_ACTION_TRIGGERED => new CollectActionRouteStat(
                $this->routeStat
            ),
            Core::EVENT_APP_ERROR => new CollectErrorRouteStat($this->routeStat),
            View::EVENT_LOAD_START => $this->collectPage
        ];
    }
}