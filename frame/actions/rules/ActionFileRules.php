<?php namespace frame\actions\rules;

use frame\actions\UploadedFile;
use frame\actions\RuleResult;
use function lightlib\dump;
use function lightlib\bytes;

/**
 * Правила обработки загружаемых файлов. Все обработчики как значение получают
 * экземпляр класса UploadedFile.
 */
class ActionFileRules
{
    /**
     * @return \callback
     */
    function getMaxSizeRule()
    {
        /**
         * @param array $rule [float, string] Максимальный допустимый размер файла.
         * Первый элемент - число размера, второй - единица измерения. Допускаются
         * такие единицы измерения: B, KB, MB, GB.
         * @param UploadedFile $file
         * @param RuleResult $result
         */
        return function($rule, $file, $result) {
            return $result->result($file->getSize() <= bytes($rule[0], $rule[1]));
        };
    }
}