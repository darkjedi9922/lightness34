<?php /** @var frame\views\Pager $self */ 

$pager = $self->getPager();
$current = $pager->getCurrent();
$previous = $pager->getPevious();
$next = $pager->getNext();
$last = $pager->getLast();
?>

<div class="pager">
    <?php if ($current > 2): ?><a class="pager__item" href="<?= $pager->toLink('p', 1) ?>">1</a><?php endif ?>
    <?php if ($current > 3): ?><span class="pager__spacing">...</span><?php endif ?>
    <?php if ($previous): ?><a class="pager__item" href="<?= $pager->toLink('p', $previous) ?>"><?= $previous ?></a><?php endif ?>
    <a class="pager__item pager__item--current"><?= $current ?></a>
    <?php if ($next): ?><a class="pager__item" href="<?= $pager->toLink('p', $next) ?>"><?= $next ?></a><?php endif ?>
    <?php if ($last - $current > 2): ?><span class="pager__spacing">...</span><?php endif ?>
    <?php if ($last - $current > 1): ?><a class="pager__item" href="<?= $pager->toLink('p', $last) ?>"><?= $last ?></a><?php endif ?>
</div>