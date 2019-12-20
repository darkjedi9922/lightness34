<?php namespace engine\statistics;

use frame\Core;
use frame\modules\Module;
use frame\modules\RightsDesc;
use engine\statistics\stats\RouteStat;
use engine\statistics\stats\DynamicRouteParam;
use frame\actions\ActionMacro;
use frame\route\Request;
use frame\route\Response;
use frame\cash\config;
use frame\views\View;
use frame\views\DynamicPage;
use frame\cash\database;

use function lightlib\encode_specials;

class StatisticsModule extends Module
{
    private $config;

    public function __construct(string $name, ?Module $parent = null)
    {
        parent::__construct($name, $parent);

        $router = Core::$app->router;
        $this->config = config::get('statistics');
        if ($router->isInAnyNamespace($this->config->ignorePageNamespaces)) return;

        $this->setupRouteStatistics();
    }

    public function createRightsDescription(): ?RightsDesc
    {
        return null;
    }

    private function setupRouteStatistics()
    {
        $router = Core::$app->router;
        $routeStat = new RouteStat;

        Core::$app->on(Core::EVENT_APP_START, function() use ($routeStat, $router) {
            $routeStat->url = $router->url;
            $routeStat->type = RouteStat::ROUTE_TYPE_PAGE;
            $routeStat->ajax = Request::isAjax();
            $routeStat->time = time();
        });

        Core::$app->on(ActionMacro::EVENT_ACTION_TRIGGERED, function() use (
            $routeStat
        ) {
            $routeStat->type = RouteStat::ROUTE_TYPE_ACTION;
        });
        
        Core::$app->on(Core::EVENT_APP_ERROR, function(\Throwable $error) use (
            $routeStat
        ) {
            $routeStat->code_info = encode_specials($error->getMessage());
        });

        $collectPage = new CollectPage;
        Core::$app->on(View::EVENT_LOAD_START, function(View $view) use (
            $collectPage
        ) {
            $collectPage->exec($view);
        });

        Core::$app->on(Core::EVENT_APP_END, function() use (
            $routeStat,
            $collectPage
        ) {
            $routeStat->code = Response::getCode();
            switch ((int) ($routeStat->code / 100)) {
                case 1:
                case 2:
                    $routeStat->code_info = '';
                    break;
            }
            switch ($routeStat->code) {
                case 302:
                    $redirect = Response::getUrl();
                    $routeStat->code_info = encode_specials("Redirect to url: $redirect");
            }

            $page = $collectPage->page;
            if ($page) {
                $routeStat->viewfile = str_replace(ROOT_DIR . '/', '', $page->file);
                if (get_class($page) === DynamicPage::class) {
                    /** @var DynamicPage $page */
                    $routeStat->type = RouteStat::ROUTE_TYPE_DYNAMIC_PAGE;
                }
            }

            $routeId = $routeStat->insert();

            if ($page && get_class($page) === DynamicPage::class) {            
                $args = $page->getArguments();
                for ($i = 0, $c = count($args); $i < $c; ++$i) {
                    $param = new DynamicRouteParam;
                    $param->route_id = $routeId;
                    $param->index = $i;
                    $param->value = encode_specials($args[$i]);
                    $param->insert();
                }
            }

            $routeTable = RouteStat::getTable();
            $paramTable = DynamicRouteParam::getTable();
            $limit = $this->config->{'routes.lastRoutes.maxAmount'};
            database::get()->query(
                "DELETE $routeTable, $paramTable
                FROM ($routeTable LEFT OUTER JOIN $paramTable ON $routeTable.id = $paramTable.route_id)
                INNER JOIN (
                    SELECT id FROM $routeTable ORDER BY id DESC LIMIT $limit, 999999
                ) AS cond_table ON $routeTable.id = cond_table.id"
            );
        });
    }
}