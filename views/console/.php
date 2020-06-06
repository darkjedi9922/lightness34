<?php /** @var frame\console\views\ConsolePage */

use frame\config\ConfigRouter;

$config = ConfigRouter::getDriver()->findConfig('core');
$sitename = $config->{'site.name'}; 
?>
Hello <?= $sitename . PHP_EOL ?>