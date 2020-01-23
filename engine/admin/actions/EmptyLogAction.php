<?php namespace engine\admin\actions;

use frame\tools\Init;
use frame\actions\ActionBody;
use frame\tools\trackers\read\ReadLimitedProgressTracker;

/**
 * Права: очистка лога.
 * Требования, чтобы файл существовал, нет.
 * Параметры: file - путь к файлу.
 */
class EmptyLogAction extends ActionBody
{
    private $file = '';

    public function listGet(): array
    {
        return [
            'file' => [self::GET_STRING, 'A log file']
        ];
    }

    public function initialize(array $get)
    {
        Init::accessRight('admin', 'clear-logs');
        $this->file = $get['file'];
    }
    
    public function succeed(array $post, array $files)
    {
        if (file_exists($this->file)) {
            file_put_contents($this->file, '');
            $tracker = new ReadLimitedProgressTracker('log', crc32($this->file), 0);
            $tracker->reset();
        }
    }
}