<?php /** @var frame\views\Page $self */

use frame\tools\Init;
use engine\users\Group;
use frame\cash\config;
use frame\actions\ViewAction;
use engine\admin\actions\EditConfigAction;

Init::accessGroup(Group::ROOT_ID);

$config = config::get('comments');
$edit = new ViewAction(EditConfigAction::class, ['name' => 'comments']);

$self->setLayout('admin');
?>

<div class="box">
    <form action="<?= $edit->getUrl() ?>" method="post">
        <table>
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
            <tr>
                <td>Порядок:</td>
                <td>
                    <div class="radio">
                        <input id="ASC" type="radio" name="list->order" value="ASC" 
                            <?php if ($edit->getPost('list->order', $config->{'list.order'} === 'ASC') )
                                echo 'checked' ?>>
                        <label for="ASC">Сначала старые</label>
                        <input id="DESC" type="radio" name="list->order" value="DESC" 
                            <?php if ($edit->getPost('list->order', $config->{'list.order'} === 'DESC'))
                                echo 'checked' ?>>
                        <label for="DESC">Сначала новые</label>
                    </div>
                </td>
            </tr>
        </table>
        <button>Сохранить</button>
    </form>
</div>