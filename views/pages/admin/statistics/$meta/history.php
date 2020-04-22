<?php /** @var frame\views\DynamicPage $self */

use frame\tools\Init;
use engine\statistics\lists\history\HistoryList;

$statName = $self->getArgument(0);
$stat = ucfirst($statName);
$listClass = "\\engine\\statistics\\lists\history\\{$stat}HistoryList";
Init::require(is_subclass_of($listClass, HistoryList::class));
?>

<div id="stat-<?=$statName?>-history" class="content__clear-bckg"></div>