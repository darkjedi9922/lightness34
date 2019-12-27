<?php /** @var frame\views\Page $self */

use frame\tools\Init;
use frame\lists\base\IdentityList;
use engine\statistics\stats\EventRouteStat;
use frame\database\Records;
use engine\statistics\stats\EventSubscriberStat;
use engine\statistics\stats\EventEmitStat;

Init::accessRight('admin', 'see-logs');

$routes = new IdentityList(EventRouteStat::class, ['id' => 'DESC']);
?>

<div class="breadcrumbs">
    <span class="breadcrumbs__item">Мониторинг</span>
    <span class="breadcrumbs__divisor"></span>
    <span class="breadcrumbs__item breadcrumbs__item--current">События</span>
</div>
<div class="box box--table">
    <table class="table routes">
        <tr class="table__headers">
            <td class="table__header">Path</td>
            <td class="table__header">Subscribers</td>
            <td class="table__header">Emits</td>
            <td class="table__header">Handles</td>
        </tr>
        <?php foreach ($routes as $route) :
            /** @var EventRouteStat $route */
            $subscriberCount = Records::select(EventSubscriberStat::getTable(), [
                'route_id' => $route->id
            ])->count('id');
            $emitCount = Records::select(EventEmitStat::getTable(), [
                'route_id' => $route->id
            ])->count('id');
            $request = $route->route;
            $handles = $route->loadHandles();
            ?>
            <tbody class="table__item-wrapper">
                <tr class="table__item">
                    <td class="table__cell">
                        <span class="routes__pagename <?= !$request ? 'routes__pagename--index' : '' ?>">
                            <?= $request ? $request : 'index request' ?>
                        </span>
                    </td>
                    <td class="table__cell"><?= $subscriberCount ?></td>
                    <td class="table__cell"><?= $emitCount ?></td>
                    <td class="table__cell"><?= count($handles) ?></td>
                </tr>
            </tbody>
        <?php endforeach ?>
    </table>
</div>