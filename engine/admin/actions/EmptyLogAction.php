<?php namespace engine\admin\actions;

use engine\users\cash\user_me;
use frame\tools\Init;
use frame\actions\Action;
use frame\tools\FileReadingTracker;

/**
 * Права: очистка лога.
 * Требования, чтобы файл существовал, нет.
 * Параметры: file - путь к файлу.
 */
class EmptyLogAction extends Action
{
    private $file = '';
    
    protected function initialize(array $get)
    {
        Init::accessRight('admin', 'clear-logs');
        
        $this->file = $this->getData('get', 'file');

        Init::require($this->file !== null);
    }
    
    protected function succeed(array $post, array $files)
    {
        if (file_exists($this->file)) {
            file_put_contents($this->file, '');
            $tracker = new FileReadingTracker($this->file, user_me::get()->id);
            $tracker->setUnreadedForAll();
        }
    }
}