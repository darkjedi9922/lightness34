<?php namespace engine\statistics;

use frame\Core;
use frame\modules\Module;
use frame\modules\RightsDesc;
use engine\statistics\BaseStatisticsSubModule;
use frame\cash\config;

class StatisticsModule extends Module
{
    public function __construct(string $name, ?Module $parent = null)
    {
        parent::__construct($name, $parent);
        $app = Core::$app;

        $submodules = [
            new EventStatisticsSubModule('events', $this),
            new RouteStatisticsSubModule('routes', $this),
            new ActionStatisticsSubModule('actions', $this),
            new DbStatisticsSubModule('db', $this),
            new CashStatisticsSubModule('cash', $this)
        ];

        foreach ($submodules as $submodule) {
            Core::$app->setModule($submodule);
        }

        $router = Core::$app->router;
        $config = config::get('statistics');
        if ($router->isInAnyNamespace($config->ignoreRouteNamespaces)) return;
        $this->setupEventHandlers($submodules);
    }

    public function createRightsDescription(): ?RightsDesc
    {
        return null;
    }

    private function setupEventHandlers(array $submodules)
    {
        foreach ($submodules as $submodule) {
            /** @var BaseStatisticsSubModule $submodule */
            $macros = $submodule->getAppEventHandlers();
            foreach ($macros as $event => $macro) Core::$app->on($event, $macro);
        }
    }
}