<?php /** @var frame\views\Page $self */

use frame\tools\Init;
use engine\users\Group;
use frame\cash\config;
use frame\actions\ViewAction;
use engine\admin\actions\EditConfigAction;

Init::accessGroup(Group::ROOT_ID);

$config = config::get('admin');
$edit = new ViewAction(EditConfigAction::class, ['name' => 'admin']);
?>

<div class="box">
    <form action="<?= $edit->getUrl() ?>" method="post">
        <table>
            <tr>
                <td>Пароль:</td>
                <td>
                    <input name="password" type="password" 
                        value="<?= $edit->getPost('password', $config->{'password'}) ?>">
                </td>
            </tr>
        </table>
        <button>Сохранить</button>
    </form>
</div>