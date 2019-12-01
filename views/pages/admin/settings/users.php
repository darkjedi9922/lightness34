<?php

/** @var frame\views\Page $self */

use frame\tools\Init;
use engine\users\Group;
use engine\users\cash\user_me;
use cash\config;
use engine\admin\actions\EditConfigAction;

$self->setLayout('admin');

$me = user_me::get();

Init::access((int) $me->group_id === Group::ROOT_ID);

$config = config::get('users');
$action = new EditConfigAction(['name' => 'users']);
?>

<div class="box">
    <form action="<?= $action->getUrl() ?>" method="post">
        <table>
            <tr>
                <td>Максимальная длина логина:</td>
                <td><input name="login->max_length" type="text" 
                    value="<?= $action->getData('post', 'login->max_length', 
                        $config->get('login.max_length')) ?>"></td>
            </tr>
            <tr>
                <td>Максимальная длина пароля:</td>
                <td><input name="password->max_length" type="text" 
                    value="<?= $action->getData('post', 'password->max_length',
                        $config->get('password.max_length')) ?>"></td>
            </tr>
            <tr>
                <td>Максимальный размер аватара:</td>
                <td>
                    <input name="avatar->max_size->value" type="text" 
                        value="<?= $action->getData('post', 'avatar->max_size->value', 
                            $config->get('avatar.max_size.value')) ?>">
                    <div class="radio">
                        <input id='KB' type='radio' name='avatar->max_size->unit' value='KB'
                            <?php if ($action->getData('post', 'avatar->max_size->unit', 
                                $config->get('avatar.max_size.unit')) === 'KB') echo 'checked' ?>>
                        <label for="KB">KB</label>
                        <input id='MB' type='radio' name='avatar->max_size->unit' value='MB'
                            <?php if ($action->getData('post', 'avatar->max_size->unit', 
                                $config->get('avatar.max_size.unit')) === 'MB') echo 'checked' ?>>
                        <label for="MB">MB</label>
                    </div>
                </td>
            </tr>
            <tr>
                <td>Количество на странице списка:</td>
                <td>
                    <input name="list->amount" type="text" 
                        value="<?= $action->getData('post', 'list->amount', 
                            $config->get('list.amount')) ?>">
                </td>
            </tr>
        </table>
        <button>Сохранить</button>
    </form>
</div>