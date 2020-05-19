<?php /** @var frame\views\Page $self */

use frame\views\Block;
use frame\views\Widget;
use frame\auth\Auth;
use frame\actions\ViewAction;
use engine\users\actions\LogoutAction;
use engine\users\User;

$auth = new Auth;
if ($auth->isLogged()) {
    $logout = new ViewAction(LogoutAction::class);
    $adminRights = User::getMyRights('admin');
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
    'desc' => 'Единичное определение значений, имеющие трудоемкую инициализацию.
        Для значений можно задавать разные типы хранилищ кэша. Присутствует
        статический тип, можно создавать свои.'
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
        фреймворка и предоставляет API для ее получения.'
], [
    'icon' => 'browser',
    'title' => 'Админ-панель',
    'desc' => 'Просмотр информации о модулях, их данных, в частности
        статистики работы механизмов приложения, а также выполнение их конфигурации.'
]];

$security = [[
    'icon' => 'security',
    'title' => 'Защита от CSRF',
    'desc' => 'В действия автоматически встраиваются уникальные токены, которые
        выдаются каждому пользователю.'
], [
    'icon' => 'interstate',
    'title' => 'Защита от XSS',
    'desc' => 'Во входных данных в действиях автоматически кодируются 
        спецсимволы HTML.'
], [
    'icon' => 'magicwand',
    'title' => 'SQL-инъекции',
    'desc' => 'В действиях и при использовании построителя запросов автоматически
        кодируются спецсимволы SQL.'
]];
?>

<div class="header">
    <div class="header__container">
        <?php (new Block('header-menu'))->show() ?>
    </div>
</div>
<div class="slide">
    <div class="slide__column">
        <span class="slide__header">Принципы фреймворка</span>
        <div class="slide__content">
            <div class="features">
                <div class="feature features__item">
                    <div class="feature__icon feature__icon--blue"><i class="icon-paper-plane"></i></div>
                    <h3 class="feature__title">Упор на простоту</h3>
                    <p class="feature__desc">
                        Стремление избавится от сложных оберток над возможностями,
                        которые можно использовать с помощью нативных средств.
                    </p>
                    <p class="feature__desc">
                        Например, использование нативного функционала SQL для
                        написания сложных запросов, вместо создания громадных
                        построителей, избавление от генерации графического интерфейса
                        с помощью классов в PHP, когда его можно написать напрямую на HTML.
                    </p>
                </div>
                <div class="feature features__item">
                    <div class="feature__icon feature__icon--red"><i class="icon-link"></i></div>
                    <h3 class="feature__title">Статическая типизация</h3>
                    <p class="feature__desc">
                        Стремление к меньшей степени использования динамических
                        возможностей языка с упором на статическую типизацию.
                    </p>
                    <p class="feature__desc">
                        Таким образом, среды разработки (IDE), применяемые в процессе разработки,
                        могут дать больше подсказок при статическом анализе кода и избавить
                        разработчика от поиска типов переменных и
                        частого обращения к документации API, а также помогает
                        избежать многих ошибок.
                    </p>
                </div>
                <div class="feature features__item">
                    <div class="feature__icon feature__icon--green"><i class="icon-cube"></i></div>
                    <h3 class="feature__title">Шаблонные методы</h3>
                    <p class="feature__desc">
                        Упор на использование паттернов проектирования,
                        выделяющие сложные алгоритмы,
                        отдавая разработчку лишь его части.
                    </p>
                    <p class="feature__desc">
                        Так, использование фреймворка превращается в простую реализацию
                        небольших абстрактных методов, которые дают удобный способ
                        пошагово определять простые части алгоритмов, не заставляя
                        разработчика думать как их собирать воедино.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="slide">
    <div class="slide__column">
        <span class="slide__header">Механизмы фреймворка</span>
        <div class="slide__content">
            <div class="fundamentals">
                <?php foreach ($mechanisms as $info) {
                    $widget = new Widget('fundamental');
                    $widget->setMeta('anchor', $info['icon']);
                    $widget->setMeta('icon', '/public/images/icons/' . $info['icon'] . '.png');
                    $widget->setMeta('title', $info['title']);
                    $widget->setMeta('desc', $info['desc']);
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
                    $widget->setMeta('anchor', $info['icon']);
                    $widget->setMeta('icon', '/public/images/icons/' . $info['icon'] . '.png');
                    $widget->setMeta('title', $info['title']);
                    $widget->setMeta('desc', $info['desc']);
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
                    $widget->setMeta('anchor', $info['icon']);
                    $widget->setMeta('icon', '/public/images/icons/' . $info['icon'] . '.png');
                    $widget->setMeta('title', $info['title']);
                    $widget->setMeta('desc', $info['desc']);
                    $widget->show();
                } ?>
            </div>
        </div>
    </div>
</div>
<div class="slide">
    <div class="slide__column">
        <span class="slide__header">Средства защиты</span>
        <div class="slide__content">
            <div class="fundamentals">
                <?php foreach ($security as $info) {
                    $widget = new Widget('fundamental');
                    $widget->setMeta('anchor', $info['icon']);
                    $widget->setMeta('icon', '/public/images/icons/' . $info['icon'] . '.png');
                    $widget->setMeta('title', $info['title']);
                    $widget->setMeta('desc', $info['desc']);
                    $widget->show();
                } ?>
            </div>
        </div>
    </div>
</div>
<div class="slide">
    <div class="slide__column slide__column--wide">
        <span class="slide__header">Сравнение с архитектурой MVC-фреймворков</span>
        <div class="slide__content">
            <div class="fundamentals fundamentals--two-parts">
                <?php
                $mvcWidget = new Widget('fundamental');
                $mvcWidget->setMeta('icon', '/images/diagrams/mvc.png');
                $mvcWidget->setMeta('title', 'MVC-фреймворк');
                $mvcWidget->setMeta('desc', 'В архитектуре MVC-фреймворков
                    запрос приходит в контроллер, а дальше
                    разработчику нужно самому решать что с ним делать, какой ответ
                    возвращать и в каком формате. Это дает большую гибкость,
                    но также разработчику приходится писать много тривиального 
                    кода.');
                $mvcWidget->show();

                $lightnessWidget = new Widget('fundamental');
                $lightnessWidget->setMeta('icon', '/images/diagrams/strict.png');
                $lightnessWidget->setMeta('title', 'Данный фреймворк');
                $lightnessWidget->setMeta('desc', 'Фреймворком реализовано
                    разные типы запросов. Разработчику остается лишь определить часть ответа,
                    специфическую для конкретной задачи, а ответ сам сгенерируется
                    в нужном формате. Расширяемость остается, так как можно независимо добавлять другие
                    алгоритмы обработки запросов.');
                $lightnessWidget->show();
                ?>
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
        <span class="slide__header">Безопасность</span>
        <div class="slide__content">
            <?php foreach ($security as $info) : ?>
                <div class="footer__info">
                    <a href="#<?= $info['icon'] ?>" class="footer__link">
                        <?= $info['title'] ?>
                    </a>
                </div>
            <?php endforeach ?>
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