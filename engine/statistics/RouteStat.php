<?php namespace engine\statistics;

use frame\cash\config;

class RouteStat extends Statistics
{
    const VALUE_UNKNOWN = 'unknown';
    const ROUTE_TYPE_PAGE = 'page';
    const ROUTE_TYPE_ACTION = 'action';

    private $lastRoutes = [];

    public function __construct()
    {
        parent::__construct('route');
        $state = $this->getPreviousState();
        $this->lastRoutes = $state['lastRoutes'] ?? [];
        $this->initCurrentRoute();
    }

    public function setUrl(string $url)
    {
        $this->lastRoutes[0]['url'] = $url;
    }

    public function setRoute(string $route)
    {
        $this->lastRoutes[0]['route'] = $route;
    }

    public function setParams(array $params)
    {
        $this->lastRoutes[0]['params'] = $params;
    }

    public function toArray(): array
    {
        return [
            'lastRoutes' => $this->lastRoutes
        ];
    }

    private function initCurrentRoute()
    {
        array_unshift($this->lastRoutes, [
            'url' => null,
            'route' => null,
            'params' => null
        ]);
        $config = config::get('statistics');
        $maxAmount = $config->{'routes.lastRoutes.maxAmount'};
        if ($maxAmount < 1) $maxAmount = 1;
        array_splice($this->lastRoutes, $maxAmount);
    }
}