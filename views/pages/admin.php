<?php /** @var frame\views\Page $self */

use frame\cash\prev_router;
use engine\admin\actions\LoginAction;
use engine\admin\Auth;

$self->setLayout('admin-base');
$self->setMeta('admin-login-page-flag', true);

$action = new LoginAction;
$auth = new Auth;
$prevRoute = prev_router::get();
$isTimeup = $auth->isTimeup() && $prevRoute && $prevRoute->getPathPart(0) == 'admin'; 
?>

<div class="centered-wrapper">
    <div class="box box--login">
        <center><h2>Вход в админ-панель</h2></center>
        <?php if ($isTimeup): ?>
            <span style="font-weight:bold;color:red">Время сессии вышло</span><br><br>
        <?php endif?>
        <?php if ($action->hasError($action::E_WRONG_PASSWORD)): ?>
            <span class='error' style="margin-bottom:10px">Неверный пароль</span><br/>
        <?php endif ?>
        <form action="<?= $action->getUrl() ?>" method="post">
            Пароль: <input type="password" name="password">
            <br><button>Войти</button>
        </form>
    </div>
</div>

<?php $auth->clearTimeupFlag(); ?>