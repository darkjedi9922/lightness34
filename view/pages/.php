<?php 

/** @var frame\views\Page $this */ 

$this->setLayout('page');

?>

Hello <?= $this->app->config->{'site.name'} ?><br>
<?php $this->includeBlock('block') ?>