<?php namespace frame\actions\rules;

use frame\actions\UploadedFile;
use frame\actions\RuleResult;
use frame\tools\File;

use function lightlib\bytes;

/**
 * Правила обработки загружаемых файлов. Все обработчики как значение получают
 * экземпляр класса UploadedFile.
 */
class ActionFileRules
{
    /**
     * Обязательна ли загрузка файла (true|false). Если файла нет, останавливает
     * цепочку проверок независимо от значения правила.
     * @return \callback
     */
    function getMustLoadRule()
    {
        /**
         * @param bool $rule
         * @param UploadedFile $file
         * @param RuleResult $result
         * @return RuleResult
         */
        return function($rule, $file, $result) {
            if ($file->isLoaded()) return $result->succeed();
            return $result->stop()->result(!$rule);
        };
    }

    /**
     * @return \callback
     */
    function getMaxSizeRule()
    {
        /**
         * @param array $rule [int, string] Максимальный допустимый размер файла.
         * Первый элемент - число размера, второй - единица измерения. Допускаются
         * такие единицы измерения: B, KB, MB, GB.
         * @param UploadedFile $file
         * @param RuleResult $result
         * @return RuleResult
         */
        return function($rule, $file, $result) {
            $bytes = $rule[1] === 'B' ? $rule[0] : bytes($rule[0], $rule[1]);
            $sizeIsOk = $file->getSize() <= $bytes;
            $noIniSizeError = !$file->hasError(UPLOAD_ERR_INI_SIZE);
            $noFormSizeError = !$file->hasError(UPLOAD_ERR_FORM_SIZE);
            return $result->result($sizeIsOk && $noIniSizeError && $noFormSizeError);
        };
    }

    /**
     * Проверяет MIME-тип файла.
     * @return \callback
     */
    function getMimeRule()
    {
        /**
         * @param array $rule [string...] Допустимые MIME-типы файла.
         * @param File $file
         * @param RuleResult $result
         * @return RuleResult
         */
        return function($rule, $file, $result) {
            return $result->result(in_array($file->getMime(), $rule, true));
        };
    }
}