<?php namespace engine\statistics;

use frame\Core;
use frame\modules\Module;
use frame\modules\RightsDesc;

use frame\cash\config;

abstract class BaseStatisticsSubModule extends Module
{
    public function __construct(string $name, Module $parent)
    {
        parent::__construct("{$parent->getName()}/$name", $parent);

        $router = Core::$app->router;
        $config = config::get('statistics');
        if ($router->isInAnyNamespace($config->ignorePageNamespaces)) return;

        $this->setupEventHandlers($this->getAppEventHandlers());
    }

    public function createRightsDescription(): ?RightsDesc
    {
        return null;
    }

    protected abstract function getAppEventHandlers(): array;

    private function setupEventHandlers(array $macros)
    {
        foreach ($macros as $event => $macro) Core::$app->on($event, $macro);
    }
} 