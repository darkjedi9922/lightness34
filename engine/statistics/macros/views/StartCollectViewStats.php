<?php namespace engine\statistics\macros\views;

use frame\views\View;
use engine\statistics\macros\BaseStatCollector;
use engine\statistics\stats\ViewStat;
use engine\statistics\stats\TimeStat;
use function lightlib\remove_prefix;
use SplObjectStorage;
use engine\statistics\stats\ViewMetaStat;
use frame\tools\Debug;

class StartCollectViewStats extends BaseStatCollector
{
    private $viewStats;
    private $viewMetaStats; 
    private $currentViewStat = null;

    public function __construct()
    {
        $this->viewStats = new SplObjectStorage;
        $this->viewMetaStats = new SplObjectStorage;
    }

    public function getViewStats(): SplObjectStorage
    {
        return $this->viewStats;
    }

    public function getViewMetaStats(): SplObjectStorage
    {
        return $this->viewMetaStats;
    }

    public function getCurrentViewStat(): ?ViewStat
    {
        return $this->currentViewStat;
    }

    public function endViewStatCollecting(View $view)
    {
        $stat = $this->viewStats[$view];

        // Преобразуем записанный ранее обьект таймера в результирующее время.
        $stat->duration_sec = $stat->duration_sec->resultInSeconds();

        $metaStats = [];
        foreach ($view->getMetaArray() as $name => $value) {
            $metaStat = new ViewMetaStat;
            $metaStat->name = $name;
            list($valueStrRepr, $valueType) = Debug::getStringAndType($value);
            $metaStat->value = $valueStrRepr;
            $metaStat->type = $valueType;
            $metaStats[] = $metaStat;
        }
        $this->viewMetaStats[$view] = $metaStats;

        // В данный момент, пока что, тут хранится сам stat вида, а не его id.
        // Нужно вернутся на прежнего родителя по окончанию вида. Если его и не было
        // будет записан null, что и должно быть.
        $this->currentViewStat = $stat->parent_id;
    }

    protected function collect(...$args)
    {
        /** @var View $view */
        $view = $args[0];

        $stat = new ViewStat;
        $stat->class = str_replace('\\', '\\\\', get_class($view));
        $stat->name = $view->name;
        $stat->file = remove_prefix($view->file, ROOT_DIR . '/');
        $stat->error = null;

        // Пока будем хранить сам stat вида, а после его вставки в БД, возьмем id.
        // Если родителя нет, будет записан null, что и должно быть в таком случае.
        $stat->parent_id = $this->currentViewStat;

        $this->viewStats[$view] = $stat;
        $this->viewMetaStats[$view] = [];
        $this->currentViewStat = $stat;

        $timer = new TimeStat;
        // Пока что запишем сюда обьект таймера.
        $stat->duration_sec = $timer;
        $timer->start();
    }
}