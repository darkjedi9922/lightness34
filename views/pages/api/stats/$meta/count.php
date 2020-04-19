<?php /** @var frame\views\DynamicPage $self */

use engine\statistics\tools\MultipleChartAPI;
use engine\statistics\lists\MultipleIntervalDataList;
use frame\tools\Init;

$stat = ucfirst($self->getArgument(0));
$class = "\\engine\\statistics\\lists\\count\\Multiple{$stat}IntervalCountList";
Init::require(is_subclass_of($class, MultipleIntervalDataList::class));

(new MultipleChartAPI($class))->jsonResult();