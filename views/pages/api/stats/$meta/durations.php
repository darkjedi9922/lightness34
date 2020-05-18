<?php /** @var frame\views\DynamicPage $self */

use engine\statistics\tools\MultipleChartAPI;
use engine\statistics\lists\MultipleIntervalDataList;
use frame\route\InitRoute;

$stat = ucfirst($self->getArgument(0));
$class = "\\engine\\statistics\\lists\\duration\\Multiple{$stat}IntervalTimeList";
InitRoute::require(is_subclass_of($class, MultipleIntervalDataList::class));

(new MultipleChartAPI($class))->jsonResult();