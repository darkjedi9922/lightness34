<?php /** @var frame\views\DynamicPage $self */

use frame\tools\JsonEncoder;
use frame\tools\Init;
use engine\statistics\lists\count\IntervalCountList;

$type = ucfirst($self->getMeta('$')[0]);
$class = "engine\\statistics\\lists\\count\\{$type}IntervalCountList";
Init::require(is_subclass_of($class, IntervalCountList::class));

$secInterval = IntervalCountList::DAY_INTERVAL;
$maxCount = 10; // Не должно быть <= 0 или слишком большим

$result = (new $class($secInterval, $maxCount))->assembleArray();
echo JsonEncoder::forViewText($result);