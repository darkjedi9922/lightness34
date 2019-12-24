<?php namespace engine\statistics\macros;

use frame\route\Request;
use frame\actions\Action;
use frame\actions\UploadedFile;
use engine\statistics\stats\ActionStat;
use engine\statistics\stats\TimeStat;

class StartCollectActionStat extends BaseStatCollector
{
    private $stat;
    private $timer;

    public function __construct(ActionStat $stat, TimeStat $timer)
    {
        $this->stat = $stat;
        $this->timer = $timer;
    }

    protected function collect(...$args)
    {
        /** @var Action $action */
        $action = $args[0];
        
        $class = get_class($action->getBody());
        $this->stat->class = '\\\\' . str_replace('\\', '\\\\', $class);
        $this->stat->ajax = Request::isAjax();
        $this->stat->time = time();

        // Обрабатываем файлы перед началом выполнения действия, потому что во время
        // него эти файлы могут быть удалены и некоторую информацию уже в конце не
        // получить.
        $files = $this->toArrayFiles($action->getDataArray()[Action::FILES]);
        
        // Сохраняем это в приватную переменную, чтобы передать в финальный макрос.
        $this->stat->setHandledFiles($files);
        
        $this->timer->start();
    }

    private function toArrayFiles(array $files): array
    {
        $result = [];
        foreach ($files as $field => $file) {
            /** @var UploadedFile $file */
            $result[$field] = $file->toArray();
            $result[$field]['mime'] = !$file->isEmpty() ? $file->getMime() : '';
        }
        return $result;
    }
}