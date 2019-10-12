<?php /** @var frame\views\Layout $this */ ?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $this->app->config->{'site.name'} ?></title>
    <link rel="stylesheet" href="/public/styles/normalize.css">
    <link rel="stylesheet" href="/public/styles/site.css">
</head>
<body>
    <?= $this->child->getContent() ?>
    <div class="footer">
        <span class="footer__info">Created by Jed Sidious Alex Everdeen Dark</span>
        <span class="footer__info">2015 - 2018</span>
    </div>
</body>
</html>