<?php /** @var frame\views\Widget $self */ 

$anchor = $self->getMeta('anchor');
$desc = $self->getMeta('desc');
?>

<div class="fundamentals__item">
    <a <?php 
        if ($anchor !== null): ?>name="<?= $self->getMeta('anchor') ?>"<?php endif ?> 
        class="fundamentals__anchor"
    ></a>
    <img src="<?= $self->getMeta('icon') ?>" class="fundamentals__icon">
    <span class="fundamentals__title"><?= $self->getMeta('title') ?></span>
    <?php if ($desc !== null): ?>
        <p class="fundamentals__desc"><?= $self->getMeta('desc') ?></p>
    <?php endif ?>
</div>