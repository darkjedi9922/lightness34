<?php namespace engine\admin\actions;

use engine\users\cash\my_group;
use frame\actions\Action;
use frame\tools\Init;
use engine\users\Group;
use frame\Core;
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
class EditRightsAction extends Action
{
    private $id;
    /** @var Group */
    private $group;

    protected function initialize()
    {
        $myGroup = my_group::get();
        Init::access($myGroup->id === $myGroup::ROOT_ID);

        $this->id = (int) $this->getData('get', 'id', -1);
        Init::require($this->id !== -1);
        Init::require($this->id !== Group::ROOT_ID);

        $this->group = Group::selectIdentity($this->id);
        Init::require((bool) $this->group);
    }

    protected function succeed()
    {
        // Если какой-либо модуль вообще не был передан, добавляем ему все
        // права как off (пустой массив)
        $rights = $this->getData('post', 'rights', []);
        $modules = Core::$app->getModules();
        foreach ($modules as $moduleName => $module) {
            /** @var Module $module */
            $desc = $module->createRightsDescription();
            if (!$desc) continue;

            $rightList = $desc->listRights();
            $groupRights = new GroupRights($desc, $module->getId(), $this->id);
            foreach ($rightList as $rightName => $rightDesc) {
                $canValue = isset($rights[$moduleName][$rightName]); 
                $groupRights->set($rightName, $canValue);
            }
            $groupRights->save();
        }
    }
}