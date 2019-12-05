<?php /** @var frame\views\Page $self */

use frame\tools\Init;
use engine\users\Group;
use frame\cash\config;
use frame\actions\ViewAction;
use engine\admin\actions\EditConfigAction;

Init::accessGroup(Group::ROOT_ID);

$config = config::get('messages');
$edit = new ViewAction(EditConfigAction::class, ['name' => 'messages']);

$self->setLayout('admin');
?>

<div class="box">
    <form action="<?= $edit->getUrl() ?>" method="post">
        <table>
            <tr>
                <td>Диалогов на странице списка:</td>
                <td>
                    <input name="dialogs->list->amount" type="text" 
                        value="<?= $edit->getPost(
                            'dialogs->list->amount', 
                            $config->{'dialogs.list.amount'}
                        ) ?>">
                </td>
            </tr>
            <tr>
                <td>Сообщений на странице диалога:</td>
                <td>
                    <input name="messages->list->amount" type="text" 
                        value="<?= $edit->getPost(
                            'messages->list->amount', 
                            $config->{'messages.list.amount'}
                        ) ?>">
                </td>
            </tr>
        </table>
        <button>Сохранить</button>
    </form>
</div>