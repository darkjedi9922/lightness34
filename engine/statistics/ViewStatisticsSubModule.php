<?php namespace engine\statistics;

use frame\database\Records;
use engine\statistics\stats\ViewStat;
use engine\statistics\stats\ViewRouteStat;
use engine\statistics\stats\ViewMetaStat;
use engine\statistics\macros\views\StartCollectViewStats;
use engine\statistics\macros\views\EndCollectViewStats;
use engine\statistics\macros\views\CollectViewError;
use engine\statistics\stats\TimeStat;
use frame\modules\Module;
use frame\views\Layouted;
use frame\views\View;
use frame\cash\config;
use frame\cash\database;
use frame\Core;

class ViewStatisticsSubModule extends BaseStatisticsSubModule
{
    private $routeStat;
    private $viewStartCollector;

    public function __construct(string $name, ?Module $parent = null)
    {
        parent::__construct($name, $parent);
        $this->routeStat = new ViewRouteStat;
        $this->viewStartCollector = new StartCollectViewStats;
    }

    public function clearStats()
    {
        Records::from(ViewMetaStat::getTable())->delete();
        Records::from(ViewStat::getTable())->delete();
        Records::from(ViewRouteStat::getTable())->delete();
    }

    public function endCollecting()
    {
        $routeId = $this->routeStat->insert();
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
        $this->deleteOldStats();
    }

    public function getAppEventHandlers(): array
    {
        $this->routeStat->collectCurrent();
        $endViewCollector = new EndCollectViewStats($this->viewStartCollector);

        return [
            Core::EVENT_APP_ERROR => new CollectViewError($this->viewStartCollector),
            View::EVENT_LOAD_START => $this->viewStartCollector,
            View::EVENT_LOAD_END => $endViewCollector
        ];
    }

    private function deleteOldStats()
    {
        $routeTable = ViewRouteStat::getTable();
        $viewTable = ViewStat::getTable();
        $metaTable = ViewMetaStat::getTable();
        $limit = config::get('statistics')->{'views.history.limit'};
        database::get()->query(
            "DELETE $routeTable
            FROM
                $routeTable
                LEFT JOIN $viewTable ON $routeTable.id = $viewTable.route_id
                LEFT JOIN $metaTable ON $viewTable.id = $metaTable.view_id
                INNER JOIN
                (
                    SELECT id FROM $routeTable 
                    ORDER BY id DESC LIMIT $limit, 999999
                ) AS cond_table
                    ON $routeTable.id = cond_table.id"
        );
    }
}