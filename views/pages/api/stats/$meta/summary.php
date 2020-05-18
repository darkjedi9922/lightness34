<?php /** @var frame\views\DynamicPage $self */

use frame\tools\JsonEncoder;
use frame\route\InitRoute;
use engine\statistics\lists\summary\IntervalSummaryCountList;

$type = ucfirst($self->getArgument(0));
$class = "engine\\statistics\\lists\\summary\\{$type}IntervalSummaryCountList";
InitRoute::require(is_subclass_of($class, IntervalSummaryCountList::class));

$secInterval = IntervalSummaryCountList::DAY_INTERVAL;
$maxCount = 10; // Не должно быть <= 0 или слишком большим

$result = (new $class($secInterval, $maxCount))->assembleArray();
echo JsonEncoder::forViewText($result);