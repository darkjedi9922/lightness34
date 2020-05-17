<?php namespace frame\tools\logging;

interface Logger
{
    const EMERGENCY = 'Emergency';
    const ALERT     = 'Alert';
    const CRITICAL  = 'Critical';
    const ERROR     = 'Error';
    const WARNING   = 'Warning';
    const NOTICE    = 'Notice';
    const INFO      = 'Info';
    const DEBUG     = 'Debug';
    const TESTING   = 'Testing';

    public function write(string $type, string $message);

    /**
     * Считывает и парсит записи лога и возвращает их в удобном виде с помощью
     * массива ассоциативных массивов вида [
     *  'date' => '01.01.2020 12:42',
     *  'ip' => 'CLI',
     *  'type' => 'Testing',
     *  'message' => "Some\nmessage\nwith\several\nlines"
     * ]
     */
    public function read(): array;
}