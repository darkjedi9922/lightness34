<?php /** @var frame\views\Page $self */

use frame\actions\ActionBody;
use engine\statistics\lists\ActionList;

$actions = new ActionList;
?>

<div class="content__header">
    <div class="breadcrumbs">
        <span class="breadcrumbs__item">Мониторинг</span>
        <span class="breadcrumbs__divisor"></span>
        <span class="breadcrumbs__item">Действия</span>
        <span class="breadcrumbs__divisor"></span>
        <span class="breadcrumbs__item breadcrumbs__item--current">Каталог</span>
    </div>
</div>

<div class="box box--table">
    <table class="table action-list">
        <tr class="table__headers">
            <td class="table__header">Class</td>
            <td class="table__header">Module</td>
        </tr>
        <?php foreach ($actions as $class) :
            /** @var string $class */
            $module = explode('\\', $class)[2];
            // $color = ord($module[0]) % 5 + 1;
            /** @var ActionBody $action */
            $action = new $class;
            $parameters = [
                'GET' => $action->listGet(),
                'POST' => $action->listPost()
            ]
            ?>
            <tbody class="table__item-wrapper">
                <tr class="table__item">
                    <td class="table__cell"><?= ltrim($class, '\\') ?></td>
                    <td class="table__cell">
                        <span class="actions__module"><?= $module ?></span>
                    </td>
                </tr>
                <tr class="table__details-wrapper">
                    <td class="table__details table__details--indent" colspan="100">
                        <?php foreach ($parameters as $type => $list) : ?>
                            <?php if (!empty($list)) : ?>
                                <div class="details">
                                    <span class="details__header"><?= $type ?> Parameters</span>
                                    <?php foreach ($list as $name => $fieldType) : ?>
                                        <div class="param action-param">
                                            <span class="param__name"><?= $name ?></span>
                                            <span class="param__value action-param__type action-param__type--<?= $fieldType ?>"><?= $fieldType ?></span>
                                        </div>
                                    <?php endforeach ?>
                                </div>
                            <?php endif ?>
                        <?php endforeach ?>
                    </td>
                </tr>
            </tbody>
        <?php endforeach ?>
    </table>
</div>