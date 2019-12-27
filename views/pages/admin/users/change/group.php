<?php /** @var frame\views\Page $self */

use frame\tools\Init;
use frame\lists\base\IdentityList;
use engine\users\Group;
use engine\users\User;
use engine\users\actions\ChangeUserGroupAction;
use frame\actions\ViewAction;
use engine\users\cash\user_me;

$me = user_me::get();

Init::access((int)$me->group_id === Group::ROOT_ID);

$id = (int)Init::requireGet('id');
$user = User::selectIdentity($id);

Init::require($user !== null);
Init::require((int)$user->group_id !== Group::ROOT_ID);

$groups = new IdentityList(Group::class);
$action = new ViewAction(ChangeUserGroupAction::class, ['uid' => $id]);
?>

<div class="breadcrumbs">
    <a href="/admin/users" class="breadcrumbs__item breadcrumbs__item--link">Пользователи</a>
    <span class="breadcrumbs__divisor"></span>
    <a href="/admin/users/profile/<?= $user->login ?>" 
        class="breadcrumbs__item breadcrumbs__item--link"><?= $user->login ?></a>
    <span class="breadcrumbs__divisor"></span>
    <span class="breadcrumbs__item breadcrumbs__item--current">Изменить группу</span>
</div>
<div class="box">
    <form action="<?= $action->getUrl() ?>" method="post">
        <div class="radio-classic">
            <?php foreach ($groups as $group) :
                if (
                    $group->id === GROUP::GUEST_ID
                    || $group->id === Group::ROOT_ID
                ) continue;
                ?>
                <input type="radio" name="group_id" value="<?= $group->id ?>" id="group-<?= $group->id ?>" <?php if ($user->group_id == $group->id) echo 'checked' ?>>
                <label for="group-<?= $group->id ?>" class="radio"><i></i></label><?= $group->name ?>
                <br />
            <?php endforeach ?>
        </div>
        <br /><button>Сохранить</button>
    </form>
</div>