<?php namespace frame\tools;

use frame\Client;

class Logger
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

    public function __construct($filename)
    {
        // Если файла нет, он создастся сам
        $this->handle = fopen($filename, 'at');
        $this->filename = $filename;
    }
    public function write($type, $message)
    {
        $date = date('d.m.Y H:i');
        $ip = Client::getIp();
        $text = "[$date - $ip] $type: $message\n";
        fwrite($this->handle, $text);
    }

    private $handle = null;
    private $filename = null;
}