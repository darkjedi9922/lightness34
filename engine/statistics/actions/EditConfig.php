<?php namespace engine\statistics\actions;

use frame\actions\ActionBody;
use frame\actions\fields\BooleanField;
use frame\actions\fields\IntegerField;
use frame\actions\fields\StringField;
use frame\config\Json;
use frame\tools\Init;
use frame\stdlib\tools\units\TimeUnit;

class EditConfig extends ActionBody
{
    public function listPost(): array
    {
        return [
            'enabled' => BooleanField::class,
            'storeTimeValue' => IntegerField::class,
            'storeTimeUnit' => StringField::class
        ];
    }

    public function initialize(array $get)
    {
        Init::accessRight('stat', 'configure');
    }

    public function succeed(array $post, array $files)
    {
        $storeTime = new TimeUnit(
            $post['storeTimeValue']->get(),
            $post['storeTimeUnit']->get()
        );
        $config = new Json(ROOT_DIR . '/config/statistics.json');
        $config->enabled = $post['enabled']->get();
        $config->storeTimeInSeconds = $storeTime->convertTo(TimeUnit::SECONDS);
        $config->save();
    }
}