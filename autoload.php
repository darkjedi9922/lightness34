<?php
/**
 * Ищет классы строго по их пространствах имен. Если класс
 * \frame\tools\Class, то находится он должен в frame/tools/Class.php,
 * начиная от корня сайта.
 */
spl_autoload_register(function ($class) {
    $class = explode('\\', $class);
    $class = __DIR__.'/'.implode('/', $class).'.php';
    if (file_exists($class)) require $class;
});