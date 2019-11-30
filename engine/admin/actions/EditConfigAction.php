<?php namespace engine\admin\actions;

use frame\actions\Action;
use frame\tools\Init;
use frame\config\Json;
use engine\users\cash\user_me;
use engine\users\Group;

/**
 * Параметр name: имя конфига (например, 'core').
 * Файл должен существовать.
 * Права: root.
 * Данные: настройки и их новые значения. Если каких-либо настроек не будет передано,
 * они не будут изменены.
 * 
 * При чем в именах настроек символ . должнен быть заменен на ->
 * Например, site.name заменить на site->name
 * Потому что при передаче POST запроса все символы . заменяются на _
 * и тогда не понятно как интерпретировать имя настройки.
 */
class EditConfigAction extends Action
{
    /** @var Json */
    private $config;

    protected function initialize(array $get)
    {
        $name = $get['name'] ?? null;
        Init::require($name);
        $this->config = new Json(ROOT_DIR . '/config/' . $name . '.json');
        Init::require(!empty($this->config->getData()));
        Init::access((int) user_me::get()->group_id === Group::ROOT_ID);
    }
    
    protected function succeed(array $post, array $files)
    {
        foreach ($post as $name => $value) {
            $name = str_replace('->', '.', $name);
            if ($this->isBool($name)) $value = $this->toBool($value);
            $this->config->set($name, $value);
        }
        $this->config->save();
    }

    private function isBool(string $setting): bool
    {
        return $this->config->$setting === true
            || $this->config->$setting === false;
    }

    private function toBool(string $value): bool
    {
        return $value === 'true' || $value === '1' ? true : false;
    }
}