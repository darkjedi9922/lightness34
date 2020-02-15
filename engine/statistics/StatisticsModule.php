<?php namespace engine\statistics;

use frame\core\Core;
use frame\modules\Module;
use frame\modules\RightsDesc;
use engine\statistics\BaseStatisticsSubModule;
use frame\cash\config;
use engine\statistics\macros\BaseStatCollector;

class StatisticsModule extends Module
{
    public function __construct(string $name, ?Module $parent = null)
    {
        parent::__construct($name, $parent);

        $submodules = [
            new EventStatisticsSubModule('events', $this),
            new RouteStatisticsSubModule('routes', $this),
            new ActionStatisticsSubModule('actions', $this),
            new DbStatisticsSubModule('db', $this),
            new CashStatisticsSubModule('cash', $this),
            new ViewStatisticsSubModule('views', $this)
        ];

        foreach ($submodules as $submodule) {
            Core::$app->setModule($submodule);
        }

        $config = config::get('statistics');
        if ($config->enabled) {
            $router = Core::$app->router;
            if ($router->isInAnyNamespace($config->ignoreRouteNamespaces)) return;
            $this->setupEventHandlers($submodules);
        }
    }

    public function createRightsDescription(): ?RightsDesc
    {
        return null;
    }

    private function setupEventHandlers(array $submodules)
    {
        foreach ($submodules as $submodule) {
            /** @var BaseStatisticsSubModule $submodule */
            $macros = $submodule->getAppEventHandlers();
            foreach ($macros as $event => $macro) Core::$app->events->on($event, $macro);
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
        Core::$app->events->on(
            Core::EVENT_APP_START, 
            new class($submodules) extends BaseStatCollector {
                private $statModules = [];
                public function __construct(array $statModules) {
                    $this->statModules = $statModules;
                }
                protected function collect(...$args) {
                    Core::$app->events->on(
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
                                        Core::$app->events->off($event, $macro);
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