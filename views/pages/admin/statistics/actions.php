<?php /** @var frame\views\Page $self */

use frame\tools\Init;
use frame\actions\ActionBody;
use engine\statistics\lists\ActionList;

Init::accessRight('admin', 'see-logs');

$actions = new ActionList;

$self->setLayout('admin');
?>

<div class="breadcrumbs">
    <span class="breadcrumbs__item">Статистика</span>
    <span class="breadcrumbs__divisor"></span>
    <span class="breadcrumbs__item breadcrumbs__item--current">Действия</span>
</div>
<div class="box box--table">
    <table class="table actions">
        <tr class="table__headers">
            <td class="table__header">Class</td>
            <td class="table__header">Module</td>
        </tr>
        <?php foreach ($actions as $class) :
            /** @var string $class */
            $module = explode('\\', $class)[2];
            $color = ord($module[0]) % 5 + 1;
            /** @var ActionBody $action */
            $action = new $class;
            ?>
            <tbody class="table__item-wrapper">
                <tr class="table__item">
                    <td class="table__cell"><?= $class ?></td>
                    <td class="table__cell">
                        <span class="actions__module actions__module--<?= $color ?>"><?= $module ?></span>
                    </td>
                </tr>
                <tr class="table__item-details-wrapper">
                    <td class="table__item-details" colspan="100">
                        <?php if (!empty($action->listGet())) : ?>
                            <span class="table__subheader">GET Parameters</span>
                            <div class="table__item-detail">
                                <?php foreach ($action->listGet() as $name => $desc) : ?>
                                    <span class="actions__param-name"><?= $name ?></span>
                                    <span class="actions__param-type"><?= $desc[0] ?></span>
                                    <span class="actions__param-desc"><?= $desc[1] ?></span>
                                <?php endforeach ?>
                            </div>
                        <?php endif ?>
                        <?php if (!empty($action->listPost())) : ?>
                            <span class="table__subheader">POST Parameters</span>
                            <div class="table__item-detail">
                                <?php foreach ($action->listPost() as $name => $desc) : ?>
                                    <span class="actions__param-name"><?= $name ?></span>
                                    <span class="actions__param-type"><?= $desc[0] ?></span>
                                    <span class="actions__param-desc"><?= $desc[1] ?></span>
                                <?php endforeach ?>
                            </div>
                        <?php endif ?>
                    </td>
                </tr>
            </tbody>
        <?php endforeach ?>
    </table>
</div>