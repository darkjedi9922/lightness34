<?php /** @var frame\views\Layout $self */

use frame\auth\Auth;
use frame\views\Widget;
use engine\users\cash\my_rights;
use engine\users\cash\user_me;

$userRights = my_rights::get('users');
$auth = new Auth;
$me = user_me::get();

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