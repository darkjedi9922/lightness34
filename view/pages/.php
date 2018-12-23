<?php  /** @var frame\views\Page $this */

use frame\views\Block;
use engine\HelloAction;
use frame\views\Value;

$this->setLayout('page');
$this->setMetaArray(['name' => $this->file]);
$message = 'This is an action message to log in';
$hello = HelloAction::instance(['message' => $message]);
$block = new Block('block');
$answer = new Value('answer');

?>

Hello <?= $this->app->config->{'site.name'} ?><br>
<a href="<?=$hello->getUrl()?>">Go to the action</a><br>
<?= $block ?>
The answer is <?= $answer ?><br>