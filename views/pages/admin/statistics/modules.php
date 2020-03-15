<?php /** @var frame\views\Page $self */

use frame\modules\Module;
use engine\users\User;
use frame\modules\Modules;

$listProps = ['list' => []];
$modules = Modules::get()->toArray();
foreach ($modules as $name => $module) {
    /** @var Module $module */
    $parent = $module->getParent();
    $rightsDesc = $module->createRightsDescription();
    $moduleProps = [
        'name' => $name,
        'class' => get_class($module),
        'parentModuleName' => $parent ? $parent->getName() : null,
        'rights' => $rightsDesc ? ['list' => []] : null
    ];
    if ($rightsDesc) {
        $additionChecks = $rightsDesc->listAdditionChecks(new User);
        foreach ($rightsDesc->listRights() as $name => $desc) {
            $right = [
                'name' => $name,
                'description' => $desc,
                'checkArgs' => null
            ];
            if (isset($additionChecks[$name])) {
                $checkReflector = new ReflectionFunction($additionChecks[$name]);
                $right['checkArgs'] = [];
                foreach ($checkReflector->getParameters() as $parameter) {
                    /** @var \ReflectionParameter $parameter */
                    $type = $parameter->getType();
                    $right['checkArgs'][] = $parameter->isVariadic() 
                        ? 'variadic' 
                        : ($type ? $type->getName() : 'mixed');
                }
            }
            $moduleProps['rights']['list'][] = $right;
        }
    }
    $listProps['list'][] = $moduleProps;
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