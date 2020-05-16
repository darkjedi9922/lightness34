<?php /** @var frame\views\DynamicPage $self */

use frame\tools\Init;
use engine\statistics\lists\history\HistoryList;
use frame\tools\JsonEncoder;
use frame\stdlib\cash\pagenumber;

$stat = ucfirst($self->getArgument(0));
$sortField = Init::requireGet('sort');
$sortOrder = Init::requireGet('order');
$pagenumber = pagenumber::get();

$listClass = "\\engine\\statistics\\lists\history\\{$stat}HistoryList";
Init::require(is_subclass_of($listClass, HistoryList::class));
/** @var HistoryList $list */
$list = new $listClass($pagenumber, $sortField, $sortOrder);
echo JsonEncoder::forViewText($list->toArray());