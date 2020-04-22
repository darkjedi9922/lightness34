<?php
use frame\tools\JsonEncoder;

$props = JsonEncoder::forHtmlAttribute([
    'name' => 'Маршруты',
    'stat' => 'routes',
]);
?>

<div id="stat-charts" class="content__clear-bckg" data-props="<?= $props ?>"></div>