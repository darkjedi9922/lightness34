<?php /** @var frame\views\Layout $self */

use engine\admin\Auth;
use frame\tools\Init;
use frame\views\Page;

Init::accessRight('admin', 'enter');

$auth = new Auth;

// Если нет авторизации, показываем страницу `admin` если сейчас еще не она.
if (!$auth->isLogged() && !$self->hasChild('pages/admin')) {
    (new Page('admin'))->show();
    return;
}
?>

<?php $self->loadChild()->show() ?>