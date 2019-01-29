<?php  /** @var frame\views\Page $this */

use frame\views\Block;
use frame\views\Value;

$this->setLayout('page');
$this->setMetaArray(['name' => $this->file]);
$message = 'This is an action message to log in';
$block = new Block('block');
$answer = new Value('answer');

?>

Hello <?= $this->app->config->{'site.name'} ?><br>
<?= $block ?>
The answer is <?= $answer ?><br>