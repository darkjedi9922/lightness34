<?php /** @var frame\views\Widget $self */

use engine\users\actions\LoginAction;
use frame\actions\ViewAction;
use engine\users\cash\user_me;

$me = user_me::get();
$action = new ViewAction(LoginAction::class);

$self->setMeta('title', 'Вход');
?>

<a name="loginform"></a>

<?php if ($action->hasError(LoginAction::E_NO_LOGIN)) : ?>
    <span class='error' style="margin-bottom:10px">Логин не указан</span>
<?php endif ?>
<?php if ($action->hasError(LoginAction::E_NO_PASSWORD)) : ?>
    <span class='error' style="margin-bottom:10px">Пароль не указан</span>
<?php endif ?>
<?php if ($action->hasError(LoginAction::E_WRONG_PASSWORD)) : ?>
    <span class='error' style="margin-bottom:10px">Неверный пароль</span>
<?php endif ?>
<form class="login-form" action="<?= $action->getUrl() ?>" method="post">
    <div class="login-form__fields">
        <div class="login-form__field">
            <i class="login-form__icon icon-user"></i>
            <input class="login-form__input" name="login" type="text" placeholder="Логин" value="<?= $action->getPost('login') ?>">
        </div>
    </div>
    <div class="login-form__fields">
        <div class="login-form__field">
            <i class="login-form__icon icon-key"></i>
            <input class="login-form__input" name="password" type="password" placeholder="Пароль">
        </div>
    </div>
    <div class="login-form__fields">
        <button class="form__button">Войти</button>
        <div class="login-form__checkbox">
            <input type="checkbox" name="remember" id="remember-checkbox" <?= $action->getPost('remember') ? 'checked' : '' ?>>
            <label for="remember-checkbox"><i class="icon-ok"></i></label><span>Запомнить меня</span>
        </div>
    </div>
</form>