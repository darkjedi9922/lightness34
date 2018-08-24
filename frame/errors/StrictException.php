<?php namespace frame\errors;

/**
 * Такие исключения должны игнорировать любые настройки о выводе ошибок
 * и выводится всегда сразу на месте возникновения.
 */
class StrictException extends \Exception {}