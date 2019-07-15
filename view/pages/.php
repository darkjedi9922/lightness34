<?php /** @var frame\views\Page $this */

use frame\views\Block;
use frame\views\Value;
use globals\client_id;
use globals\pagenumber;
use globals\prev_router;

$this->setLayout('page');
$this->setMetaArray(['name' => $this->file]);
$message = 'This is an action message to log in';
$block = new Block('block');
$answer = new Value('answer');

$cid = client_id::get();
$prevRouter = prev_router::get();
$pagenumber = pagenumber::get();
?>

Hello <?= $this->app->config->{'site.name'} ?><br>
<?= $block ?>
The answer is <?= $answer ?><br>
Your client id: <?= $cid ?><br>
<a href="<?=$this->app->router->toUrl()?>">Click here</a><br>
<?php if ($prevRouter): ?><?= $prevRouter->toUrl() ?><br><?php endif?>
Pagenumber: <?= $pagenumber ?><br>