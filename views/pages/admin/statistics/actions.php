<?php /** @var frame\views\Page $self */

use frame\tools\Init;
use frame\actions\ActionBody;
use frame\lists\base\IdentityList;
use engine\statistics\lists\ActionList;
use engine\statistics\stats\ActionStat;
use engine\statistics\actions\ClearStatistics;
use frame\actions\UploadedFile;
use frame\actions\ViewAction;

use function lightlib\bytes_to;
use function lightlib\encode_specials;

Init::accessRight('admin', 'see-logs');

$actions = new ActionList;
$history = new IdentityList(ActionStat::class, ['id' => 'DESC']);
$clear = new ViewAction(ClearStatistics::class, ['module' => 'stat/actions']);
?>

<div class="content__header">
    <div class="breadcrumbs">
        <span class="breadcrumbs__item">Мониторинг</span>
        <span class="breadcrumbs__divisor"></span>
        <span class="breadcrumbs__item breadcrumbs__item--current">Действия</span>
    </div>
    <a href="<?= $clear->getUrl() ?>" class="button">Очистить статистику</a>
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
            $errors = $data['errors'];
            $result = $data['result'];
            $fatal = $action->response_type === ActionStat::RESPONSE_TYPE_ERROR;
            $success = !$fatal && empty($errors);
            $fail = !$fatal && !empty($errors);
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
                        <span class="routes__code routes__code--status routes__code--<?= $success ? 'ok' : ($fail ? 'warning' : 'error') ?>">
                            <?= $success ? 'Success' : ($fail ? 'Failure' : 'Fatal') ?>
                        </span>
                    </td>
                    <td class="table__cell"><?= date('d.m.Y H:i', $action->time) ?></td>
                </tr>
                <tr class="table__details-wrapper">
                    <td class="table__details table__details--indent" colspan="100">
                        <div class="details">
                            <span class="details__header">Get Data</span>
                            <?php if (empty($data['data']['get'])) : ?>
                                <div class="param">
                                    <!-- Скорее всего это условие никогда не выполнится, -->
                                    <!-- потому что каждый экшн имеет как минимум поле с пустым идентификатором. -->
                                    <!-- Но на всякий, если вдруг это изменится, оставлю такой случай тут, -->
                                    <!-- чтобы не нужно было вспоминать потом добавить это. -->
                                    <!-- Или может быть такое, что экшн просто запущен вручную. -->
                                    <span class="param__value param__value--empty">No data</span>
                                </div>
                            <?php else : ?>
                                <?php foreach ($data['data']['get'] as $field => $value) : ?>
                                    <div class="param">
                                        <span class="param__name"><?= $field ?></span>
                                        <span class="param__value <?= $value === '' ? 'param__value--empty' : '' ?>">
                                            <?= $value !== '' ? $value : 'empty' ?>
                                        </span>
                                    </div>
                                <?php endforeach ?>
                            <?php endif ?>
                        </div>
                        <div class="details">
                            <span class="details__header">Post Data</span>
                            <?php if (empty($data['data']['post'])) : ?>
                                <div class="param">
                                    <span class="param__value param__value--empty">No data</span>
                                </div>
                            <?php else : ?>
                                <?php foreach ($data['data']['post'] as $field => $value) :
                                    $empty = $value === '';
                                    $secret = ($postDesc[$field][0] ?? null) === ActionBody::POST_PASSWORD;
                                    ?>
                                    <div class="param">
                                        <span class="param__name"><?= $field ?></span>
                                        <span class="param__value <?= $empty || $secret || is_array($value) ? 'param__value--empty' : '' ?>">
                                            <?= !$empty ? (!is_array($value) ? $value : 'array') : 'empty' ?>
                                        </span>
                                    </div>
                                <?php endforeach ?>
                            <?php endif ?>
                        </div>
                        <?php if (empty($data['data']['files'])) : ?>
                            <div class="details">
                                <span class="details__header">Uploaded Files</span>
                                <div class="param">
                                    <span class="param__value param__value--empty">No files uploaded</span>
                                </div>
                            </div>
                        <?php else : ?>
                            <?php foreach ($data['data']['files'] as $field => $fileData) :
                                $file = new UploadedFile($fileData);
                                ?>
                                <div class="details">
                                    <span class="details__header">Uploaded file - <?= $field ?></span>
                                    <?php if ($file->isEmpty()) : ?>
                                        <div class="param">
                                            <span class="param__value param__value--empty">Empty</span>
                                        </div>
                                    <?php else : ?>
                                        <div class="param">
                                            <span class="param__name">name</span>
                                            <span class="param__value"><?= $file->getOriginalName() ?></span>
                                        </div>
                                        <div class="param">
                                            <span class="param__name">mime type</span>
                                            <span class="param__value <?= !$fileData['mime'] ? 'param__value--empty' : '' ?>">
                                                <?= $fileData['mime'] ? $fileData['mime'] : 'file was not successfully loaded' ?>
                                            </span>
                                        </div>
                                        <div class="param">
                                            <span class="param__name">temporary path</span>
                                            <span class="param__value <?= !$file->getTempName() ? 'param__value--empty' : '' ?>">
                                                <?= $file->getTempName() ? $file->getTempName() : 'file was not successfully loaded' ?>
                                            </span>
                                        </div>
                                        <div class="param">
                                            <span class="param__name">status</span>
                                            <?php
                                            switch ($file->getError()) {
                                                case UploadedFile::UPLOAD_ERR_INI_SIZE:
                                                    $status = 'INI size error';
                                                    break;
                                                case UploadedFile::UPLOAD_ERR_FORM_SIZE:
                                                    $status = 'Form size error';
                                                    break;
                                                case UploadedFile::UPLOAD_ERR_PARTIAL:
                                                    $status = 'Partial error';
                                                    break;
                                                case UploadedFile::UPLOAD_ERR_NO_TMP_DIR:
                                                    $status = 'No temporary directory';
                                                    break;
                                                case UploadedFile::UPLOAD_ERR_CANT_WRITE:
                                                    $status = 'Could not write the file on the disk';
                                                    break;
                                                case UploadedFile::UPLOAD_ERR_EXTENSION:
                                                    $status = 'Some extension stopped the file loading';
                                                    break;
                                                default:
                                                    $status = 'OK';
                                            }
                                            ?>
                                            <span class="param__value"><?= $status ?></span>
                                        </div>
                                        <div class="param">
                                            <span class="param__name">size</span>
                                            <span class="param__value"><?= round(bytes_to($file->getSize(), 'KB'), 1) ?> KB</span>
                                        </div>
                                    <?php endif ?>
                                </div>
                            <?php endforeach ?>
                        <?php endif ?>
                        <div class="details">
                            <span class="details__header">Result data</span>
                            <?php if (empty($result)) : ?>
                                <div class="param">
                                    <span class="param__value param__value--empty">No result data</span>
                                </div>
                            <?php else : ?>
                                <?php foreach ($result as $key => $value) : ?>
                                    <div class="param">
                                        <span class="param__name"><?= $key ?></span>
                                        <span class="param__value"><?= $value ?></span>
                                    </div>
                                <?php endforeach ?>
                            <?php endif ?>
                        </div>
                        <?php if ($fail) : ?>
                            <div class="details">
                                <span class="status status--warning">
                                    <span class="status__name">Validation error codes:</span>
                                    <span class="status__message"><?= implode(', ', $errors) ?></span>
                                    <span class="status__hint">See their meaning in the class</span>
                                </span>
                            </div>
                            <?php if ($action->response_type === ActionStat::RESPONSE_TYPE_REDIRECT) : ?>
                                <div class="details">
                                    <span class="status status--ok">
                                        <span class="status__name">Redirect:</span>
                                        <span class="status__message"><?= $action->response_info ?></span>
                                    </span>
                                </div>
                            <?php endif ?>
                        <?php elseif ($fatal) : ?>
                            <div class="details">
                                <span class="status status--error">
                                    <span class="status__name">Fatal error:</span>
                                    <?php if ($action->response_info) : ?>
                                        <span class="status__message"><?= $action->response_info ?></span>
                                    <?php else : ?>
                                        <span class="status__message status__message--empty">The error was not specified<span>
                                            <?php endif ?>
                                        </span>
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
                                    <?php foreach ($list as $name => $desc) : ?>
                                        <div class="param action-param">
                                            <span class="param__name"><?= $name ?></span>
                                            <span class="param__value action-param__type action-param__type--<?= $desc[0] ?>"><?= $desc[0] ?></span>
                                            <span class="action-param__desc"><?= $desc[1] ?></span>
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