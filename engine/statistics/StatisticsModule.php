<?php namespace engine\statistics;

use frame\Core;
use frame\modules\Module;
use frame\modules\RightsDesc;
use frame\cash\config;

class StatisticsModule extends Module
{
    private $config;

    public function __construct(string $name, ?Module $parent = null)
    {
        parent::__construct($name, $parent);

        $app = Core::$app;
        $router = $app->router;
        $this->config = config::get('statistics');
        if ($router->isInAnyNamespace($this->config->ignorePageNamespaces)) return;

        $app->setModule(new EventStatisticsSubModule("events", $this));
        $app->setModule(new RouteStatisticsSubModule("routes", $this));
        $app->setModule(new ActionStatisticsSubModule("common", $this));
    }

    public function createRightsDescription(): ?RightsDesc
    {
        return null;
    }
}