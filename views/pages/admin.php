<?php /** @var frame\views\Page $self */

use frame\cash\prev_router;
use engine\admin\actions\LoginAction;
use frame\actions\ViewAction;
use engine\admin\Auth;
use frame\tools\JsonEncoder;

$self->setLayout('admin-base');
$self->setMeta('admin-login-page-flag', true);

$action = new ViewAction(LoginAction::class);
$auth = new Auth;
$prevRoute = prev_router::get();
$isTimeup = $auth->isTimeup() && $prevRoute && $prevRoute->getPathPart(0) == 'admin';

$loginErrors = [];
if ($isTimeup) $loginErrors[] = 'Время сессии вышло';
if ($action->hasError(LoginAction::E_WRONG_PASSWORD))
    $loginErrors[] = 'Неверный пароль';

$formProps = JsonEncoder::forHtmlAttribute([
    'actionUrl' => $action->getUrl(),
    'method' => 'post',
    'fields' => [[
        'title' => 'Пароль',
        'name' => 'password',
        'type' => 'password'
    ]],
    'errors' => $loginErrors,
    'buttonText' => 'Войти'
]);
?>

<div class="centered-wrapper">
    <div class="box box--login">
        <center><h2>Вход в админ-панель</h2></center>
        <div class="react-form" data-props="<?= $formProps ?>"></div>
    </div>
</div>

<?php $auth->clearTimeupFlag(); ?>