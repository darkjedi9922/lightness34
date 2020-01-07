<?php /** @var frame\views\Page $self */

use frame\tools\Init;
use frame\tools\JsonEncoder;
use frame\lists\base\IdentityList;
use engine\statistics\stats\EventRouteStat;
use frame\database\Records;
use engine\statistics\stats\EventSubscriberStat;
use engine\statistics\stats\EventEmitStat;
use frame\lists\iterators\IdentityIterator;
use frame\actions\ViewAction;
use engine\statistics\actions\ClearStatistics;

Init::accessRight('admin', 'see-logs');

$clear = new ViewAction(ClearStatistics::class, ['module' => 'stat/events']);

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
        'handles' => [],
        'time' => date('d.m.Y H:i', $routeStat->time)
    ];

    $subscribers = Records::from(EventSubscriberStat::getTable(), [
        'route_id' => $routeStat->id
    ])->order(['id' => 'ASC'])->select();
    $subscribersIt = new IdentityIterator($subscribers, EventSubscriberStat::class);
    foreach ($subscribersIt as $subscriberStat) {
        /** @var EventSubscriberStat $subscriberStat */
        $subscriber = [
            'id' => $subscriberStat->id,
            'event' => $subscriberStat->event,
            'class' => $subscriberStat->class
        ];
        $route['subscribers'][] = $subscriber;
    }

    $emits = Records::from(EventEmitStat::getTable(), [
        'route_id' => $routeStat->id
    ])->order(['id' => 'ASC'])->select();
    $emitsIt = new IdentityIterator($emits, EventEmitStat::class);
    foreach ($emitsIt as $emitStat) {
        /** @var EventEmitStat $emitStat */
        $route['emits'][] = [
            'id' => $emitStat->id,
            'event' => $emitStat->event,
            'argsJson' => $emitStat->args_json
        ];
    }

    $handles = $routeStat->loadHandles();
    foreach ($handles as $handle) {
        $route['handles'][] = [
            'emitId' => $handle['emit_id'],
            'subscriberId' => $handle['subscriber_id'],
            'durationSec' => $handle['duration_sec']
        ];
    }

    $eventsProps['routes'][$routeStat->id] = $route;
}

$jsonEncoder = new JsonEncoder;
$eventsProps = $jsonEncoder->forHtmlAttribute($eventsProps);
?>

<div class="content__header">
    <div class="breadcrumbs">
        <span class="breadcrumbs__item">Мониторинг</span>
        <span class="breadcrumbs__divisor"></span>
        <span class="breadcrumbs__item breadcrumbs__item--current">События</span>
    </div>
    <a href="<?= $clear->getUrl() ?>" class="button">Очистить статистику</a>
</div>

<div id="events" data-props="<?= $eventsProps ?>"></div>