<?php /** @var frame\views\DynamicPage $self */

use frame\tools\JsonEncoder;
use frame\tools\Init;
use engine\statistics\lists\IntervalCountList;

Init::require($self->hasArgument(0));
$type = ucfirst($self->getArgument(0));
$class = "engine\\statistics\\lists\\{$type}IntervalCountList";
Init::require(is_subclass_of($class, IntervalCountList::class));

$secInterval = IntervalCountList::DAY_INTERVAL;
$maxCount = 10; // Не должно быть <= 0 или слишком большим

$result = (new $class($secInterval, $maxCount))->assembleArray();
echo JsonEncoder::forViewText($result);