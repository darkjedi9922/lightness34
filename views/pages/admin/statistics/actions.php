<?php /** @var frame\views\Page $self */

use frame\tools\Init;
use engine\statistics\lists\ActionList;

Init::accessRight('admin', 'see-logs');

$actions = new ActionList;

$self->setLayout('admin');
?>

<div class="breadcrumbs">
    <span class="breadcrumbs__item">Статистика</span>
    <span class="breadcrumbs__divisor"></span>
    <span class="breadcrumbs__item breadcrumbs__item--current">Действия</span>
</div>
<div class="box">
    <?php foreach ($actions as $class): ?>
    <div><?= $class ?></div>
    <?php endforeach ?>
</div>