<?php /** @var frame\views\Widget $self */ ?>

<div class="fundamentals__item">
    <img src="/public/images/icons/<?= $self->getMeta('icon') ?>.png" class="fundamentals__icon">
    <span class="fundamentals__title"><?= $self->getMeta('title') ?></span>
    <p class="fundamentals__desc"><?= $self->getMeta('desc') ?></p>
</div>