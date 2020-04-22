<?php
use frame\tools\JsonEncoder;

$props = JsonEncoder::forHtmlAttribute([
    'name' => 'Вызовы обработчиков событий',
    'stat' => 'events'
]);
?>

<div id="stat-charts" class="content__clear-bckg" data-props="<?= $props ?>"></div>