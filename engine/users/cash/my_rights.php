<?php namespace engine\users\cash;

use frame\Core;
use frame\tools\Cash;
use frame\modules\UserRights;
use engine\users\cash\user_me;

class my_rights extends Cash
{
    /**
     * @throws \Exception if there is not such module.
     * @throws \Exception if there is no such module rights.
     */
    public static function get(string $module): UserRights
    {
        return self::cash($module, function() use ($module) {
            $moduleInstance = Core::$app->getModule($module);
            if (!$moduleInstance)
                throw new \Exception("There is no module $module.");

            $desc = $moduleInstance->createRightsDescription();
            if (!$desc) throw new \Exception("There is no module rights 
                of $module module.");

            return new UserRights($desc, $moduleInstance->getId(), user_me::get());
        });
    }
}