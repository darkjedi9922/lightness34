<?php /** @var frame\views\Page $self */

use frame\cash\config;
use frame\tools\Init;
use engine\users\User;
use frame\lists\base\IdentityList;
use engine\users\Gender;
use engine\users\actions\ProfileEditAction;
use frame\actions\ViewAction;

$uid = (int)Init::requireGet('id');
$user = User::selectIdentity($uid);

Init::require($user !== null);
Init::accessOneRight('users', ['edit-all' => $user, 'edit-own' => $user]);

$genders = new IdentityList(Gender::class);
$action = new ViewAction(ProfileEditAction::class, ['id' => $uid]);
$config = config::get('users');
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
    <form action="<?= $action->getUrl() ?>" class="registration" method="post" enctype="multipart/form-data">
        <table>
            <tr>
                <td>Логин:</td>
                <td>
                    <input name="login" type="text" 
                        value="<?= $action->getPost('login', $user->login) ?>" 
                        style="width:100%">
                </td>
                <td>
                    <?php if ($action->hasError(ProfileEditAction::E_NO_LOGIN)) : ?>
                        <span class='error'>Логин не указан</span>
                    <?php endif ?>
                    <?php if ($action->hasError(ProfileEditAction::E_LONG_LOGIN)) : ?>
                        <span class='error'>Логин слишком большой</span>
                    <?php endif ?>
                    <?php if ($action->hasError(ProfileEditAction::E_LOGIN_EXISTS)) : ?>
                        <span class='error'>Такой логин уже занят</span>
                    <?php endif ?>
                    <?php if ($action->hasError(ProfileEditAction::E_INCORRECT_LOGIN)) : ?>
                        <span class='error'>Логин содержит недопустимые символы</span>
                    <?php endif ?>
                </td>
            </tr>
            <tr>
                <td>Новый пароль:</td>
                <td><input name="password" type="text" style="width:100%"></td>
                <td>
                    <?php if ($action->hasError(ProfileEditAction::E_LONG_PASSWORD)) : ?>
                        <span class='error'>Пароль слишком большой</span>
                    <?php endif ?>
                    <?php if ($action->hasError(ProfileEditAction::E_INCORRECT_PASSWORD)) : ?>
                        <span class='error'>Пароль содержит недопустимые символы</span>
                    <?php endif ?>
                </td>
            </tr>
            <tr>
                <td>E-mail:</td>
                <td>
                    <input name="email" type="text" value="<?= $action->getPost('email', $user->email) ?>" style="width:100%">
                </td>
                <td>
                    <?php if ($action->hasError(ProfileEditAction::E_INCORRECT_EMAIL)) : ?>
                        <span class='error'>E-mail заполнен некорректно</span>
                    <?php endif ?>
                </td>
            </tr>
            <tr>
                <td>Имя:</td>
                <td>
                    <input name="name" type="text" value="<?= $action->getPost('name', $user->name) ?>" style="width:100%">
                </td>
                <td>
                    <?php if ($action->hasError(ProfileEditAction::E_INCORRECT_NAME)) : ?>
                        <span class='error'>Имя содержит недопустимые символы</span>
                    <?php endif ?>
                </td>
            </tr>
            <tr>
                <td>Фамилия:</td>
                <td>
                    <input name="surname" type="text" value="<?= $action->getPost('surname', $user->surname) ?>" style="width:100%">
                </td>
                <td>
                    <?php if ($action->hasError(ProfileEditAction::E_INCORRECT_SURNAME)) : ?>
                        <span class='error'>Фамилия содержит недопустимые символы</span>
                    <?php endif ?>
                </td>
            </tr>
            <tr>
                <td>Пол:</td>
                <td>
                    <div class="radio">
                        <?php
                        $saved = (int)$action->getPost('gender_id', $user->gender_id);
                        foreach ($genders as $gender) : $checked = $saved === $gender->id;
                            ?>
                            <input type='radio' name='gender_id' id='gender-<?= $gender->id ?>' value='<?= $gender->id ?>' <?php if ($checked) echo 'checked'; ?>>
                            <label for="gender-<?= $gender->id ?>"><?= $gender->name ?></label>
                        <?php endforeach ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td>Аватар:</td>
                <td>
                    <div class="input-file">
                        <span class="button" id="profile-edit-avatar-button" style="position:relative;bottom:2px">Загрузить...</span>
                        <input type="text" id="profile-edit-avatar-text" disabled style="width:9.5em">
                        <input type="file" class="file" id="profile-edit-avatar-file" name="avatar">
                    </div>
                    <script src="/public/scripts/input-value-updating.js"></script>
                    <script type="text/javascript">
                        setUpdatingInputValueFromInput('profile-edit-avatar-file', 'profile-edit-avatar-text')
                    </script>
                </td>
                <td>
                    <?php if ($action->hasError(ProfileEditAction::E_AVATAR_SIZE)) : ?>
                        <span class='error'>Размер превышает
                            <?= $config->{'avatar.max_size.value'} ?>
                            <?= $config->{'avatar.max_size.unit'} ?></span>
                    <?php endif ?>
                    <?php if ($action->hasError(ProfileEditAction::E_AVATAR_TYPE)) : ?>
                        <span class='error'>Некорректный тип файла</span>
                    <?php endif ?>
                </td>
            </tr>
        </table>
        <button>Сохранить</button>
    </form>
</div>