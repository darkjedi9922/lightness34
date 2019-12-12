<?php /** @var frame\views\Page $self */

use frame\tools\Init;
use engine\users\Group;
use engine\users\Gender;
use frame\actions\ViewAction;
use engine\admin\actions\EditGender;

Init::accessGroup(Group::ROOT_ID);

$id = (int)Init::requireGet('id');
$gender = Gender::selectIdentity($id);

Init::require($gender !== null);

$edit = new ViewAction(EditGender::class, ['id' => $id]);

$self->setLayout('admin');
?>

<div class="breadcrumbs">
    <a href="/admin/users/genders" class="breadcrumbs__item breadcrumbs__item--link">Пол</a>
    <span class="breadcrumbs__divisor"></span>
    <span class="breadcrumbs__item breadcrumbs__item--current">ID <?= $gender->id ?></span>
</div>
<div class="box">
    <form action="<?= $edit->getUrl() ?>" method="post">
        <table>
            <tr>
                <td>Название:</td>
                <td>
                    <input name="name" type="text" value="<?= $edit->getPost('name', $gender->name) ?>">
                    <?php if ($edit->hasError(EditGender::E_NO_NAME)) : ?>
                        <span class="error">Название не указано</span>
                    <?php endif ?>
                </td>
            </tr>
        </table>
        <button>Сохранить</button>
    </form>
</div>