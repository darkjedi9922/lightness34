<?php /** @var frame\views\Page $self */

use frame\tools\Init;
use frame\lists\base\IdentityList;
use engine\statistics\stats\RouteStat;
use frame\route\Router;

Init::accessRight('admin', 'see-logs');

$routes = new IdentityList(RouteStat::class, ['time' => 'DESC']);

$self->setLayout('admin');
?>

<span class="content__title">Маршрутизация</span>
<div class="box">
    <div class="routes">
        <div class="routes__headers">
            <span class="routes__header routes__cell routes__cell--path">Path</span>
            <span class="routes__header routes__cell routes__cell--code">Code</span>
            <span class="routes__header routes__cell routes__cell--time">Time</span>
        </div>
        <?php foreach ($routes as $route) : /** @var RouteStat $route */ ?>
            <?php
            $router = new Router($route->url);
            $severity = 'ok';
            switch ((int)($route->code / 100)) {
                case 4:
                    $severity = 'warning';
                    break;
                case 5:
                    $severity = 'error';
                    break;
            }
            $empty = $router->pagename === '';
            $pagename = !$empty ? $router->pagename : 'index request';
            ?>
            <div class="routes__item">
                <div class="routes__route">
                    <span class="routes__cell routes__cell--path">
                        <span class="routes__pagename <?= $empty ? 'routes__pagename--index' : '' ?>">
                            <?= $pagename ?>
                        </span>
                        <?php if ($route->ajax) : ?>
                            <span class="routes__mark routes__mark--ajax">ajax</span>
                        <?php endif  ?>
                        <?php if ($route->type === $route::ROUTE_TYPE_ACTION) : ?>
                            <span class="routes__mark routes__mark--action">action</span>
                        <?php endif ?>
                    </span>
                    <div class="routes__cell routes__cell--code">
                        <span class="routes__code routes__code--<?= $severity ?>"><?= $route->code ?></span>
                    </div>
                    <div class="routes__cell routes__cell--time">
                        <span class="routes__time"><?= date('d.m.Y H:i', $route->time) ?></span>
                    </div>
                </div>
                <?php if (!empty($router->args)) : ?>
                    <div class="routes__details">
                        <span class="routes__subheader">Get</span>
                        <?php foreach ($router->args as $key => $value) : ?>
                            <div class="param">
                                <span class="routes__param-name"><?= $key ?></span>
                                <span class="routes__param-equals">=</span>
                                <span class="class routes__param-value <?= $value === '' ? 'routes__param-value--empty' : '' ?>"><?= $value !== '' ? $value : 'empty' ?></span>
                            </div>
                        <?php endforeach ?>
                    </div>
                <?php endif ?>
            </div>
        <?php endforeach ?>
    </div>
</div>