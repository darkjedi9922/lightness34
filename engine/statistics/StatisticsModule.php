<?php namespace engine\statistics;

use frame\core\Core;
use frame\modules\Module;
use frame\auth\RightsDesc;
use frame\events\Events;
use engine\statistics\BaseStatisticsSubModule;
use engine\statistics\macros\BaseStatCollector;
use engine\statistics\tools\StatEvents;
use frame\stdlib\cash\config;
use frame\stdlib\cash\route;
use frame\modules\Modules;

class StatisticsModule extends Module
{
    public function __construct(string $name, ?Module $parent = null)
    {
        parent::__construct($name, $parent);

        // Все подмодули будут привязываться к одному RouteStat маршрутизации.
        // К тому времени, как до них дойдет очередь вставлять записи, RouteStat
        // уже будет вставлен (помодуль статистики маршрутов указан ниже первым),
        // а значит уже будет иметь ID для привязки к нему.
        $routesSubmodule = new RouteStatisticsSubModule('routes', $this);
        $routeStat = $routesSubmodule->getRouteStat();

        $submodules = [
            $routesSubmodule,
            new EventStatisticsSubModule('events', $routeStat, $this),
            new ActionStatisticsSubModule('actions', $routeStat, $this),
            new DbStatisticsSubModule('db', $routeStat, $this),
            new CashStatisticsSubModule('cash', $routeStat, $this),
            new ViewStatisticsSubModule('views', $routeStat, $this)
        ];

        foreach ($submodules as $submodule) {
            Modules::getDriver()->set($submodule);
        }

        $config = config::get('statistics');
        if ($config->enabled) {
            $router = route::get();
            if ($router->isInAnyNamespace($config->ignoreRouteNamespaces)) return;
            $this->setupEventHandlers($submodules);
        }
    }

    public function createRightsDescription(): ?RightsDesc
    {
        return new StatsRightsDesc;
    }

    private function setupEventHandlers(array $submodules)
    {
        Core::$app->decorateDriver(Events::class, StatEvents::class);
        foreach ($submodules as $submodule) {
            /** @var BaseStatisticsSubModule $submodule */
            $macros = $submodule->getAppEventHandlers();
            foreach ($macros as $event => $macro) 
                Events::getDriver()->on($event, $macro);
        }

        // Нужно закончить сбор статистики (сохранить все в БД) после ее выключения. 
        // Иначе некоторая статистика собирается о сборе другой некоторой статистики.
        // (Например, записывается информация о вызовах БД для записи данных о 
        // собранной статистике).
        // 
        // На старте приложения добавляем обработчик конца приложения (чтобы он был
        // именно последним концом, т.к. после установки модуля статистики могут быть
        // установлены дополнительные обработчики события конца приложения, не
        // связанные со статистикой).
        // 
        // В этом обработчике конца убираем все установленные обработчики событий
        // статистики (выключаем сбор статистики) и только после этого уже добавляем
        // все в БД.
        Events::getDriver()->on(
            Core::EVENT_APP_START, 
            new class($submodules) extends BaseStatCollector {
                private $statModules = [];
                public function __construct(array $statModules) {
                    $this->statModules = $statModules;
                }
                protected function collect(...$args) {
                    Events::getDriver()->on(
                        Core::EVENT_APP_END, 
                        new class($this->statModules) extends BaseStatCollector {
                            private $statModules;
                            public function __construct(array $statModules) {
                                $this->statModules = $statModules;
                            }
                            protected function collect(...$args) {
                                foreach ($this->statModules as $module) {
                                    /** @var BaseStatisticsSubModule $module */
                                    $macros = $module->getAppEventHandlers();
                                    foreach ($macros as $event => $macro) {
                                        Events::getDriver()->off($event, $macro);
                                    }
                                }
                                foreach ($this->statModules as $module) {
                                    /** @var BaseStatisticsSubModule $module */
                                    $module->endCollecting();
                                }
                            }
                        }
                    );
                }
            }
        );
    }
}