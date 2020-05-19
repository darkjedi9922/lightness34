<?php /** @var frame\views\Layout $self */

use frame\config\ConfigRouter;
use function lightlib\versionify;

$config = ConfigRouter::getDriver()->findConfig('core');
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $config->{'site.name'} ?></title>
    <link rel="stylesheet" href="<?= versionify('public/styles/normalize.css') ?>">
    <link rel="stylesheet" href="<?= versionify('public/build/site.css') ?>">
</head>
<body>
    <?php $self->loadChild()->show() ?>
    <div class="footer">
        <div class="footer__column">
            <span class="footer__info">Created by Jed Sidious Alex Everdeen Dark</span>
            <span class="footer__info">2015 - 2020</span>
        </div>
        <div class="footer__column">
            <a class="footer__link" href="https://github.com/darkjedi9922/lightness34" target="__blank">
                <i class="icon-github footer__icon"></i>Repository
            </a>
        </div>
    </div>
</body>
</html>