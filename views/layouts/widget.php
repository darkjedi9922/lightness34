<?php /** @var frame\views\Layout $self */

$title = $self->getChildMeta('title');
?>

<div class="widget">
    <div class="widget__title"><?= $title ?></div>
    <div class="widget__content">
        <?php $self->showChild() ?>
    </div>
</div>