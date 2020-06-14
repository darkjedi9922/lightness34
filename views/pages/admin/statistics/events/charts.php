<?php
use frame\tools\JsonEncoder;

$props = JsonEncoder::forHtmlAttribute([
    'name' => 'Вызовы обработчиков событий',
    'stat' => 'events',
    'objectName' => 'Macro'
]);
?>

<div id="stat-charts" class="content__clear-bckg" data-props="<?= $props ?>"></div>