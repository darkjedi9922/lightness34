<?php  /** @var frame\views\Page $this */

use engine\HelloAction;

$this->setLayout('page');
$message = 'This is an action message to log in';
$hello = HelloAction::instance(['message' => $message]);

?>

Hello <?= $this->app->config->{'site.name'} ?><br>
<a href="<?=$hello->getUrl()?>">Go to the action</a><br>
<?php $this->includeBlock('block') ?>