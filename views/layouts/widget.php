<?php /** @var frame\views\Layout $self */

$child = $self->loadChild();
$title = $child->getMeta('title');
?>

<div class="widget">
    <div class="widget__title"><?= $title ?></div>
    <div class="widget__content">
        <?php $child->show() ?>
    </div>
</div>