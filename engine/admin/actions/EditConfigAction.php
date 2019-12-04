<?php namespace engine\admin\actions;

use frame\actions\ActionBody;
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
class EditConfigAction extends ActionBody
{
    /** @var Json */
    private $config;

    public function initialize(array $get)
    {
        $name = $get['name'] ?? null;
        Init::require($name);
        $this->config = new Json(ROOT_DIR . '/config/' . $name . '.json');
        Init::require(!empty($this->config->getData()));
        Init::access((int) user_me::get()->group_id === Group::ROOT_ID);
    }
    
    public function succeed(array $post, array $files)
    {
        foreach ($post as $name => $value) {
            $name = str_replace('->', '.', $name);
            $this->config->set($name, $this->typize($name, $value));
        }
        $this->config->save();
    }

    /**
     * Преобразует значение переданной настройки (оно string или массив) в тип,
     * которая эта настройка имеет в конфиге, чтобы сохранить новое значение
     * в правильном типе.
     * 
     * @param mixed $postValue
     * @return mixed
     */
    private function typize(string $setting, $postValue)
    {
        if (is_bool($this->config->$setting)) 
            return $postValue === 'true' || $postValue === '1' ? true : false;
        else if (is_int($this->config->$setting)) return (int) $postValue;
        else if (is_float($this->config->$setting)) return (float) $postValue;
        else if (is_double($this->config->$setting)) return (double) $postValue;
        return $postValue;
    }
}