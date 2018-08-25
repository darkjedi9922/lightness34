<?php namespace frame\errors\handlers;

interface ErrorHandler
{
    /**
     * Сюда должны попадать все Throwable исключения типа, на который
     * был задан обработчик в Core.
     * 
     * @param \Throwable $error
     */
    function handle($error);
}