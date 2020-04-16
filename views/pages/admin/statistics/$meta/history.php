<?php /** @var frame\views\DynamicPage $self */

use frame\tools\Init;
use frame\views\Page;

$statName = $self->getMeta('$')[0];
Init::require(Page::find("api/stats/$statName/history") !== null);
?>

<div id="stat-<?=$statName?>-history" class="content__clear-bckg"></div>