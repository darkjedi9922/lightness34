<?php /** @var frame\views\Block $self */

use engine\users\cash\my_group;
use engine\users\cash\my_rights;

$group = my_group::get();
$rights = my_rights::get('admin');

$menu = [
    'items' => [[
        'name' => 'Главная',
        'link' => '/admin/home',
        'icon' => 'home'
    ], [
        'name' => 'Пользователи',
        'link' => '/admin/users',
        'icon' => 'user'
    ], [
        'name' => 'Статьи',
        'link' => '/admin/articles',
        'icon' => 'doc-text'
    ], [
        'name' => 'Новое',
        'icon' => 'rss',
        'submenu' => [[
            'name' => 'Статьи',
            'link' => '/admin/new/articles',
            'icon' => 'doc-text'
        ]]
    ]]
];

if ($group->id === $group::ROOT_ID) $menu = array_merge_recursive($menu, [
    'items' => [[
        'name' => 'Настройки',
        'icon' => 'cog',
        'submenu' => [[
            'name' => 'Общие',
            'icon' => 'sliders',
            'link' => '/admin/settings/core'
        ], [
            'name' => 'Пользователи',
            'icon' => 'user',
            'submenu' => [[
                'name' => 'Общие',
                'icon' => 'cog',
                'link' => '/admin/settings/users'
            ], [
                'name' => 'Группы',
                'icon' => 'id-card-o',
                'link' => '/admin/users/groups'
            ], [
                'name' => 'Пол',
                'icon' => 'transgender',
                'link' => '/admin/users/genders'
            ], [
                'name' => 'Сообщения',
                'icon' => 'email',
                'link' => '/admin/settings/messages'
            ]]
        ], [
            'name' => 'Статьи',
            'icon' => 'doc-text',
            'link' => '/admin/settings/articles'
        ], [
            'name' => 'Комментарии',
            'icon' => 'commenting',
            'link' => '/admin/settings/comments'
        ], [
            'name' => 'Мониторинг',
            'icon' => 'chart-bar',
            'link' => '/admin/settings/statistics'
        ], [
            'name' => 'Админ-панель',
            'icon' => 'television',
            'link' => '/admin/settings/admin'
        ]]
    ]]
]);

if ($rights->can('see-logs')) $menu = array_merge_recursive($menu, [
    'items' => [[
        'name' => 'Мониторинг',
        'icon' => 'chart-bar',
        'submenu' => [[
            'name' => 'Маршруты',
            'icon' => 'link',
            'link' => '/admin/statistics/routes'
        ], [
            'name' => 'События',
            'icon' => 'flash-1',
            'link' => '/admin/statistics/events'
        ], [
            'name' => 'Модули',
            'icon' => 'cube',
            'link' => '/admin/statistics/modules'
        ], [
            'name' => 'Виды',
            'icon' => 'television',
            'link' => '/admin/statistics/views'
        ], [
            'name' => 'Действия',
            'icon' => 'superpowers',
            'link' => '/admin/statistics/actions'
        ], [
            'name' => 'База данных',
            'icon' => 'database',
            'submenu' => [[
                'name' => 'История',
                'icon' => 'clock',
                'link' => '/admin/statistics/database/history'
            ], [
                'name' => 'Запросы',
                'icon' => 'flag',
                'link' => '/admin/statistics/database/queries'
            ], [
                'name' => 'Таблицы',
                'icon' => 'folder',
                'link' => '/admin/statistics/database/tables'
            ]]
        ], [
            'name' => 'Кеш',
            'icon' => 'floppy',
            'link' => '/admin/statistics/cash'
        ]]
    ], [
        'name' => 'Лог',
        'icon' => 'doc-text',
        'link' => '/admin/log'
    ]]
]);
?>

<div id="menu" data-props='<?= json_encode($menu, JSON_HEX_AMP) ?>'></div>