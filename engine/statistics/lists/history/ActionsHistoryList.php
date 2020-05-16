<?php namespace engine\statistics\lists\history;

use engine\statistics\stats\ActionStat;
use frame\actions\fields\PasswordField;
use frame\actions\UploadedFile;
use frame\stdlib\tools\units\ByteUnit;
use engine\statistics\stats\RouteStat;
use Iterator;
use frame\database\Records;

class ActionsHistoryList extends HistoryList
{
    protected function queryCountAll(): int
    {
        return Records::from(ActionStat::getTable())->count('id');
    }

    protected function getSqlQuery(
        string $sortField,
        string $sortOrder,
        int $offset,
        int $limit
    ): string {
        $actionTable = ActionStat::getTable();
        $countTable = 'stat_action_counts';
        return "SELECT
            $actionTable.id as action_id,
            $actionTable.class,
            $actionTable.duration_sec,
            $countTable.status
            FROM $actionTable INNER JOIN $countTable
                ON $actionTable.id = $countTable.action_id
            ORDER BY $sortField $sortOrder
            LIMIT $offset, $limit";
    }

    protected function assembleArray(Iterator $list): array
    {
        $resultHistory = [];
        foreach ($list as $row) {
            $action = ActionStat::selectIdentity($row['action_id']);
            $data = json_decode($action->data_json, true);

            $actionPost = [];
            /** @var ActionBody $body */
            $body = new $action->class;
            $postDesc = $body->listPost();
            foreach ($data['data']['post'] as $field => $value) {
                $type = $postDesc[$field] ?? null;
                $actionPost[] = [
                    'field' => $field,
                    'value' => !is_array($value) ? $value : 'array',
                    'isSecret' => $type === PasswordField::class
                        || is_subclass_of($type, PasswordField::class)
                ];
            }

            $actionFiles = [];
            foreach ($data['data']['files'] as $field => $fileData) {
                $file = new UploadedFile($fileData);
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
                $size = new ByteUnit($file->getSize(), ByteUnit::BYTES);
                list($convenientSize, $convenientUnit) = $size->calcConvenientForm();
                $actionFiles[] = [
                    'field' => $field,
                    'data' => $file->isEmpty() ? [] : [
                        'originalName' => $file->getOriginalName(),
                        'mime' => $fileData['mime'],
                        'tempName' => $file->getTempName(),
                        'status' => $status,
                        'size' => "$convenientSize $convenientUnit"
                    ]
                ];
            }

            $actionData = [
                'data' => [
                    'get' => $data['data']['get'],
                    'post' => $actionPost,
                    'files' => $actionFiles
                ],
                'errors' => $data['errors'],
                'result' => $data['result']
            ];

            switch ($action->response_type) {
                case ActionStat::RESPONSE_TYPE_JSON:
                    $responseType = 'json';
                    break;
                case ActionStat::RESPONSE_TYPE_ERROR:
                    $responseType = 'error';
                    break;
                case ActionStat::RESPONSE_TYPE_REDIRECT:
                    $responseType = 'redirect';
                    break;
            }

            $routeStat = RouteStat::selectIdentity($action->route_id);
            $resultHistory[] = [
                'class' => $action->class,
                'data' => $actionData,
                'responseType' => $responseType,
                'responseInfo' => $action->response_info,
                'isAjax' => (bool) $routeStat->ajax,
                'secondDuration' => $action->duration_sec,
                'time' => date('d.m.Y H:i', $routeStat->time)
            ];
        }

        return $resultHistory;
    }
}