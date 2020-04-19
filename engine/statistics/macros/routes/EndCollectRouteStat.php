<?php namespace engine\statistics\macros\routes;

use frame\route\Response;
use frame\views\DynamicPage;
use engine\statistics\stats\TimeStat;
use engine\statistics\stats\RouteStat;
use engine\statistics\stats\DynamicRouteParam;
use engine\statistics\macros\BaseStatCollector;

use function lightlib\encode_specials;

class EndCollectRouteStat extends BaseStatCollector
{
    private $stat;
    private $collectPage;
    private $timer;

    public function __construct(
        RouteStat $stat,
        CollectPageRouteStat $collectPage,
        TimeStat $timer
    ) {
        $this->stat = $stat;
        $this->collectPage = $collectPage;
        $this->timer = $timer;
    }

    protected function collect(...$args)
    {
        $this->collectCodeInfo();
        $this->collectViewfileAndType();
        $this->stat->duration_sec = $this->timer->resultInSeconds();
        $this->stat->insert();
        $this->collectDynamicParams();
    }

    private function collectCodeInfo()
    {
        $this->stat->code = Response::getDriver()->getCode();
        switch ((int)($this->stat->code / 100)) {
            case 1:
            case 2:
                $this->stat->code_info = '';
                break;
        }
        switch ($this->stat->code) {
            case 302:
                $redirect = Response::getDriver()->getUrl();
                $this->stat->code_info = encode_specials(
                    "Redirect to url: $redirect"
                );
        }
    }

    private function collectViewfileAndType()
    {
        $page = $this->collectPage->page;
        if ($page) {
            $this->stat->viewfile = str_replace(ROOT_DIR . '/', '', $page->file);
            if (get_class($page) === DynamicPage::class) {
                $this->stat->type = RouteStat::ROUTE_TYPE_DYNAMIC_PAGE;
            }
        }
    }

    /**
     * Must be called after the stat insert.
     */
    private function collectDynamicParams()
    {
        $page = $this->collectPage->page;
        if ($page && get_class($page) === DynamicPage::class) {
            $args = $page->getArguments();
            for ($i = 0, $c = count($args); $i < $c; ++$i) {
                $param = new DynamicRouteParam;
                $param->route_id = $this->stat->id;
                $param->index = $i;
                $param->value = encode_specials($args[$i]);
                $param->insert();
            }
        }
    }
}