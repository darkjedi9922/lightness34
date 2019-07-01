<?php  /** @var frame\views\Page $this */

use frame\views\Block;
use frame\views\Value;
use frame\tools\Client;
use engine\HelloAction;
use frame\actions\Action;

$this->setLayout('page');
$this->setMetaArray(['name' => $this->file]);
$message = 'This is an action message to log in';
$block = new Block('block');
$answer = new Value('answer');
$action = new HelloAction([Action::ID => 'the_id', 'answer' => $answer]);
?>

Hello <?= $this->app->config->{'site.name'} ?><br>
<?= $block ?>
The answer is <?= $answer ?><br>
Your client id: <?= Client::getId() ?><br>
<a href="<?= $action->getUrl() ?>">Action link</a><br>