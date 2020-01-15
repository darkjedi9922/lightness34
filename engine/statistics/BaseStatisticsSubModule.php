<?php namespace engine\statistics;

use frame\modules\Module;
use frame\modules\RightsDesc;

abstract class BaseStatisticsSubModule extends Module
{
    public function createRightsDescription(): ?RightsDesc
    {
        return null;
    }

    public abstract function clearStats();
    public abstract function endCollecting();
    public abstract function getAppEventHandlers(): array;
}