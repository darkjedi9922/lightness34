<?php namespace engine\statistics\actions;

use frame\actions\ActionBody;
use frame\modules\Modules;
use frame\modules\Module;
use frame\auth\InitAccess;
use engine\statistics\StatisticsModule;
use engine\statistics\BaseStatisticsSubModule;

class ClearAllStats extends ActionBody
{
    public function initialize(array $get)
    {
        InitAccess::accessRight('stat', 'clear');
    }

    public function succeed(array $post, array $files)
    {
        $modules = Modules::getDriver()->toArray();
        foreach ($modules as $module) {
            /** @var Module $module */
            $parent = $module->getParent();
            if ($parent instanceof StatisticsModule) {
                /** @var BaseStatisticsSubModule $module */
                $module->clearStats();
            }
        }
    }
}