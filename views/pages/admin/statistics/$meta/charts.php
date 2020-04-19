<?php /** @var frame\views\DynamicPage $self */

use engine\statistics\lists\MultipleIntervalDataList;
use engine\statistics\lists\summary\IntervalSummaryCountList;
use frame\tools\Init;

$stat = ucfirst($self->getArgument(0));
$summaryClass = "engine\\statistics\\lists\\summary\\{$stat}IntervalSummaryCountList";
Init::require(is_subclass_of($summaryClass, IntervalSummaryCountList::class));
$countClass = "engine\\statistics\\lists\\count\\Multiple{$stat}IntervalCountList";
Init::require(is_subclass_of($countClass, MultipleIntervalDataList::class));
$timeClass = "engine\\statistics\\lists\\duration\\Multiple{$stat}IntervalTimeList";
Init::require(is_subclass_of($timeClass, MultipleIntervalDataList::class));
?>

<div id="<?= $self->getArgument(0) ?>-charts" class="content__clear-bckg"></div>