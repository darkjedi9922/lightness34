<?php
use frame\tools\JsonEncoder;

$props = JsonEncoder::forHtmlAttribute([
    'name' => 'Действия',
    'stat' => 'actions',
    'objectName' => 'Action'
]);
?>

<div id="stat-charts" class="content__clear-bckg" data-props="<?= $props ?>"></div>