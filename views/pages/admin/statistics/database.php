<?php /** @var frame\views\Page $self */

use frame\tools\Init;
use frame\lists\base\IdentityList;
use engine\statistics\stats\QueryRouteStat;
use frame\lists\iterators\IdentityIterator;
use frame\database\Records;
use engine\statistics\stats\QueryStat;
use frame\actions\ViewAction;
use engine\statistics\actions\ClearStatistics;
use frame\tools\JsonEncoder;
use frame\cash\database;

Init::accessRight('admin', 'see-logs');

$clear = new ViewAction(ClearStatistics::class, ['module' => 'stat/db']);

$queryHistoryProps = ['routes' => []];
$queryRoutes = new IdentityList(QueryRouteStat::class, ['id' => 'DESC']);
foreach ($queryRoutes as $routeStat) {
    /** @var QueryRouteStat $routeStat */
    $route = [
        'route' => $routeStat->route,
        'queries' => [],
        'time' => date('d.m.Y H:i', $routeStat->time)
    ];

    $routeQueries = new IdentityIterator(
        Records::from(QueryStat::getTable(), ['route_id' => $routeStat->id])
            ->order(['id' => 'ASC'])
            ->select(),
        QueryStat::class
    );
    foreach ($routeQueries as $queryStat) {
        /** @var QueryStat $queryStat */
        $route['queries'][] = [
            'sql' => $queryStat->sql_text,
            'error' => $queryStat->error,
            'durationSec' => $queryStat->duration_sec
        ];
    }

    $queryHistoryProps['routes'][] = $route;
}
$queryHistoryCount = count($queryHistoryProps['routes']);
$queryHistoryProps = JsonEncoder::forHtmlAttribute($queryHistoryProps);

$tablesProps = ['tables' => []];
$tables = database::get()->query("SHOW TABLES")->readColumn(0);
foreach ($tables as $table) {
    $tableProps = [
        'name' => $table,
        'fields' => []
    ];
    $fields = database::get()->query("DESCRIBE `$table`")->readAll();
    foreach ($fields as $field) {
        $tableProps['fields'][] = [
            'name' => $field['Field'],
            'type' => $field['Type'],
            'null' => $field['Null'] !== 'NO',
            'primary' => $field['Key'] === 'PRI',
            'default' => $field['Default']
        ];
    }
    $tableProps['rowCount'] = database::get()
        ->query("SELECT COUNT(*) FROM `$table`")
        ->readScalar();
    $tablesProps['tables'][] = $tableProps;
}
$tablesCount = count($tablesProps['tables']);
$tablesProps = JsonEncoder::forHtmlAttribute($tablesProps);
?>

<div class="content__header">
    <div class="breadcrumbs">
        <span class="breadcrumbs__item">Мониторинг</span>
        <span class="breadcrumbs__divisor"></span>
        <span class="breadcrumbs__item breadcrumbs__item--current">База данных</span>
    </div>
    <a href="<?= $clear->getUrl() ?>" class="button">Очистить статистику</a>
</div>

<span class="content__title">История запросов (<?= $queryHistoryCount ?>)</span>
<div id="query-history" data-props="<?= $queryHistoryProps ?>"></div>

<span class="content__title">Таблицы (<?= $tablesCount ?>)</span>
<div id="db-tables" data-props="<?= $tablesProps ?>"></div>