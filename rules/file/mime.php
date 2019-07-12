<?php

use frame\rules\RuleResult;
use frame\tools\File;

/**
 * Проверяет MIME-тип файла.
 * @param array $rule [string...] Допустимые MIME-типы файла.
 */
return function (array $rule, File $file, RuleResult $result) {
    return $result->result(in_array($file->getMime(), $rule, true));
};