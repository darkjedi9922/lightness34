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
        записями как с объектами. Включает в себя построитель запросов.'
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
    'desc' => 'Фреймворк предоставляет и использует классы для разбора URL, 
        получения его параметров, данных из HTTP-запроса и установки
        значений в параметры HTTP-ответа.'
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
]];

$additions = [[
    'icon' => 'typography',
    'title' => 'JSON для фронтенда',
    'desc' => 'Генерация совместимого с HTML и JavaScript формата JSON для передачи
        данных из PHP в JavaScript без дополнительных запросов API.'
], [
    'icon' => 'ribbon',
    'title' => 'Трекеры прочтения',
    'desc' => 'Возможность прикреплять отслеживание состояния и прогресса прочтения
        контента веб-сайта для каждого пользователя.'
], [
    'icon' => 'booklet',
    'title' => 'Cookie и cессии',
    'desc' => 'Во фреймворке предоставлены утилиты для работы с cookie и сессиями
        клиентов. При чем данные в cookie имитируют мгновенное обновление.'
], [
    'icon' => 'compose',
    'title' => 'Логирование',
    'desc' => 'Все ошибки детально описываются в логе, включая стек вызова.
        Записи можно логировать, разделяя по встроенным или своим уровням.'
], [
    'icon' => 'flag',
    'title' => 'Семафоры',
    'desc' => 'Кроссплатформенная реализация семафор для синхронизации доступа
        параллельных процессов с помощью специальных флагов блокировки.'
], [
    'icon' => 'barchart',
    'title' => 'Единицы измерения',
    'desc' => 'Утилиты для работы с единицами измерения, включая конвертирование
        и нахождение самого человекопонятного значения.'
], [
    'icon' => 'recycle',
    'title' => 'Демоны',
    'desc' => 'Реализация обработчиков событий, подобных процессам-демонам в ОС,
        которые запускаются регулярно через заданный интервал времени.'
], [
    'icon' => 'document',
    'title' => 'Пагинация',
    'desc' => 'Разбиение списков по номерам страниц.
        Сами пагинаторы это представления, поэтому можно настроить
        их вид с использованием логики.'
]];

$modules = [[
    'icon' => 'contacts',
    'title' => 'Пользователи',
    'desc' => 'Добавление, редактирование пользователей. Просмотр списков профилей
        пользователей. Включает управление их генедрами и группами.'
], [
    'icon' => 'mail',
    'title' => 'Сообщения',
    'desc' => 'Пользователи могут вести друг с другом диалоги. Модуль дает
        возможности управлять списками диалогов и сообщениями в них.'
], [
    'icon' => 'news',
    'title' => 'Статьи',
    'desc' => 'Добавление, редактирование, удаление статей, просмотр списков всех
        статей, а также списков новых для пользователя статей.'
], [
    'icon' => 'megaphone2',
    'title' => 'Комментарии',
    'desc' => 'Добавление комментариев к материалам модуля. 
        Реализованы как подмодуль и могут быть присоединены к любому модулю.'
], [
    'icon' => 'pie-chart',
    'title' => 'Статистика',
    'desc' => 'Динамически подключает сбор статистики работы основных функций
        фреймворка и получать ее с помощью API запросов.'
], [
    'icon' => 'browser',
    'title' => 'Админ-панель',
    'desc' => 'Просмотр информации о модулях, их данных, в частности
        статистики работы механизмов приложения, а также выполнение их конфигурации.'
]];
?>

<div class="header">
    <div class="header__container">
        <?php (new Block('header-menu'))->show() ?>
    </div>
</div>
<div class="slide">
    <div class="slide__column">
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
</div>
<div class="slide">
    <div class="slide__column">
        <span class="slide__header">Дополнительные возможности</span>
        <div class="slide__content">
            <div class="fundamentals">
                <?php foreach ($additions as $info) {
                    $widget = new Widget('fundamental');
                    foreach ($info as $key => $value)
                        $widget->setMeta($key, $value);
                    $widget->show();
                } ?>
            </div>
        </div>
    </div>
</div>
<div class="slide">
    <div class="slide__column">
        <span class="slide__header">Встроенные пользовательские модули</span>
        <div class="slide__content">
            <div class="fundamentals">
                <?php foreach ($modules as $info) {
                    $widget = new Widget('fundamental');
                    foreach ($info as $key => $value)
                        $widget->setMeta($key, $value);
                    $widget->show();
                } ?>
            </div>
        </div>
    </div>
</div>

<!-- Футер -->
<div class="slide slide--dark">
    <div class="slide__column">
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
    <div class="slide__column">
        <span class="slide__header">Механизмы</span>
        <div class="slide__content">
            <?php foreach ($mechanisms as $mechanism) : ?>
                <div class="footer__info">
                    <a href="#<?= $mechanism['icon'] ?>" class="footer__link">
                        <?= $mechanism['title'] ?>
                    </a>
                </div>
            <?php endforeach ?>
        </div>
    </div>
    <div class="slide__column">
        <span class="slide__header">Дополнительно</span>
        <div class="slide__content">
            <?php foreach ($additions as $info) : ?>
                <div class="footer__info">
                    <a href="#<?= $info['icon'] ?>" class="footer__link">
                        <?= $info['title'] ?>
                    </a>
                </div>
            <?php endforeach ?>
        </div>
    </div>
    <div class="slide__column">
        <span class="slide__header">Модули</span>
        <div class="slide__content">
            <?php foreach ($modules as $info) : ?>
                <div class="footer__info">
                    <a href="#<?= $info['icon'] ?>" class="footer__link">
                        <?= $info['title'] ?>
                    </a>
                </div>
            <?php endforeach ?>
        </div>
    </div>
</div>