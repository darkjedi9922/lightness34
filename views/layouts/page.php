<?php 

/** @var frame\views\Layout $this */ 

?>

Hello Page Layout for <?= $this->child->getMeta('name') ?><br>

<?= $this->child->content ?>

Bye Page Layout<br>