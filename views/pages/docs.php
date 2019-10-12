<?php /** @var frame\views\Page $this */

use frame\views\Block;

?>

<div class="header">
    <div class="header__container">
        <?php (new Block('header-menu'))->show() ?>
    </div>
</div>
<div class="slide">
    <div class="slide__container">
        <span class="slide__header">Начало</span>
    </div>
    <div class="slide__container">
        <span class="slide__header">Конфиг</span>
    </div>
    <div class="slide__container">
        <span class="slide__header">Виды</span>
    </div>
</div>