<?php /** @var frame\views\Block $self */

use engine\users\cash\my_rights;
use engine\users\Group;
use engine\users\cash\user_me;

$adminRights = my_rights::get('admin');
$usersRights = my_rights::get('users');
$messageRights = my_rights::get('messages');
$articleRights = my_rights::get('articles');
$commentsRights = my_rights::get('articles/comments');
$statsRights = my_rights::get('stat');
$isIRoot = user_me::get()->group_id === Group::ROOT_ID;

$globalItems = [];

$globalItems[] = [
    'name' => 'Главная',
    'link' => '/admin/home',
    'icon' => 'home'
];

if ($usersRights->can('see-list')) $globalItems[] = [
    'name' => 'Пользователи',
    'link' => '/admin/users',
    'icon' => 'user'
];

if ($articleRights->can('see-list')) $globalItems[] = [
    'name' => 'Статьи',
    'link' => '/admin/articles',
    'icon' => 'doc-text'
];

if ($articleRights->can('see-new-list')) $globalItems[] = [
    'name' => 'Новое',
    'icon' => 'rss',
    'submenu' => [[
        'name' => 'Статьи',
        'link' => '/admin/new/articles',
        'icon' => 'doc-text'
    ]]
];

$settingsItems = [];

if ($isIRoot) $settingsItems[] = [
    'name' => 'Общие',
    'icon' => 'sliders',
    'link' => '/admin/settings/core'
];

$userSettingsItems = [];

if ($usersRights->can('setup')) $userSettingsItems[] = [
    'name' => 'Общие',
    'icon' => 'cog',
    'link' => '/admin/settings/users'
];
if ($isIRoot) $userSettingsItems[] = [
    'name' => 'Группы',
    'icon' => 'id-card-o',
    'link' => '/admin/users/groups'
];
if ($usersRights->can('configure-genders')) $userSettingsItems[] = [
    'name' => 'Пол',
    'icon' => 'transgender',
    'link' => '/admin/users/genders'
];
if ($messageRights->can('setup')) $userSettingsItems[] = [
    'name' => 'Сообщения',
    'icon' => 'email',
    'link' => '/admin/settings/messages'
];

if (!empty($userSettingsItems)) $settingsItems[] = [
    'name' => 'Пользователи',
    'icon' => 'user',
    'submenu' => $userSettingsItems
];

if ($articleRights->can('configure')) $settingsItems[] = [
    'name' => 'Статьи',
    'icon' => 'doc-text',
    'link' => '/admin/settings/articles'
];
if ($commentsRights->can('configure')) $settingsItems[] = [
    'name' => 'Комментарии',
    'icon' => 'commenting',
    'link' => '/admin/settings/comments'
];
if ($statsRights->can('configure')) $settingsItems[] = [
    'name' => 'Мониторинг',
    'icon' => 'chart-bar',
    'link' => '/admin/settings/statistics'
];
if ($isIRoot) $settingsItems[] = [
    'name' => 'Админ-панель',
    'icon' => 'television',
    'link' => '/admin/settings/admin'
];

if (!empty($settingsItems)) $globalItems[] = [
    'name' => 'Настройки',
    'icon' => 'cog',
    'submenu' => $settingsItems
];

if ($statsRights->can('see')) $globalItems[] = [
    'name' => 'Мониторинг',
    'icon' => 'chart-bar',
    'submenu' => [[
        'name' => 'Маршруты',
        'icon' => 'link',
        'submenu' => [[
            'name' => 'История',
            'icon' => 'clock',
            'link' => '/admin/statistics/routes/history'
        ], [
            'name' => 'Статистика',
            'icon' => 'chart-area',
            'link' => '/admin/statistics/routes/charts'
        ]]
    ], [
        'name' => 'События',
        'icon' => 'flash-1',
        'submenu' => [[
            'name' => 'История',
            'icon' => 'clock',
            'link' => '/admin/statistics/events/history'
        ], [
            'name' => 'Обработка',
            'icon' => 'chart-area',
            'link' => '/admin/statistics/events/charts'
        ]]
    ], [
        'name' => 'Модули',
        'icon' => 'cube',
        'link' => '/admin/statistics/modules'
    ], [
        'name' => 'Представления',
        'icon' => 'television',
        'submenu' => [[
            'name' => 'История',
            'icon' => 'clock',
            'link' => '/admin/statistics/views/history'
        ], [
            'name' => 'Статистика',
            'icon' => 'chart-area',
            'link' => '/admin/statistics/views/charts'
        ]],
    ], [
        'name' => 'Действия',
        'icon' => 'superpowers',
        'submenu' => [[
            'name' => 'Каталог',
            'icon' => 'folder',
            'link' => '/admin/statistics/actions/catalog'
        ], [
            'name' => 'История',
            'icon' => 'clock',
            'link' => '/admin/statistics/actions/history'
        ], [
            'name' => 'Статистика',
            'icon' => 'chart-area',
            'link' => '/admin/statistics/actions/charts'
        ]]
    ], [
        'name' => 'База данных',
        'icon' => 'database',
        'submenu' => [[
            'name' => 'История',
            'icon' => 'clock',
            'link' => '/admin/statistics/queries/history'
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
        'name' => 'Кэш',
        'icon' => 'floppy',
        'link' => '/admin/statistics/cash/history'
    ]]
];

if ($adminRights->can('see-logs')) $globalItems[] = [
    'name' => 'Лог',
    'icon' => 'doc-text',
    'link' => '/admin/log'
];

$menu = ['items' => $globalItems];
?>

<div id="menu" data-props='<?= json_encode($menu, JSON_HEX_AMP) ?>'></div>