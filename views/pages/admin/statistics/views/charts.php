<?php
use frame\tools\JsonEncoder;

$props = JsonEncoder::forHtmlAttribute([
    'name' => 'Представления',
    'stat' => 'views',
]);
?>

<div id="stat-charts" class="content__clear-bckg" data-props="<?= $props ?>"></div>