<?php namespace engine\statistics\actions;

use frame\actions\ActionBody;
use frame\actions\fields\BooleanField;
use frame\actions\fields\IntegerField;
use frame\actions\fields\StringField;
use frame\config\Json;
use frame\tools\Init;
use InvalidArgumentException;

class EditConfig extends ActionBody
{
    const STORE_TIME_UNIT_HOURS = 'hours';
    const STORE_TIME_UNIT_DAYS = 'days';
    const STORE_TIME_UNIT_MONTHS = 'months';

    /**
     * @throws InvalidArgumentException if unit is not equals one of this action
     * unit constants.
     */
    public static function calcSecondsFromStoreTime(int $value, string $unit): int
    {
        switch ($unit) {
            case self::STORE_TIME_UNIT_HOURS: return $value * 60 * 60;
            case self::STORE_TIME_UNIT_DAYS: return $value * 24 * 60 * 60;
            case self::STORE_TIME_UNIT_MONTHS: return $value * 30 * 24 * 60 * 60;
            default:
                throw new InvalidArgumentException("Unit $unit is not supported");
        }
    }

    /**
     * @throws InvalidArgumentException if unit is not equals one of this action
     * unit constants.
     */
    public static function calcStoreTimeFromSeconds(int $seconds, string $unit): int
    {
        switch ($unit) {
            case self::STORE_TIME_UNIT_HOURS: return $seconds / (60 * 60);
            case self::STORE_TIME_UNIT_DAYS: return $seconds / (24 * 60 * 60);
            case self::STORE_TIME_UNIT_MONTHS: return $seconds / (30 * 24 * 60 * 60);
            default:
                throw new InvalidArgumentException("Unit $unit is not supported");
        }
    }

    public function listPost(): array
    {
        return [
            'enabled' => BooleanField::class,
            'routes->history->limit' => IntegerField::class,
            'events->history->limit' => IntegerField::class,
            'views->history->limit' => IntegerField::class,
            'actions->history->limit' => IntegerField::class,
            'queries->history->limit' => IntegerField::class,
            'cash->history->limit' => IntegerField::class,
            'storeTimeValue' => IntegerField::class,
            'storeTimeUnit' => StringField::class
        ];
    }

    public function initialize(array $get)
    {
        Init::accessRight('stat', 'configure');
    }

    public function succeed(array $post, array $files)
    {
        $config = new Json(ROOT_DIR . '/config/statistics.json');
        $config->enabled = $post['enabled']->get();
        $config->{'routes.history.limit'} = $post['routes->history->limit']->get();
        $config->{'events.history.limit'} = $post['events->history->limit']->get();
        $config->{'views.history.limit'} = $post['views->history->limit']->get();
        $config->{'actions.history.limit'} = $post['actions->history->limit']->get();
        $config->{'queries.history.limit'} = $post['queries->history->limit']->get();
        $config->{'cash.history.limit'} = $post['cash->history->limit']->get();
        $config->storeTimeInSeconds = self::calcSecondsFromStoreTime(
            $post['storeTimeValue']->get(),
            $post['storeTimeUnit']->get()
        );
        $config->save();
    }

    public function validate(array $post, array $files): array
    {
        $unit = $post['storeTimeUnit']->get();
        Init::require(in_array($unit, [
            self::STORE_TIME_UNIT_HOURS,
            self::STORE_TIME_UNIT_DAYS,
            self::STORE_TIME_UNIT_MONTHS
        ]));

        return [];
    }
}