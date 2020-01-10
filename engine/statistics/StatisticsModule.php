<?php namespace engine\statistics;

use frame\Core;
use frame\modules\Module;
use frame\modules\RightsDesc;

class StatisticsModule extends Module
{
    public function __construct(string $name, ?Module $parent = null)
    {
        parent::__construct($name, $parent);
        $app = Core::$app;

        $app->setModule(new EventStatisticsSubModule('events', $this));
        $app->setModule(new RouteStatisticsSubModule('routes', $this));
        $app->setModule(new ActionStatisticsSubModule('actions', $this));
        $app->setModule(new DbStatisticsSubModule('db', $this));
        $app->setModule(new CashStatisticsSubModule('cash', $this));
    }

    public function createRightsDescription(): ?RightsDesc
    {
        return null;
    }
}