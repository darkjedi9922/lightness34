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
<div class="box box--table">
    <table class="table routes">
        <tr class="table__headers">
            <td class="table__header">Path</td>
            <td class="table__header">Code</td>
            <td class="table__header">Time</td>
        </tr>
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
            <tbody class="table__item-wrapper">
                <tr class="table__item">
                    <td class="table__cell">
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
                    </td>
                    <td class="table__cell">
                        <span class="routes__code routes__code--<?= $severity ?>"><?= $route->code ?></span>
                    </td>
                    <td class="table__cell">
                        <span class="routes__time"><?= date('d.m.Y H:i', $route->time) ?></span>
                    </td>
                </tr>
                <tr class="table__item-details-wrapper">
                    <td class="table__item-details" colspan="100">
                        <?php if ($route->viewfile) : ?>
                            <span class="table__subheader">View file</span>
                            <div class="table__detail-wrapper">
                                <span class="routes__param-value"><?= $route->viewfile ?></span>
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
                                <span class="table__subheader">Dynamic Page Arguments</span>
                                <div class="table__detail-wrapper">
                                    <?php foreach ($paramsIterator as $param) : /** @var DynamicRouteParam $param */ ?>
                                        <div class="table__item-detail">
                                            <span class="routes__param-name"><?= $param->index ?></span>
                                            <span class="routes__param-equals">=</span>
                                            <span class="routes__param-value"><?= $param->value ?></span>
                                        </div>
                                    </div>
                                <?php endforeach ?>
                            <?php endif ?>
                        <?php endif ?>
                        <?php if (!empty($router->args)) : ?>
                            <span class="table__subheader">Get</span>
                            <div class="table__detail-wrapper">
                                <?php foreach ($router->args as $key => $value) : ?>
                                    <div class="table__item-detail">
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
                    </td>
                </tr>
            </tbody>
        <?php endforeach ?>
    </table>
</div>