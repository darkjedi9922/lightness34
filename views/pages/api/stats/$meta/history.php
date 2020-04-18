<?php /** @var frame\views\DynamicPage $self */

use frame\tools\Init;
use engine\statistics\lists\history\HistoryList;
use frame\tools\JsonEncoder;

$stat = ucfirst($self->getArgument(0));
$listClass = "\\engine\\statistics\\lists\history\\{$stat}HistoryList";
Init::require(is_subclass_of($listClass, HistoryList::class));
/** @var HistoryList $list */
$list = new $listClass;
echo JsonEncoder::forViewText($list->toArray());