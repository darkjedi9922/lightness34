<?php /** @var frame\views\Page $self */

use frame\tools\Init;
use frame\lists\base\IdentityList;
use engine\statistics\stats\EventRouteStat;
use frame\database\Records;
use engine\statistics\stats\EventSubscriberStat;
use engine\statistics\stats\EventEmitStat;
use frame\lists\iterators\IdentityIterator;

Init::accessRight('admin', 'see-logs');

$eventsProps = [
    'routes' => []
];

$routes = new IdentityList(EventRouteStat::class, ['id' => 'DESC']);
foreach ($routes as $routeStat) {
    /** @var EventRouteStat $routeStat */
    $route = [
        'route' => $routeStat->route,
        'subscribers' => [],
        'emits' => [],
        'handles' => []
    ];

    $subscribers = Records::select(EventSubscriberStat::getTable(), [
        'route_id' => $routeStat->id
    ])->load();
    $subscribersIt = new IdentityIterator($subscribers, EventSubscriberStat::class);
    foreach ($subscribersIt as $subscriberStat) {
        /** @var EventSubscriberStat $subscriberStat */
        $subscriber = [];
        $route['subscribers'][$subscriberStat->id] = $subscriber;
    }

    $emits = Records::select(EventEmitStat::getTable(), [
        'route_id' => $routeStat->id
    ])->load();
    $emitsIt = new IdentityIterator($emits, EventEmitStat::class);
    foreach ($emitsIt as $emitStat) {
        /** @var EventEmitStat $emitStat */
        $emit = [];
        $route['emits'][$emitStat->id] = $emit;
    }

    $handles = $routeStat->loadHandles();
    foreach ($handles as $handle) {
        $route['handles'][] = [
            'emitId' => $handle['emit_id'],
            'subscriberId' => $handle['subscriber_id']
        ];
    }

    $eventsProps['routes'][$routeStat->id] = $route;
}
?>

<div class="breadcrumbs">
    <span class="breadcrumbs__item">Мониторинг</span>
    <span class="breadcrumbs__divisor"></span>
    <span class="breadcrumbs__item breadcrumbs__item--current">События</span>
</div>

<div id="events" data-props='<?= json_encode($eventsProps, JSON_HEX_AMP) ?>'></div>