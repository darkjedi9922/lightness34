<?php namespace engine\statistics;

use frame\modules\RightsDesc;
use frame\modules\UserRights;
use frame\modules\GroupUser;
use frame\modules\Modules;

class StatsRightsDesc extends RightsDesc
{
    public function listRights(): array
    {
        return [
            'see' => 'See statistics',
            'clear' => 'Clear statistics',
            'configure' => 'Configure the module',
        ];
    }

    public function listAdditionChecks(GroupUser $user): array
    {
        return [
            'configure' => function() use ($user) {
                $statsModule = Modules::get()->findByName('stat')->getId();
                $statsRights = new UserRights($this, $statsModule, $user);
                return $statsRights->can('see');
            }
        ];
    }
}