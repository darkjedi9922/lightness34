<?php namespace frame\cash;

use frame\tools\Logger as FrameLogger;
use frame\cash\config;

class logger extends \frame\tools\Cash
{
    public static function get(): FrameLogger
    {
        return self::cash('app-logger', function() {
            $file = config::get('core')->{'log.file'};
            return new FrameLogger(ROOT_DIR . '/' . $file);
        });
    }
}