<?php /** @var frame\views\Page $self */

use frame\stdlib\cash\config;
use frame\auth\InitAccess;
use frame\route\InitRoute;
use engine\users\User;
use frame\lists\base\IdentityList;
use engine\users\Gender;
use engine\users\actions\ProfileEditAction;
use frame\actions\ViewAction;
use frame\tools\JsonEncoder;

$uid = (int)InitRoute::requireGet('id');
$user = User::selectIdentity($uid);

InitRoute::require($user !== null);
InitAccess::accessOneRight('users', ['edit-all' => [$user], 'edit-own' => [$user]]);

$genders = new IdentityList(Gender::class);
$action = new ViewAction(ProfileEditAction::class, ['id' => $uid]);
$config = config::get('users');

$loginErrors = [];
if ($action->hasError(ProfileEditAction::E_NO_LOGIN))
    $loginErrors[] = 'Логин не указан';
if ($action->hasError(ProfileEditAction::E_LONG_LOGIN))
    $loginErrors[] = 'Логин слишком большой';
if ($action->hasError(ProfileEditAction::E_LOGIN_EXISTS))
    $loginErrors[] = 'Такой логин уже занят';
if ($action->hasError(ProfileEditAction::E_INCORRECT_LOGIN))
    $loginErrors[] = 'Логин содержит недопустимые символы';

$passwordErrors = [];
if ($action->hasError(ProfileEditAction::E_LONG_PASSWORD))
    $passwordErrors[] = 'Пароль слишком большой';
if ($action->hasError(ProfileEditAction::E_INCORRECT_PASSWORD))
    $passwordErrors[] = 'Пароль содержит недопустимые символы';

$emailErrors = [];
if ($action->hasError(ProfileEditAction::E_INCORRECT_EMAIL))
    $emailErrors[] = 'Email заполнен некорректно';

$nameErrors = [];
if ($action->hasError(ProfileEditAction::E_INCORRECT_NAME))
    $nameErrors[] = 'Имя содержит недопустимые символы';

$surnameErrors = [];
if ($action->hasError(ProfileEditAction::E_INCORRECT_SURNAME))
    $surnameErrors[] = 'Фамилия содержит недопустимые символы';

$fileErrors = [];
if ($action->hasError(ProfileEditAction::E_AVATAR_SIZE)) {
    $maxSizeValue = $config->{'avatar.max_size.value'};
    $maxSizeUnit = $config->{'avatar.max_size.unit'};
    $fileErrors[] = "Размер превышает $maxSizeValue $maxSizeUnit";
}
if ($action->hasError(ProfileEditAction::E_AVATAR_TYPE))
    $fileErrors[] = 'Некорректный тип файла';

$genderValues = [];
foreach ($genders as $gender) {
    /** @var Gender $gender */
    $genderValues[] = [
        'label' => $gender->name,
        'value' => (string)$gender->id
    ];
}

$formProps = [
    'actionUrl' => $action->getUrl(),
    'method' => 'post',
    'multipart' => true,
    'fields' => [[
        'title' => 'Логин',
        'name' => 'login',
        'type' => 'text',
        'defaultValue' => $action->getPost('login', $user->login),
        'errors' => $loginErrors
    ], [
        'title' => 'Новый пароль',
        'name' => 'password',
        'type' => 'password',
        'errors' => $passwordErrors
    ], [
        'title' => 'Email',
        'name' => 'email',
        'type' => 'text',
        'defaultValue' => $action->getPost('email', $user->email),
        'errors' => $emailErrors
    ], [
        'title' => 'Имя',
        'name' => 'name',
        'type' => 'text',
        'defaultValue' => $action->getPost('name', $user->name),
        'errors' => $nameErrors
    ], [
        'title' => 'Фамилия',
        'name' => 'surname',
        'type' => 'text',
        'defaultValue' => $action->getPost('surname', $user->surname),
        'errors' => $surnameErrors
    ], [
        'title' => 'Пол',
        'name' => 'gender_id',
        'type' => 'radio',
        'values' => $genderValues,
        'currentValue' => (string)$action->getPost('gender_id', $user->gender_id)
    ], [
        'title' => 'Аватар',
        'name' => 'avatar',
        'type' => 'file',
        'errors' => $fileErrors
    ]],
    'buttonText' => 'Сохранить',
    'className' => 'form--short'
];

$formProps = JsonEncoder::forHtmlAttribute($formProps);
?>

<div class="content__header">
    <div class="breadcrumbs">
        <a href="/admin/users" class="breadcrumbs__item breadcrumbs__item--link">Пользователи</a>
        <span class="breadcrumbs__divisor"></span>
        <a href="/admin/users/profile/<?= $user->login ?>" class="breadcrumbs__item breadcrumbs__item--link"><?= $user->login ?></a>
        <span class="breadcrumbs__divisor"></span>
        <span class="breadcrumbs__item breadcrumbs__item--current">Редактировать профиль</span>
    </div>
</div>

<div class="box">
    <div class="react-form" data-props="<?= $formProps ?>"></div>
</div>