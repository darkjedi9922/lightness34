<?php namespace engine\statistics;

use frame\database\Records;
use engine\statistics\stats\ViewStat;
use engine\statistics\stats\ViewRouteStat;
use engine\statistics\macros\views\StartCollectViewStats;
use engine\statistics\macros\views\EndCollectViewStats;
use frame\modules\Module;
use frame\views\Layouted;
use frame\views\View;
use frame\cash\config;
use frame\cash\database;

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
        }
        $this->deleteOldStats();
    }

    public function getAppEventHandlers(): array
    {
        $this->routeStat->collectCurrent();
        $endViewCollector = new EndCollectViewStats($this->viewStartCollector);

        return [
            View::EVENT_LOAD_START => $this->viewStartCollector,
            View::EVENT_LOAD_END => $endViewCollector
        ];
    }

    private function deleteOldStats()
    {
        $routeTable = ViewRouteStat::getTable();
        $viewTable = ViewStat::getTable();
        $limit = config::get('statistics')->{'views.history.limit'};
        database::get()->query(
            "DELETE $routeTable
            FROM
                $routeTable
                LEFT JOIN $viewTable ON $routeTable.id = $viewTable.route_id
                INNER JOIN
                (
                    SELECT id FROM $routeTable 
                    ORDER BY id DESC LIMIT $limit, 999999
                ) AS cond_table
                    ON $routeTable.id = cond_table.id"
        );
    }
}