<?php /** @var frame\views\Page $self */

use frame\tools\Init;
use engine\users\Group;
use frame\lists\base\IdentityList;
use engine\users\Gender;
use frame\actions\ViewAction;
use engine\admin\actions\AddGender;
use engine\admin\actions\DeleteGender;
use frame\tools\JsonEncoder;

Init::accessGroup(Group::ROOT_ID);

$genders = new IdentityList(Gender::class);
$addGender = new ViewAction(AddGender::class);
$deleteGender = new ViewAction(DeleteGender::class);

$nameErrors = [];
if ($addGender->hasError(AddGender::E_NO_NAME))
    $nameErrors[] = 'Название не указано';

$formProps = [
    'actionUrl' => $addGender->getUrl(),
    'method' => 'post',
    'fields' => [[
        'type' => 'text',
        'title' => 'Название',
        'name' => 'name',
        'defaultValue' => $addGender->getPost('name', ''),
        'errors' => $nameErrors
    ]],
    'buttonText' => 'Добавить',
    'short' => true
];
$formProps = JsonEncoder::forHtmlAttribute($formProps);
?>

<div class="content__header">
    <div class="breadcrumbs">
        <span class="breadcrumbs__item">Пользователи</span>
        <span class="breadcrumbs__divisor"></span>
        <span class="breadcrumbs__item breadcrumbs__item--current">Пол</span>
    </div>
</div>
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
<span class="content__title">Добавить</span>
<div class="box">
    <div class="react-form" data-props="<?= $formProps ?>"></div>
</div>