<?php namespace engine\statistics;

use frame\Core;
use frame\modules\Module;
use frame\modules\RightsDesc;
use engine\statistics\macros\RouteStatMacro;

class StatisticsModule extends Module
{
    public function __construct(string $name, ?Module $parent = null)
    {
        parent::__construct($name, $parent);

        $routeStat = new RouteStat;

        Core::addHook(Core::HOOK_BEFORE_EXECUTION, new RouteStatMacro($routeStat));
    }

    public function createRightsDescription(): ?RightsDesc
    {
        return null;
    }
}