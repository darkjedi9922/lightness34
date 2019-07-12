<?php

use frame\rules\RuleResult;
use frame\actions\UploadedFile;
use function lightlib\bytes;

/**
 * @param array $rule [int, string] Максимальный допустимый размер файла.
 * Первый элемент - число размера, второй - единица измерения. Допускаются
 * такие единицы измерения: B, KB, MB, GB.
 */
return function (array $rule, UploadedFile $file, RuleResult $result): RuleResult {
    $bytes = $rule[1] === 'B' ? $rule[0] : bytes($rule[0], $rule[1]);
    $sizeIsOk = $file->getSize() <= $bytes;
    $noIniSizeError = !$file->hasError(UPLOAD_ERR_INI_SIZE);
    $noFormSizeError = !$file->hasError(UPLOAD_ERR_FORM_SIZE);
    return $result->result($sizeIsOk && $noIniSizeError && $noFormSizeError);
};