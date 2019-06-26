<?php /** @var frame\views\Page $this */

use frame\views\Block;
use frame\views\Value;
use frame\route\Router;

$this->setLayout('page');
$this->setMetaArray(['name' => $this->file]);
$message = 'This is an action message to log in';
$block = new Block('block');
$answer = new Value('answer');

/** @var int $cid */
$cid = $this->global('client-id');
/** @var Router $prevRouter */
$prevRouter = $this->global('prev-router');
/** @var int $pagenumber */
$pagenumber = $this->global('pagenumber');

?>

Hello <?= $this->app->config->{'site.name'} ?><br>
<?= $block ?>
The answer is <?= $answer ?><br>
Your client id: <?= $cid ?><br>
<a href="<?=$this->app->router->toUrl()?>">Click here</a><br>
<?php if ($prevRouter): ?><?= $prevRouter->toUrl() ?><br><?php endif?>
Pagenumber: <?= $pagenumber ?><br>