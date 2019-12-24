<?php namespace engine\statistics;

use frame\Core;
use frame\modules\Module;
use frame\modules\RightsDesc;
use engine\statistics\stats\RouteStat;
use frame\actions\ActionMacro;
use frame\cash\config;
use frame\views\View;

use engine\statistics\macros\routes\StartCollectRouteStat;
use engine\statistics\macros\routes\CollectActionRouteStat;
use engine\statistics\macros\routes\CollectErrorRouteStat;
use engine\statistics\macros\routes\CollectPageRouteStat;
use engine\statistics\macros\routes\EndCollectRouteStat;
use engine\statistics\stats\TimeStat;
use frame\actions\Action;
use engine\statistics\stats\ActionStat;
use engine\statistics\macros\actions\StartCollectActionStat;
use engine\statistics\macros\actions\EndCollectActionStat;

class StatisticsModule extends Module
{
    private $config;

    public function __construct(string $name, ?Module $parent = null)
    {
        parent::__construct($name, $parent);

        $router = Core::$app->router;
        $this->config = config::get('statistics');
        if ($router->isInAnyNamespace($this->config->ignorePageNamespaces)) return;

        $this->setupEventHandlers($this->getRouteStatCollectors());
        $this->setupEventHandlers($this->getActionStatCollectors());
    }

    public function createRightsDescription(): ?RightsDesc
    {
        return null;
    }

    private function setupEventHandlers(array $macros)
    {
        foreach ($macros as $event => $macro) Core::$app->on($event, $macro);
    }

    private function getRouteStatCollectors(): array
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

    private function getActionStatCollectors(): array
    {
        $stat = new ActionStat;
        $timer = new TimeStat;

        return [
            Action::EVENT_START => new StartCollectActionStat($stat, $timer),
            Action::EVENT_END => new EndCollectActionStat($stat, $timer)
        ];
    }
}