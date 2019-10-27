<?php /** @var frame\views\Layout $self */

use cash\config_core;
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

$config = config_core::get();
?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html" charset="utf-8">
    <link rel="icon" href="<?= versionify('public/favicon.ico') ?>" type="image/x-icon">
    <link rel="stylesheet" href="<?= versionify('public/styles/admin.css') ?>" type="text/css">
    <title><?= $config->{'site.name'} ?></title>
</head>
<body>
    <?php $self->showChild() ?>
</body>
</html>