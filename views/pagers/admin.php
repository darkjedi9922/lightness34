<?php /** @var frame\lists\paged\PagerView $self */

use frame\cash\router as current_router;
use frame\route\Router;

$pager = $self->getPager();
$current = $pager->getCurrent();
$previous = $pager->getPevious();
$next = $pager->getNext();
$last = $pager->getLast();

// Дадим возможность принимать пользовательский маршрут вместо текущего.
$customRoute = $self->getMeta('route');
$router = $customRoute === null ? current_router::get() : new Router($customRoute);
?>

<div class="pager">
    <?php if ($current > 2): ?><a class="pager__item" href="<?= $router->toUrl(['p' => 1]) ?>">1</a><?php endif ?>
    <?php if ($current > 3): ?><span class="pager__spacing">...</span><?php endif ?>
    <?php if ($previous): ?><a class="pager__item" href="<?= $router->toUrl(['p' => $previous]) ?>"><?= $previous ?></a><?php endif ?>
    <a class="pager__item pager__item--current"><?= $current ?></a>
    <?php if ($next): ?><a class="pager__item" href="<?= $router->toUrl(['p' => $next]) ?>"><?= $next ?></a><?php endif ?>
    <?php if ($last - $current > 2): ?><span class="pager__spacing">...</span><?php endif ?>
    <?php if ($last - $current > 1): ?><a class="pager__item" href="<?= $router->toUrl(['p' => $last]) ?>"><?= $last ?></a><?php endif ?>
</div>