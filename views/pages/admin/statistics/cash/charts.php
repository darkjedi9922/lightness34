<?php
use frame\tools\JsonEncoder;

$props = JsonEncoder::forHtmlAttribute([
    'name' => 'Вызовы кэша',
    'stat' => 'cash',
]);
?>

<div id="stat-charts" class="content__clear-bckg" data-props="<?= $props ?>"></div>