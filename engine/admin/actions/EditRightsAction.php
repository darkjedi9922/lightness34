<?php namespace engine\admin\actions;

use frame\actions\ActionBody;
use frame\auth\InitAccess;
use frame\route\InitRoute;
use engine\users\Group;
use frame\actions\fields\MixedField;
use frame\actions\fields\IntegerField;
use frame\auth\GroupRights;
use frame\modules\Module;
use frame\modules\Modules;

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
            'rights' => MixedField::class
        ];
    }

    public function initialize(array $get)
    {
        $myGroup = Group::getMine();
        InitAccess::access($myGroup->id === $myGroup::ROOT_ID);

        $this->id = $get['id']->get();
        InitRoute::require($this->id !== Group::ROOT_ID);

        $this->group = Group::selectIdentity($this->id);
        InitRoute::require((bool) $this->group);
    }

    public function succeed(array $post, array $files)
    {
        $rights = $post['rights']->get();
        $modules = Modules::getDriver()->toArray();
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