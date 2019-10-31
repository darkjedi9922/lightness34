<?php /** @var frame\views\Layout $self */ 

use frame\auth\Auth;
use frame\views\Widget;

$self->setLayout('base');

$auth = new Auth;
?>

<div class="header">
    <div class="header__container">
        <?php (new frame\views\Block('header-menu'))->show() ?>
    </div>
</div>
<div class="container">
    <div class="container__content">
        <?= $self->showChild() ?>
    </div>
    <div class="container__sidebox">
        <?= (new Widget($auth->isLogged() ? 'mini-profile' : 'welcome'))->show() ?>
    </div>
</div>