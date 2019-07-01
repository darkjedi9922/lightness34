<?php

use frame\actions\RuleResult;
use frame\actions\UploadedFile;

/**
 * Обязательна ли загрузка файла (true|false). Если файла нет, останавливает
 * цепочку проверок независимо от значения правила.
 */
return function (bool $rule, ?UploadedFile $file, RuleResult $result): RuleResult {
    if ($file && $file->isLoaded()) return $result->succeed();
    return $result->stop()->result(!$rule);
};