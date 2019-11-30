<?php /** @var frame\views\Page $self */

use frame\tools\Init;
use engine\users\Group;
use engine\users\cash\user_me;
use cash\config;
use engine\admin\actions\EditConfigAction;

$self->setLayout('admin');

$me = user_me::get();

Init::access((int) $me->group_id === Group::ROOT_ID);

$config = config::get('core');

$action = new EditConfigAction(['name' => 'core']);
?>

<div class="box">
    <form action="<?= $action->getUrl() ?>" method="post">
        <table>
            <tr>
                <td>Название сайта:</td>
                <td>
                    <input name="site->name" type="text"
                        value="<?= $action->getData('post', 'site->name', $config->{'site.name'}) ?>">
                    </td>
            </tr>
            <tr>
                <td colspan=2>
                    <div class="checkbox">
                        <input type="hidden" name="log->enabled" value="0">
                        <input type="checkbox" name="log->enabled" value="1" id="testing-chbx" <?php if ($config->{'log.enabled'} === true) echo 'checked' ?>>
                        <label for="testing-chbx"><i class="fontello icon-ok"></i></label>Включить логирование
                    </div>
                </td>
            </tr>
        </table>
        <button>Сохранить</button>
    </form>
</div>