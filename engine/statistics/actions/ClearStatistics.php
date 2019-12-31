<?php namespace engine\statistics\actions;

use frame\actions\ActionBody;
use frame\tools\Init;
use frame\Core;
use engine\statistics\BaseStatisticsSubModule;

class ClearStatistics extends ActionBody
{
    /** @var BaseStatisticsSubModule */
    private $module;

    public function listGet(): array
    {
        return [
            'module' => [
                self::GET_STRING,
                'Name of the statistics submodule under which it was setup'
            ],
        ];
    }

    public function initialize(array $get)
    {
        $this->module = Core::$app->getModule($get['module']);
        Init::require($this->module !== null);
        Init::require(is_subclass_of($this->module, BaseStatisticsSubModule::class));
    }

    public function succeed(array $post, array $files)
    {
        $this->module->clearStats();
    }
}