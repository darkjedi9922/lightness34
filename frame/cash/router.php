<?php namespace frame\cash;

use frame\route\Request;
use frame\route\Router as FrameRouter;
use frame\tools\Cash;

/**
 * Возвращает роутер текущего запроса.
 */
class router extends Cash
{
    public static function get(): ?FrameRouter
    {
        return self::cash('current-router', function() {
           return new FrameRouter(Request::getRequest());
        });
    }
}