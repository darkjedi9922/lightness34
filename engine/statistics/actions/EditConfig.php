<?php namespace engine\statistics\actions;

use frame\actions\ActionBody;
use frame\actions\fields\BooleanField;
use frame\actions\fields\IntegerField;
use frame\stdlib\configs\JsonConfig;
use frame\auth\InitAccess;

class EditConfig extends ActionBody
{
    public function listPost(): array
    {
        return [
            'enabled' => BooleanField::class,
            'historyListLimit' => IntegerField::class
        ];
    }

    public function initialize(array $get)
    {
        InitAccess::accessRight('stat', 'configure');
    }

    public function succeed(array $post, array $files)
    {
        $config = new JsonConfig(ROOT_DIR . '/config/statistics');
        $config->enabled = $post['enabled']->get();
        $config->historyListLimit = $post['historyListLimit']->get();
        $config->save();
    }
}