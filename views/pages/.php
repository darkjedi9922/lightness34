<?php /** @var frame\views\Page $this */

use frame\views\Block;
use frame\views\Value;
use frame\tools\Client;
use engine\HelloAction;
use frame\actions\Action;
use frame\rules\RouteRules;

(new RouteRules($this->app->router, [
    'login' => [
        'rules' => [
            // Параметр login может быть не задан.
            'base/mandatory' => false,
            // Но если он задан, он не должен быть пустым.
            'base/emptiness' => false
        ]
    ]
// Если правила не выполняются, возникнет ошибка 404.
]))->assert();

$this->setLayout('page');
$this->setMetaArray(['name' => $this->file]);
$message = 'This is an action message to log in';
$block = new Block('block');
$answer = new Value('answer');
$action = new HelloAction([Action::ID => 'the_id', 'answer' => $answer]);

$config = cash\config_core::get();
$prevRouter = cash\prev_router::get();
$pagenumber = cash\pagenumber::get();
?>

Hello <?= $config->{'site.name'} ?><br>
<?= $block ?>
The answer is <?= $answer ?><br>
Your client id: <?= Client::getId() ?><br>
<a href="<?= $action->getUrl() ?>">Action link</a><br>
<?php if ($prevRouter): ?><?= $prevRouter->toUrl() ?><br><?php endif?>
Pagenumber: <?= $pagenumber ?><br>