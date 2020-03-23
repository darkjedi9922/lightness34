<?php namespace engine\statistics\actions;

use frame\actions\ActionBody;
use frame\tools\Init;
use frame\modules\Modules;
use engine\statistics\BaseStatisticsSubModule;
use frame\actions\fields\StringField;

class ClearStatistics extends ActionBody
{
    /** @var BaseStatisticsSubModule */
    private $module;

    public function listGet(): array
    {
        return [
            'module' => StringField::class
        ];
    }

    public function initialize(array $get)
    {
        Init::accessRight('stat', 'clear');
        $this->module = Modules::getDriver()->findByName($get['module']->get());
        Init::require($this->module !== null);
        Init::require(is_subclass_of($this->module, BaseStatisticsSubModule::class));
    }

    public function succeed(array $post, array $files)
    {
        $this->module->clearStats();
    }
}