<?php
use frame\config\ConfigRouter;

$config = ConfigRouter::getDriver()->findConfig('core');
?>

<div class="header__header">
    <a href="/" class="header__logo"><?= $config->{'site.name'} ?></a>
    <!-- <div class="header__menu header-menu">
        <a href="/articles" class="header-menu__item">Статьи</a>
        <a href="#" class="header-menu__item">О нас</a>
    </div>
    <a href="#" class="header__button">Скачать</a> -->
    <span class="header__label">PHP-фреймворк для Web</span>
</div>