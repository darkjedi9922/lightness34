<?php
use frame\tools\JsonEncoder;

$props = JsonEncoder::forHtmlAttribute([
    'name' => 'Загрузка кэша',
    'stat' => 'cash',
    'objectName' => 'Object'
]);
?>

<div id="stat-charts" class="content__clear-bckg" data-props="<?= $props ?>"></div>