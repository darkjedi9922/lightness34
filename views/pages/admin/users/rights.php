<?php /** @var frame\views\Page $self */

use engine\admin\actions\EditRightsAction;
use frame\actions\ViewAction;
use engine\users\cash\my_group;
use engine\users\cash\user_me;
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
$action = new ViewAction(EditRightsAction::class, ['id' => $id]);
$me = user_me::get();
?>

<div class="content__header">
    <div class="breadcrumbs">
        <a href="/admin/users/groups" class="breadcrumbs__item breadcrumbs__item--link">Группы</a>
        <span class="breadcrumbs__divisor"></span>
        <span class="breadcrumbs__item breadcrumbs__item--current"><?= $group->name ?></span>
    </div>
</div>
<div class="box">
    <form action="<?= $action->getUrl() ?>" method="post">
        <div class="checkbox">
            <?php foreach ($modules as $module):
            /** @var Module $module */
            $rightsDesc = $module->createRightsDescription();
            if (!$rightsDesc) continue;
            $rightList = $rightsDesc->listRights();
            $rights = new GroupRights($rightsDesc, $module->getId(), $group->id);
            ?>
                <h3>Модуль <?= $module->getName() ?>
                </h3>
                <?php foreach ($rightList as $right => $desc): ?>
                    <input type="checkbox" id="rights-<?= $module->getId() ?>-<?= $right ?>" 
                        name="rights[<?= $module->getName() ?>][<?= $right ?>]" 
                        <?php if ($rights->can($right)) echo 'checked' ?>
                        <?php if ($group->id === $group::ROOT_ID) echo 'disabled'?>>
                    <label for="rights-<?= $module->getId() ?>-<?= $right ?>"><i class="fontello icon-ok"></i></label>
                    <span><?= $desc ?></span><br/>
                <?php endforeach ?>
                <br>
            <?php endforeach ?>
        </div>
        <button <?php if ($group->id === $group::ROOT_ID) echo 'disabled'?>>Сохранить</button>
    </form>
</div>