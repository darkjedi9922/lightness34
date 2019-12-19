<?php namespace engine\statistics;

use frame\Core;
use frame\modules\Module;
use frame\modules\RightsDesc;
use engine\statistics\stats\RouteStat;
use frame\actions\ActionMacro;
use frame\route\Request;
use frame\route\Response;
use frame\cash\config;
use frame\views\View;
use frame\views\DynamicPage;

use function lightlib\encode_specials;

class StatisticsModule extends Module
{
    public function __construct(string $name, ?Module $parent = null)
    {
        parent::__construct($name, $parent);

        $router = Core::$app->router;
        $config = config::get('statistics');
        if ($router->isInAnyNamespace($config->ignorePageNamespaces)) return;

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

        Core::$app->on(View::EVENT_LOAD_START, function(View $view) use (
            $routeStat
        ) {
            if (get_class($view) === DynamicPage::class) {
                $routeStat->url = $view->name;
                $routeStat->type = RouteStat::ROUTE_TYPE_DYNAMIC_PAGE;
            }
        });

        Core::$app->on(Core::EVENT_APP_END, function() use ($routeStat) {
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
            $routeStat->insert();
        });
    }
}