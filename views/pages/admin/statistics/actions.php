<?php /** @var frame\views\Page $self */

use frame\tools\Init;
use frame\actions\ActionBody;
use frame\lists\base\IdentityList;
use engine\statistics\lists\ActionList;
use engine\statistics\stats\ActionStat;

Init::accessRight('admin', 'see-logs');

$actions = new ActionList;
$history = new IdentityList(ActionStat::class, ['id' => 'DESC']);

$self->setLayout('admin');
?>

<div class="breadcrumbs">
    <span class="breadcrumbs__item">Мониторинг</span>
    <span class="breadcrumbs__divisor"></span>
    <span class="breadcrumbs__item breadcrumbs__item--current">Действия</span>
</div>

<span class="content__title">История запусков</span>
<div class="box box--table">
    <table class="table routes">
        <tr class="table__headers">
            <td class="table__header">Class</td>
            <td class="table__header">Duration</td>
            <td class="table__header">Status</td>
            <td class="table__header">Time</td>
        </tr>
        <?php foreach ($history as $action) :
            /** @var ActionStat $action */
            $data = json_decode($action->data_json, true);
            $success = empty($data['errors']);
            /** @var ActionBody $body */
            $body = new $action->class;
            $postDesc = $body->listPost();
            ?>
            <tbody class="table__item-wrapper">
                <tr class="table__item">
                    <td class="table__cell">
                        <?= $action->class ?>
                        <?php if ($action->ajax) : ?>
                            <span class="routes__mark routes__mark--ajax">ajax</span>
                        <?php endif  ?>
                    </td>
                    <td class="table__cell routes__duration"><?= $action->duration_sec ?> sec</td>
                    <td class="table__cell">
                        <span class="routes__code routes__code--status routes__code--<?= $success ? 'ok' : 'error' ?>">
                            <?= $success ? 'Success' : 'Failure' ?>
                        </span>
                    </td>
                    <td class="table__cell"><?= date('d.m.Y H:i', $action->time) ?></td>
                </tr>
                <tr class="table__item-details-wrapper">
                    <td class="table__item-details" colspan="100">
                        <?php if (!empty($data['data']['get'])) : ?>
                            <span class="table__subheader">Get Data</span>
                            <div class="table__detail-wrapper">
                                <?php foreach ($data['data']['get'] as $field => $value) : ?>
                                    <div class="table__item-detail">
                                        <span class="routes__param-name"><?= $field ?></span>
                                        <span class="routes__param-equals">=</span>
                                        <span class="routes__param-value <?= $value === '' ? 'routes__param-value--empty' : '' ?>">
                                            <?= $value !== '' ? $value : 'empty' ?>
                                        </span>
                                    </div>
                                <?php endforeach ?>
                            </div>
                        <?php endif ?>
                        <?php if (!empty($data['data']['post'])) : ?>
                            <span class="table__subheader">Post Data</span>
                            <div class="table__detail-wrapper">
                                <?php foreach ($data['data']['post'] as $field => $value) :
                                    $empty = $value === '';
                                    $secret = ($postDesc[$field][0] ?? null) === ActionBody::POST_PASSWORD;
                                    ?>
                                    <div class="table__item-detail">
                                        <span class="routes__param-name"><?= $field ?></span>
                                        <span class="routes__param-equals">=</span>
                                        <span class="routes__param-value <?= $empty || $secret ? 'routes__param-value--empty' : '' ?>">
                                            <?= !$empty ? $value : 'empty' ?>
                                        </span>
                                    </div>
                                <?php endforeach ?>
                            </div>
                        <?php endif ?>
                    </td>
                </tr>
            </tbody>
        <?php endforeach ?>
    </table>
</div>

<span class="content__title">Доступные действия</span>
<div class="box box--table">
    <table class="table actions">
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
            ?>
            <tbody class="table__item-wrapper">
                <tr class="table__item">
                    <td class="table__cell"><?= $class ?></td>
                    <td class="table__cell">
                        <span class="actions__module"><?= $module ?></span>
                    </td>
                </tr>
                <tr class="table__item-details-wrapper">
                    <td class="table__item-details" colspan="100">
                        <?php if (!empty($action->listGet())) : ?>
                            <span class="table__subheader">GET Parameters</span>
                            <div class="table__detail-wrapper">
                                <?php foreach ($action->listGet() as $name => $desc) : ?>
                                    <div class="table__item-detail actions__param">
                                        <span class="actions__param-type actions__param-type--<?= $desc[0] ?>"><?= $desc[0] ?></span>
                                        <span class="actions__param-name"><?= $name ?></span>
                                        <span class="actions__param-desc"><?= $desc[1] ?></span>
                                    </div>
                                <?php endforeach ?>
                            </div>
                        <?php endif ?>
                        <?php if (!empty($action->listPost())) : ?>
                            <span class="table__subheader">POST Parameters</span>
                            <div class="table__detail-wrapper">
                                <?php foreach ($action->listPost() as $name => $desc) : ?>
                                    <div class="table__item-detail actions__param">
                                        <span class="actions__param-type actions__param-type--<?= $desc[0] ?>"><?= $desc[0] ?></span>
                                        <span class="actions__param-name"><?= $name ?></span>
                                        <span class="actions__param-desc"><?= $desc[1] ?></span>
                                    </div>
                                <?php endforeach ?>
                            </div>
                        <?php endif ?>
                    </td>
                </tr>
            </tbody>
        <?php endforeach ?>
    </table>
</div>