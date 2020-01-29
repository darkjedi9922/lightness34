<?php /** @var frame\views\Page $self */

use engine\admin\actions\EditRightsAction;
use frame\actions\ViewAction;
use frame\tools\JsonEncoder;
use engine\users\cash\my_group;
use engine\users\Group;
use frame\core\Core;
use frame\modules\GroupRights;
use frame\modules\Module;
use frame\tools\Init;

$myGroup = my_group::get();

Init::access($myGroup->id === $myGroup::ROOT_ID);

$id = (int) Init::requireGet('id');
$group = Group::selectIdentity($id);

Init::require((bool) $group);

$modules = Core::$app->getModules();
$edit = new ViewAction(EditRightsAction::class, ['id' => $id]);

$fieldGroups = [];
foreach ($modules as $module) {
    /** @var Module $module */
    $rightsDesc = $module->createRightsDescription();
    if (!$rightsDesc) continue;
    $rightList = $rightsDesc->listRights();
    $rights = new GroupRights($rightsDesc, $module->getId(), $group->id);
    
    $rightFields = [];
    foreach ($rightList as $right => $desc) {
        $rightFields[] = [
            'type' => 'checkbox',
            'name' => "rights[{$module->getName()}][$right]",
            'label' => $desc,
            'defaultChecked' => $rights->can($right),
            'disabled' => $group->id === $group::ROOT_ID
        ];
    }

    $fieldGroups[] = [
        'type' => 'group',
        'title' => 'Модуль ' . $module->getName(),
        'fields' => $rightFields
    ];
}

$formProps = [
    'actionUrl' => $edit->getUrl(),
    'method' => 'post',
    'fields' => $fieldGroups,
    'short' => true,
    'buttonText' => 'Сохранить'
];
$formProps = JsonEncoder::forHtmlAttribute($formProps);
?>

<div class="content__header">
    <div class="breadcrumbs">
        <a href="/admin/users/groups" class="breadcrumbs__item breadcrumbs__item--link">Группы</a>
        <span class="breadcrumbs__divisor"></span>
        <span class="breadcrumbs__item breadcrumbs__item--current"><?= $group->name ?></span>
    </div>
</div>
<div class="box">
    <div class="react-form" data-props="<?= $formProps ?>"></div>
</div>