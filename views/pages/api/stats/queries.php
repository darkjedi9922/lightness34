<?php /** @var frame\views\Page $self */

use frame\tools\JsonEncoder;
use engine\statistics\lists\QueryIntervalCountList;

$secInterval = 60*60; // 1 hour
$maxCount = 10; // Не должно быть <= 0 или слишком большим

$result = [
    'data' => (new QueryIntervalCountList($secInterval, $maxCount))->assembleArray()
];
echo JsonEncoder::forViewText($result);