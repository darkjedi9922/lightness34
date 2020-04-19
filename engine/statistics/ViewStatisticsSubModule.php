<?php namespace engine\statistics;

use frame\database\Records;
use engine\statistics\stats\ViewStat;
use engine\statistics\stats\ViewMetaStat;
use engine\statistics\macros\views\StartCollectViewStats;
use engine\statistics\macros\views\EndCollectViewStats;
use engine\statistics\macros\views\CollectViewError;
use engine\statistics\stats\TimeStat;
use frame\modules\Module;
use frame\views\Layouted;
use frame\views\View;
use frame\errors\Errors;
use engine\statistics\stats\RouteStat;

class ViewStatisticsSubModule extends BaseStatisticsSubModule
{
    private $routeStat;
    private $viewStartCollector;

    public function __construct(
        string $name,
        RouteStat $routeStat,
        ?Module $parent = null
    ) {
        parent::__construct($name, $parent);
        $this->routeStat = $routeStat;
        $this->viewStartCollector = new StartCollectViewStats;
    }

    public function clearStats()
    {
        Records::from(ViewMetaStat::getTable())->delete();
        Records::from(ViewStat::getTable())->delete();
    }

    public function endCollecting()
    {
        $routeId = $this->routeStat->getId();
        $stats = $this->viewStartCollector->getViewStats();
        foreach ($stats as $view) {
            /** @var View $view */
            /** @var ViewStat $stat */
            $stat = $stats[$view];
            
            // Если во время загрузки вида возникла ошибка, то заданный в начале
            // TimeStat так и остался. Завершим его.
            if ($stat->duration_sec instanceof TimeStat)
                $stat->duration_sec = $stat->duration_sec->resultInSeconds();

            $stat->layout_name = null;
            if ($view instanceof Layouted) {
                /** @var Layouted $view */
                if ($view->getLayout() !== null)
                    $stat->layout_name = $view->getLayout();
            }
            
            if ($stat->parent_id !== null) {
                // Если есть родитель, то сначала тут записан сам stat вида.
                // Если он родитель, значит он был записан раньше, значит у него уже
                // есть id.
                $stat->parent_id = $stat->parent_id->id;
            }
            
            $stat->route_id = $routeId;
            $stat->insert();

            $metaStats = $this->viewStartCollector->getViewMetaStats()[$view];
            foreach ($metaStats as $metaStat) {
                /** @var ViewMetaStat $metaStat */
                $metaStat->view_id = $stat->id;
                $metaStat->insert();
            }
        }
    }

    public function getAppEventHandlers(): array
    {
        $endViewCollector = new EndCollectViewStats($this->viewStartCollector);

        return [
            Errors::EVENT_ERROR => new CollectViewError($this->viewStartCollector),
            View::EVENT_LOAD_START => $this->viewStartCollector,
            View::EVENT_LOAD_END => $endViewCollector
        ];
    }
}