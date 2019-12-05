<?php /** @var frame\views\Page $self */

use frame\tools\Init;
use engine\users\Group;
use frame\lists\IdentityList;
use engine\users\Gender;
use frame\actions\ViewAction;
use engine\admin\actions\AddGender;
use engine\admin\actions\DeleteGender;

Init::accessGroup(Group::ROOT_ID);

$self->setLayout('admin');

$genders = new IdentityList(Gender::class);
$addGender = new ViewAction(AddGender::class);
$deleteGender = new ViewAction(DeleteGender::class);
?>

<div class="box">
    <table width="100%">
        <?php foreach ($genders as $gender): /** @var Gender $gender */ ?>
            <?php $deleteGender->setArg('id', $gender->id); ?>
            <tr>
                <td>ID: <?= $gender->id ?></td>
                <td><?= $gender->name ?></td>
                <td><a href="/admin/users/gender?id=<?= $gender->id ?>" class="button">Редактировать</a></td>
                <td><?php if (!$gender->isDefault()) : ?>
                    <a href="<?= $deleteGender->getUrl() ?>" class="button">Удалить</a>
                <?php endif ?></td>
            </tr>
        <?php endforeach ?>
    </table>
</div>
<div class="box">
    <h3>Добавить</h3><br>
    <?php if ($addGender->hasError(AddGender::E_NO_NAME)) : ?>
        <span class="error" style="margin-bottom:7px">Название не указано</span>
    <?php endif ?>
    <form action="<?= $addGender->getUrl() ?>" method="post">
        <table>
            <tr>
                <td>Название:</td>
                <td><input name="name" type="text"></td>
            </tr>
        </table>
        <button>Добавить</button>
    </form>
</div>