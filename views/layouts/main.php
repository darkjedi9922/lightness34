<?php /** @var frame\views\Layout $self */

use frame\auth\Auth;
use frame\views\Widget;
use engine\users\User;

$userRights = User::getMyRights('users');
$auth = new Auth;
$me = User::getMe();

$doShowMiniProfile = $auth->isLogged();
$doShowWelcomeWidget = !$auth->isLogged() && $userRights->can('add');
?>

<div class="header">
    <div class="header__container">
        <?php (new frame\views\Block('header-menu'))->show() ?>
    </div>
</div>
<div class="container">
    <div class="container__content">
        <?= $self->loadChild()->show() ?>
    </div>
    <?php if ($doShowMiniProfile || $doShowWelcomeWidget): ?>
    <div class="container__sidebox">
        <?= (new Widget($doShowMiniProfile ? 'mini-profile' : 'welcome'))->show() ?>
    </div>
    <?php endif ?>
</div>