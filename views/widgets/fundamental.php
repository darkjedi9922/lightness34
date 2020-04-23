<?php /** @var frame\views\Widget $self */ ?>

<div class="fundamentals__item">
    <a name="<?= $self->getMeta('icon') ?>" class="fundamentals__anchor"></a>
    <img src="/public/images/icons/<?= $self->getMeta('icon') ?>.png" class="fundamentals__icon">
    <span class="fundamentals__title"><?= $self->getMeta('title') ?></span>
    <p class="fundamentals__desc"><?= $self->getMeta('desc') ?></p>
</div>