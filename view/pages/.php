<?php 

/** @var frame\views\Page $this */ 

use frame\Core;

$this->setLayout('page');

?>

Hello <?= Core::$app->config->{'site.name'} ?><br>
<?php $this->includeBlock('block') ?>