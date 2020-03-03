<?php
use engine\statistics\lists\MultipleRouteIntervalCountList;
use engine\statistics\tools\MultipleChartAPI;
(new MultipleChartAPI(MultipleRouteIntervalCountList::class))->jsonResult();