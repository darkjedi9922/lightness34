<?php /** @var frame\views\Layout $self */

use frame\stdlib\cash\config;
use engine\admin\Auth;
use frame\tools\Init;
use frame\views\Page;
use function lightlib\versionify;

Init::accessRight('admin', 'enter');

$auth = new Auth;

// Если нет авторизации, показываем страницу `admin` если сейчас еще не она.
if (!$auth->isLogged() && !$self->hasChild('pages/admin')) {
    (new Page('admin'))->show();
    return;
}

$config = config::get('core');
?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html" charset="utf-8">
    <link rel="icon" href="<?= versionify('public/favicon.ico') ?>" type="image/x-icon">
    <link rel="stylesheet" href="<?= versionify('public/build/admin.css') ?>" type="text/css">
    <title><?= $config->{'site.name'} ?></title>
</head>
<body>
    <?php $self->loadChild()->show() ?>
    <script src="<?= versionify('public/build/admin.js') ?>"></script>
</body>
</html>