<?php /** @var frame\views\DynamicPage $self */

use frame\route\InitRoute;
use engine\statistics\lists\history\HistoryList;
use frame\tools\JsonEncoder;
use frame\lists\paged\PagerModel;

$stat = ucfirst($self->getArgument(0));
$sortField = InitRoute::requireGet('sort');
$sortOrder = InitRoute::requireGet('order');
$pagenumber = PagerModel::getRoutePage();

$listClass = "\\engine\\statistics\\lists\history\\{$stat}HistoryList";
InitRoute::require(is_subclass_of($listClass, HistoryList::class));
/** @var HistoryList $list */
$list = new $listClass($pagenumber, $sortField, $sortOrder);
echo JsonEncoder::forViewText($list->toArray());