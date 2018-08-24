<?php namespace frame\handlers;

interface ErrorHandler
{
    /**
     * Сюда должны попадать все Throwable исключения типа, на который
     * был задан обработчик в Application.
     * 
     * @param \Throwable $error
     */
    function handle($error);
}