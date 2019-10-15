<?php /** @var frame\views\Widget $self */

use engine\users\actions\LoginAction;
use frame\actions\Action;

$me = cash\user_me::get();
//$action = new Action(LoginAction::class);

$self->setMeta('title', 'Вход');
?>

<?php //if ($action->hasDataError('post', 'login', 'base/emptiness')) : ?>
    <span class='error' style="margin-bottom:10px">Логин не указан</span>
<?php //endif ?>
<?php //if ($action->hasDataError('post', 'login', 'base/emptiness')) : ?>
    <span class='error' style="margin-bottom:10px">Пароль не указан</span>
<?php //endif ?>
<?php //if ($action->hasError(LoginAction::E_WRONG_PASSWORD)) : ?>
    <span class='error' style="margin-bottom:10px">Неверный пароль</span>
<?php //endif ?>
<form class="login-form" action="<?php //$action->getUrl() ?>" method="post">
    <input class="login-form__input login-form__input--login" name="login" type="text" placeholder="Логин" value="<?php // $action->getData('post', 'login') ?>">
    <input class="login-form__input login-form__input--password" name="password" type="password" placeholder="Пароль">
    <div class="login-form__checkbox" style="margin-bottom:10px">
        <input type="hidden" name="remember" value="0">
        <input type="checkbox" name="remember" id="remember-checkbox" value="<?php //$action->getData('post', 'remember') ?>">
        <label for="remember-checkbox"><i class="fontello icon-ok"></i></label><span>Запомнить меня</span>
    </div>
    <button class="form__button">Войти</button>
    <a class="form__button" style="margin-left:5px;" href="/registration">Регистрация</a>
</form>