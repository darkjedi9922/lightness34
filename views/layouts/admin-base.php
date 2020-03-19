<?php /** @var frame\views\Layout $self */

use frame\stdlib\cash\config;
use engine\admin\Auth;
use frame\tools\Init;
use frame\views\Page;
use function lightlib\versionify;

Init::accessRight('admin', 'enter');

$auth = new Auth;

// Если нет авторзации и при этом не находимся на странице `admin` (там задаем флаг).
if (!$auth->isLogged() && !$self->getChildMeta('admin-login-page-flag')) {
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
    <?php $self->showChild() ?>
    <script src="<?= versionify('public/build/admin.js') ?>"></script>
</body>
</html>