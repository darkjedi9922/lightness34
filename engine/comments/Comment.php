<?php namespace engine\comments;

use frame\Core;
use frame\database\Identity;
use frame\database\Records;

class Comment extends Identity
{
    public static function getTable(): string
    {
        return 'comments';
    }

    public static function count(string $module, int $materialId): int
    {
        return Records::from(static::getTable(), [
            'module_id' => Core::$app->getModule($module)->getId(),
            'material_id' => $materialId
        ])->count('id');
    }
}