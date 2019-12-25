<?php /** @var frame\views\Page $self */

use frame\tools\Init;
use frame\lists\base\IdentityList;
use engine\statistics\stats\EventRouteStat;
use frame\database\Records;
use engine\statistics\stats\EventSubscriberStat;

Init::accessRight('admin', 'see-logs');

$routes = new IdentityList(EventRouteStat::class, ['id' => 'DESC']);

$self->setLayout('admin');
?>

<div class="breadcrumbs">
    <span class="breadcrumbs__item">Мониторинг</span>
    <span class="breadcrumbs__divisor"></span>
    <span class="breadcrumbs__item breadcrumbs__item--current">События</span>
</div>
<div class="box box--table">
    <table class="table">
        <tr class="table__headers">
            <td class="table__header">Маршрут</td>
            <td class="table__header">Установлено</td>
        </tr>
        <?php foreach ($routes as $route) :
            /** @var EventRouteStat $route */
            $subscriberCount = Records::select(EventSubscriberStat::getTable(), [
                'route_id' => $route->id
            ])->count('id');
            ?>
            <tbody class="table__item-wrapper">
                <tr class="table__item">
                    <td class="table__cell"><?= $route->route ?></td>
                    <td class="table__cell"><?= $subscriberCount ?></td>
                </tr>
            </tbody>
        <?php endforeach ?>
    </table>
</div>