<?php /** @var frame\views\Page $self */

use frame\tools\Init;
use frame\lists\base\IdentityList;
use engine\statistics\stats\RouteStat;
use frame\route\Router;
use frame\lists\iterators\IdentityIterator;
use frame\database\Records;
use engine\statistics\stats\DynamicRouteParam;

Init::accessRight('admin', 'see-logs');

$routes = new IdentityList(RouteStat::class, ['id' => 'DESC']);
?>

<div class="content__header">
    <div class="breadcrumbs">
        <span class="breadcrumbs__item">Мониторинг</span>
        <span class="breadcrumbs__divisor"></span>
        <span class="breadcrumbs__item breadcrumbs__item--current">Маршруты</span>
    </div>
</div>
<div class="box box--table">
    <table class="table routes">
        <tr class="table__headers">
            <td class="table__header">Path</td>
            <td class="table__header">Duration</td>
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
                        <span class="routes__duration"><?= $route->duration_sec ?> sec</span>
                    </td>
                    <td class="table__cell">
                        <span class="routes__code routes__code--<?= $severity ?>"><?= $route->code ?></span>
                    </td>
                    <td class="table__cell">
                        <span class="routes__time"><?= date('d.m.Y H:i', $route->time) ?></span>
                    </td>
                </tr>
                <tr class="table__details-wrapper">
                    <td class="table__details" colspan="100">
                        <?php if ($route->viewfile) : ?>
                            <div class="details">
                                <span class="details__header">View file</span>
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
                                <div class="details">
                                    <span class="details__header">Dynamic Page Arguments</span>
                                    <?php foreach ($paramsIterator as $param) : /** @var DynamicRouteParam $param */ ?>
                                        <div class="param">
                                            <span class="param__name"><?= $param->index ?></span>
                                            <span class="param__value"><?= $param->value ?></span>
                                        </div>
                                    <?php endforeach ?>
                                </div>
                            <?php endif ?>
                        <?php endif ?>
                        <?php if (!empty($router->args)) : ?>
                            <div class="details">
                                <span class="details__header">Get</span>
                                <?php foreach ($router->args as $key => $value) : ?>
                                    <div class="param">
                                        <span class="param__name"><?= $key ?></span>
                                        <span class="param__value <?= $value === '' ? 'param__value--empty' : '' ?>"><?= $value !== '' ? $value : 'empty' ?></span>
                                    </div>
                                <?php endforeach ?>
                            </div>
                        <?php endif ?>
                        <?php if ($route->code_info) : ?>
                            <div class="details">
                                <span class="status status--<?= $severity ?>">
                                    <span class="status__name">Status <?= $route->code ?></span>
                                    <span class="status__message"><?= $route->code_info ?></span>
                                </span>
                            </div>
                        <?php endif ?>
                    </td>
                </tr>
            </tbody>
        <?php endforeach ?>
    </table>
</div>