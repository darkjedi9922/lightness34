<?php /** @var frame\views\Layout $self */

use frame\views\Block;
?>

<div class="container">
    <div rowspan="2" class="container__sidebox"><?php (new Block('admin/left'))->show() ?></div>
    <div class="container__body">
        <div class="container__head-bar head-bar"><?php (new Block('admin/headbar'))->show() ?></div>
        <div class="container__content content"><?php $self->loadChild()->show() ?></div>
    </div>
</div>