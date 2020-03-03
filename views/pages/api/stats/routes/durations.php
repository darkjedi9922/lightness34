<?php
use engine\statistics\lists\MultipleRouteIntervalTimeList;
use engine\statistics\tools\MultipleChartAPI;
(new MultipleChartAPI(MultipleRouteIntervalTimeList::class))->jsonResult();