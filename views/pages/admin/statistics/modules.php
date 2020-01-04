<?php /** @var frame\views\Page $self */

use frame\Core;
use frame\tools\Init;
use frame\modules\Module;

Init::accessRight('admin', 'see-logs');

$listProps = ['list' => []];
$modules = Core::$app->getModules();
foreach ($modules as $name => $module) {
    /** @var Module $module */
    $parent = $module->getParent();
    $listProps['list'][] = [
        'name' => $name,
        'class' => get_class($module),
        'parentModuleName' => $parent ? $parent->getName() : null
    ];
}
?>

<div class="content__header">
    <div class="breadcrumbs">
        <span class="breadcrumbs__item">Мониторинг</span>
        <span class="breadcrumbs__divisor"></span>
        <span class="breadcrumbs__item breadcrumbs__item--current">Подключенные модули</span>
    </div>
</div>
<div id="modules" data-props='<?= json_encode($listProps, JSON_HEX_AMP) ?>'></div>