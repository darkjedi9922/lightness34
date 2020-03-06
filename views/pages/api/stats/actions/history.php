<?php
use frame\tools\JsonEncoder;
use frame\actions\ViewAction;
use engine\statistics\actions\ClearStatistics;
use frame\lists\base\IdentityList;
use engine\statistics\stats\ActionStat;
use frame\actions\fields\PasswordField;
use frame\actions\UploadedFile;
use function lightlib\bytes_to;

$clear = new ViewAction(ClearStatistics::class, ['module' => 'stat/actions']);

$resultHistory = [];
$history = new IdentityList(ActionStat::class, ['id' => 'DESC']);
foreach ($history as $action) {
    /** @var ActionStat $action */
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
        $actionFiles[] = [
            'field' => $field,
            'data' => $file->isEmpty() ? [] : [
                'originalName' => $file->getOriginalName(),
                'mime' => $fileData['mime'],
                'tempName' => $file->getTempName(),
                'status' => $status,
                'size' => round(bytes_to($file->getSize(), 'KB'), 1) . ' KB'
            ]
        ];
    }

    $errors = $data['errors'];
    $result = $data['result'];
    $actionData = [
        'data' => [
            'get' => $data['data']['get'],
            'post' => $actionPost,
            'files' => $actionFiles
        ],
        'errors' => $data['errors'],
        'result' => $data['result']
    ];

    $fatal = $action->response_type === ActionStat::RESPONSE_TYPE_ERROR;
    $success = !$fatal && empty($errors);
    $fail = !$fatal && !empty($errors);

    switch ($action->response_type) {
        case ActionStat::RESPONSE_TYPE_JSON: $responseType = 'json'; break;
        case ActionStat::RESPONSE_TYPE_ERROR: $responseType = 'error'; break;
        case ActionStat::RESPONSE_TYPE_REDIRECT: $responseType = 'redirect'; break;
    }
    $resultHistory[] = [
        'class' => $action->class,
        'data' => $actionData,
        'responseType' => $responseType,
        'responseInfo' => $action->response_info,
        'isAjax' => (bool) $action->ajax,
        'secondDuration' => $action->duration_sec,
        'time' => date('d.m.Y H:i', $action->time)
    ];
}

echo JsonEncoder::forViewText([
    'clearUrl' => $clear->getUrl(),
    'history' => $resultHistory
]);