<?php /** @var frame\views\Page $self */

use frame\views\Block;
use frame\views\Widget;
use frame\auth\Auth;
use frame\actions\ViewAction;
use engine\users\actions\LogoutAction;
use engine\users\cash\my_rights;

$auth = new Auth;
if ($auth->isLogged()) {
    $logout = new ViewAction(LogoutAction::class);
    $adminRights = my_rights::get('admin');
}

$mechanisms = [[
    'icon' => 'bolt',
    'title' => 'Действия',
    'desc' => 'Позволяют обрабатывать запросы из форм и другие CRUD запросы, 
        которые относятся к созданию, редактированию и удалению,
        предоставляя абстрактный класс для их пошаговой реализации.'
], [
    'icon' => 'key',
    'title' => 'Аутентификация',
    'desc' => 'Механизм входа пользователей в систему с возможностью запоминания
        входа, основыванный на использовании cookie. Позволяет определять
        и свои реализации.'
], [
    'icon' => 'check',
    'title' => 'Контроль доступа',
    'desc' => 'Определение прав пользователей, разбивая их по
        модулям, а также возможность реализации над правами своих
        проверок со сложной логикой. Взято лучшее из RBAC и ABAC.'
], [
    'icon' => 'stack',
    'title' => 'База данных',
    'desc' => 'Работа с СУБД MySQL, построение и выполнение SQL-запросов.
        Присутствует простая реализация ORM для работы с отдельными
        записями как с объектами.'
], [
    'icon' => 'caution',
    'title' => 'Обработка ошибок',
    'desc' => 'Устанавливает единый обработчик всех типов ошибок PHP с возможностью
        устанавливать на каждый тип ошибки пользовательский обработчик. Включает
        в себя вывод страниц ошибок.'
], [
    'icon' => 'brightness',
    'title' => 'События',
    'desc' => 'Классы могут иметь события, на которые можно подписываться и
        обрабатывать из любой части приложения. Из системы событий строится
        алгоритм обработки запросов.'
], [
    'icon' => 'crossroads',
    'title' => 'Маршрутизация',
    'desc' => 'Предоставляет классы для разбора URL, получения его параметров,
        а также данных из HTTP-запроса и установки значений в параметры 
        HTTP-ответа.'
], [
    'icon' => 'dev',
    'title' => 'Представления',
    'desc' => 'Состоят из страниц, блоков, виджетов, компоновщиков. Маршруты к 
        страницам соответствуют иерархии файлов в директории со страницами 
        и поддерживают формат ЧПУ.'
], [
    'icon' => 'gear',
    'title' => 'Конфигурация',
    'desc' => 'Возможность использования разных типов конфигов, которые
        можно взаимозаменять, обращаясь только по имени без указания формата.
        Содержит встроенные типы JSON и PHP.'
], [
    'icon' => 'plugin',
    'title' => 'Драйвера',
    'desc' => 'Определяют релизацию абстрактного функционала, чтобы его
        можно было взаимозаменять на глобальном уровне. Поддерживают
        декорирование прямо во время выполнения.'
], [
    'icon' => 'rocket',
    'title' => 'Кэширование',
    'desc' => 'Единичное определение значений, имеющие ресурсоемку инициализацию,
        а затем использование их в любых других частях приложения,
        как синглтоны.'
], [
    'icon' => 'document',
    'title' => 'Пагинация',
    'desc' => 'Возможность разбивать длинные списки материалов по номерам страниц.
        Сами пагинаторы являются представлениями, поэтому можно настроить
        их вид с использованием логики.'
]];
?>

<div class="header">
    <div class="header__container">
        <?php (new Block('header-menu'))->show() ?>
    </div>
</div>
<div class="slide">
    <span class="slide__header">Механизмы фреймворка</span>
    <div class="slide__content">
        <div class="fundamentals">
            <?php foreach ($mechanisms as $mechanism) {
                $widget = new Widget('fundamental');
                foreach ($mechanism as $key => $value)
                    $widget->setMeta($key, $value);
                $widget->show();
            } ?>
        </div>
    </div>
</div>
<div class="slide slide--dark">
    <span class="slide__header">Профиль</span>
    <div class="slide__content">
        <?php if ($auth->isLogged()) : ?>
            <?php if ($adminRights->can('enter')) : ?>
                <div class="footer__info">
                    <a href="/admin" class="footer__link">Перейти в админ-панель</a>
                </div>
            <?php endif ?>
            <div class="footer__info">
                <a href="<?= $logout->getUrl() ?>" class="footer__link">Выйти из профиля</a>
            </div>
        <?php else : ?>
            <?php (new Widget('welcome'))->show() ?>
        <?php endif ?>
    </div>
</div>