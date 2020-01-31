<?php namespace engine\admin\actions;

use engine\users\cash\my_group;
use frame\actions\ActionBody;
use frame\tools\Init;
use engine\users\Group;
use frame\actions\fields\BaseField;
use frame\actions\fields\IntegerField;
use frame\core\Core;
use frame\modules\GroupRights;
use frame\modules\Module;

/**
 * Параметры:
 * id: id группы пользователя, права которой нужно отредактировать.
 * Она должна существовать.
 * Права: root.
 * Требования: заданная группа не должна быть root.
 * Данные: двумерный массив ВСЕХ прав. Первый индекс: name модуля, второй: имя права.
 * Пример: <input name="rights[admin][enter]">
 * Если какого-либо права не будет в верстке, оно сочтется за off
 * (Если оно off, в массив post оно вообще не передается)
 */
class EditRightsAction extends ActionBody
{
    private $id;
    /** @var Group */
    private $group;

    public function listGet(): array
    {
        return [
            'id' => IntegerField::class
        ];
    }

    public function listPost(): array
    {
        return [
            'rights' => BaseField::class
        ];
    }

    public function initialize(array $get)
    {
        $myGroup = my_group::get();
        Init::access($myGroup->id === $myGroup::ROOT_ID);

        $this->id = $get['id']->get();
        Init::require($this->id !== Group::ROOT_ID);

        $this->group = Group::selectIdentity($this->id);
        Init::require((bool) $this->group);
    }

    public function succeed(array $post, array $files)
    {
        $rights = $post['rights']->get();
        $modules = Core::$app->getModules();
        foreach ($modules as $moduleName => $module) {
            /** @var Module $module */
            $desc = $module->createRightsDescription();
            if (!$desc) continue;

            $rightList = $desc->listRights();
            $groupRights = new GroupRights($desc, $module->getId(), $this->id);
            foreach ($rightList as $rightName => $rightDesc) {
                $canValue = ($rights[$moduleName][$rightName] ?? '0') === '1'; 
                $groupRights->set($rightName, $canValue);
            }
            $groupRights->save();
        }
    }
}