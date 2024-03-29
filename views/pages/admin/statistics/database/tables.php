<?php /** @var frame\views\Page $self */

use frame\tools\JsonEncoder;
use frame\database\SqlDriver;

$tablesProps = ['tables' => []];
$tables = SqlDriver::getDriver()->query("SHOW TABLES")->readColumn(0);
foreach ($tables as $table) {
    $tableProps = [
        'name' => $table,
        'fields' => []
    ];
    $fields = SqlDriver::getDriver()->query("DESCRIBE `$table`")->readAll();
    foreach ($fields as $field) {
        $tableProps['fields'][] = [
            'name' => $field['Field'],
            'type' => $field['Type'],
            'null' => $field['Null'] !== 'NO',
            'primary' => $field['Key'] === 'PRI',
            'default' => $field['Default']
        ];
    }
    $tableProps['rowCount'] = SqlDriver::getDriver()
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
        <span class="breadcrumbs__item">База данных</span>
        <span class="breadcrumbs__divisor"></span>
        <span class="breadcrumbs__item breadcrumbs__item--current">
            Таблицы (<?= $tablesCount ?>)
        </span>
    </div>
</div>
<div id="db-tables" data-props="<?= $tablesProps ?>"></div>