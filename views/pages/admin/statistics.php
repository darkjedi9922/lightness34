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
    <table class="routes">
        <tr class="routes__headers">
            <td class="routes__header">Path</td>
            <td class="routes__header">Code</td>
            <td class="routes__header">Time</td>
        </tr>
        <?php foreach ($routes as $route) : /** @var RouteStat $route */ ?>
            <?php 
                $router = new Router($route->url);
                $severity = 'warning';
                switch ((int) $route->code / 100) {
                    case 2: $severity = 'ok'; break;
                    case 5: $severity = 'error'; break;
                }
            ?>
            <tr class="routes__mainrow">
                <td class="routes__path">
                    <span class="routes__pagename"><?= $router->pagename ?></span>
                    <?php if ($route->ajax): ?>
                        <span class="routes__mark routes__mark--ajax">ajax</span>
                    <?php endif  ?>
                    <?php if ($route->type === $route::ROUTE_TYPE_ACTION): ?>
                        <span class="routes__mark routes__mark--action">action</span>
                    <?php endif ?>
                </td>
                <td class="routes__code routes__code--<?= $severity ?>"><?= $route->code ?></td>
                <td class="routes__time"><?= date('d.m.Y H:i', $route->time) ?></td>
            </tr>
            <tr class="routes__subrow">
                <td colspan="100" class="route__details">

                </td>
            </tr>
        <?php endforeach ?>
    </table>
</div>