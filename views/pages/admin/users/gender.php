<?php /** @var frame\views\Page $self */

use frame\tools\Init;
use engine\users\Gender;
use frame\actions\ViewAction;
use engine\admin\actions\EditGender;
use frame\tools\JsonEncoder;

Init::accessRight('users', 'configure-genders');
$id = (int)Init::requireGet('id');
$gender = Gender::selectIdentity($id);

Init::require($gender !== null);

$edit = new ViewAction(EditGender::class, ['id' => $id]);

$nameErrors = [];
if ($edit->hasError(EditGender::E_NO_NAME))
    $nameErrors[] = 'Название не указано';

$formProps = [
    'actionUrl' => $edit->getUrl(),
    'method' => 'post',
    'fields' => [[
        'type' => 'text',
        'title' => 'Название',
        'name' => 'name',
        'defaultValue' => $edit->getPost('name', $gender->name),
        'errors' => $nameErrors
    ]],
    'buttonText' => 'Сохранить',
    'short' => true
];
$formProps = JsonEncoder::forHtmlAttribute($formProps);
?>

<div class="content__header">
    <div class="breadcrumbs">
        <a href="/admin/users/genders" class="breadcrumbs__item breadcrumbs__item--link">Пол</a>
        <span class="breadcrumbs__divisor"></span>
        <span class="breadcrumbs__item breadcrumbs__item--current">ID <?= $gender->id ?></span>
    </div>
</div>
<div class="box">
    <div class="react-form" data-props="<?= $formProps ?>"></div>
</div>