<?php /** @var frame\views\Page $self */

use frame\views\Block;
?>

<div class="header">
    <div class="header__container">
        <?php (new Block('header-menu'))->show() ?>
        <div class="header__content">
            <span class="header__text">Легкий, гибкий фреймворк для создания сайтов</span>
            <span class="header__subtext">Ничего лишнего</span>
        </div>
    </div>
</div>
<div class="slide">
    <div class="slide__container">
        <span class="slide__header">Основные характеристики Lightness:</span>
        <div class="fundamentals">
            <div class="fundamentals__item">
                <img src="/public/images/icons/toolbox.png" class="fundamentals__icon">
                <span class="fundamentals__title">Легкость</span>
                <p class="fundamentals__desc">
                    Основных классов, создающих и обеспечивающих архитектуру фреймворка немного,
                    а все остальное можно упростить и получить с помощью классов-инструментов.
                </p>
            </div>
            <div class="fundamentals__item">
                <img src="/public/images/icons/brush-pencil.png" class="fundamentals__icon">
                <span class="fundamentals__title">Гибкость в верстке</span>
                <p class="fundamentals__desc">
                    Любую часть в верстке и дизайне страниц изменять просто. Фронтенд смешивается с
                    бэкендом по минимуму, поэтому даже не знающим PHP разработчкам будет просто понять
                    что где найти и изменить.
                </p>
            </div>
            <div class="fundamentals__item">
                <img src="/public/images/icons/dev.png" class="fundamentals__icon">
                <span class="fundamentals__title">Простота</span>
                <p class="fundamentals__desc">
                    Всегда понятно что откуда появляется, не нужно долго искать в документации, чтобы
                    это узнать. Код написан достаточно самодокументированно, так что нужно знать лишь
                    самые базовые части фреймворка, а все остальное будет не сложно узнать из исходного
                    кода.
                </p>
            </div>
            <div class="fundamentals__item">
                <img src="/public/images/icons/rocket.png" class="fundamentals__icon">
                <span class="fundamentals__title">Производительность</span>
                <p class="fundamentals__desc">
                    Все объекты изначально создаются только в тот
                    момент, когда они используются. Фреймворк можно спокойно использовать даже лишь
                    для создания статических HTML сайтов, и при этом будут инициализированы только те части,
                    которые отвечают за роутинг страниц и не более.
                </p>
            </div>
        </div>
    </div>
</div>