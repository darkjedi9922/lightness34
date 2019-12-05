<?php /** @var frame\views\Page $self */

use frame\tools\Init;
use engine\users\Group;
use frame\cash\config;
use frame\actions\ViewAction;
use engine\admin\actions\EditConfigAction;

Init::accessGroup(Group::ROOT_ID);

$config = config::get('articles');
$edit = new ViewAction(EditConfigAction::class, ['name' => 'articles']);

$self->setLayout('admin');
?>

<div class="box">
    <form action="<?= $edit->getUrl() ?>" method="post">
        <table>
            <tr>
                <td>Максимальная длина названия:</td>
                <td>
                    <input name="title->maxLength" type="text" 
                        value="<?= $edit->getPost(
                            'title->maxLength', 
                            $config->{'title.maxLength'}
                        ) ?>">
                </td>
            </tr>
            <tr>
                <td>Количество на странице списка:</td>
                <td>
                    <input name="list->amount" type="text" 
                        value="<?= $edit->getPost(
                            'list->amount', 
                            $config->{'list.amount'}
                        ) ?>">
                </td>
            </tr>
        </table>
        <button>Сохранить</button>
    </form>
</div>