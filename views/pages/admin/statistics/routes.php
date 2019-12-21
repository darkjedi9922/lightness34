<?php /** @var frame\views\Page $self */

use frame\tools\Init;
use frame\lists\base\IdentityList;
use engine\statistics\stats\RouteStat;
use frame\route\Router;
use frame\lists\iterators\IdentityIterator;
use frame\database\Records;
use engine\statistics\stats\DynamicRouteParam;

Init::accessRight('admin', 'see-logs');

$routes = new IdentityList(RouteStat::class, ['time' => 'DESC']);

$self->setLayout('admin');
?>

<div class="breadcrumbs">
    <span class="breadcrumbs__item">Статистика</span>
    <span class="breadcrumbs__divisor"></span>
    <span class="breadcrumbs__item breadcrumbs__item--current">Маршруты</span>
</div>
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
                        <?php if ($route->type === $route::ROUTE_TYPE_DYNAMIC_PAGE) : ?>
                            <span class="routes__mark routes__mark--dynamic">dynamic</span>
                        <?php endif ?>
                    </span>
                    <div class="routes__cell routes__cell--code">
                        <span class="routes__code routes__code--<?= $severity ?>"><?= $route->code ?></span>
                    </div>
                    <div class="routes__cell routes__cell--time">
                        <span class="routes__time"><?= date('d.m.Y H:i', $route->time) ?></span>
                    </div>
                </div>
                <div class="routes__details">
                    <?php if ($route->viewfile) : ?>
                        <div class="routes__get">
                            <span class="routes__subheader">View file</span>
                            <div class="param">
                                <span class="routes__param-value"><?= $route->viewfile ?></span>
                            </div>
                        </div>
                    <?php endif ?>
                    <?php if ($route->type === $route::ROUTE_TYPE_DYNAMIC_PAGE) :
                        $params = Records::select(DynamicRouteParam::getTable(), [
                            'route_id' => $route->id
                        ])->load();
                        ?>
                        <?php if ($params->count()) :
                            $paramsIterator = new IdentityIterator(
                                $params,
                                DynamicRouteParam::class
                            );
                            ?>
                            <div class="routes__get">
                                <span class="routes__subheader">Dynamic Page Arguments</span>
                                <?php foreach ($paramsIterator as $param) : /** @var DynamicRouteParam $param */ ?>
                                    <div class="param">
                                        <span class="routes__param-name"><?= $param->index ?></span>
                                        <span class="routes__param-equals">=</span>
                                        <span class="routes__param-value"><?= $param->value ?></span>
                                    </div>
                                <?php endforeach ?>
                            </div>
                        <?php endif ?>
                    <?php endif ?>
                    <?php if (!empty($router->args)) : ?>
                        <div class="routes__get">
                            <span class="routes__subheader">Get</span>
                            <?php foreach ($router->args as $key => $value) : ?>
                                <div class="param">
                                    <span class="routes__param-name"><?= $key ?></span>
                                    <span class="routes__param-equals">=</span>
                                    <span class="routes__param-value <?= $value === '' ? 'routes__param-value--empty' : '' ?>"><?= $value !== '' ? $value : 'empty' ?></span>
                                </div>
                            <?php endforeach ?>
                        </div>
                    <?php endif ?>
                    <?php if ($route->code_info) : ?>
                        <span class="routes__status routes__status--<?= $severity ?>">
                            <span class="routes__status-code">Status <?= $route->code ?></span>
                            <span class="routes__status-message"><?= $route->code_info ?></span>
                        </span>
                    <?php endif ?>
                </div>
            </div>
        <?php endforeach ?>
    </div>
</div>