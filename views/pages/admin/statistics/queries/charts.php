<?php
use frame\tools\JsonEncoder;

$props = JsonEncoder::forHtmlAttribute([
    'name' => 'Статистика',
    'stat' => 'queries',
]);
?>

<div id="stat-charts" class="content__clear-bckg" data-props="<?= $props ?>"></div>