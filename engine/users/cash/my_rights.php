<?php namespace engine\users\cash;

use frame\cash\CashValue;
use frame\modules\Modules;
use frame\auth\UserRights;
use engine\users\cash\user_me;
use frame\cash\CashStorage;
use frame\cash\StaticCashStorage;

class my_rights extends CashValue
{
    public static function getStorage(): CashStorage
    {
        return StaticCashStorage::getDriver();
    }

    /**
     * @throws \Exception if there is not such module.
     * @throws \Exception if there is no such module rights.
     * @return UserRights
     */
    public static function get(string $module)
    {
        return self::cash($module, function() use ($module) {
            $moduleInstance = Modules::getDriver()->findByName($module);
            if (!$moduleInstance)
                throw new \Exception("There is no module $module.");

            $desc = $moduleInstance->createRightsDescription();
            if (!$desc) throw new \Exception(
                "Module '$module' has no rights description."
            );

            return new UserRights($desc, $moduleInstance->getId(), user_me::get());
        });
    }
}