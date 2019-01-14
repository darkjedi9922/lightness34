## Lightness 3.4 Framework

### Мини-документация (пока-что мини)

1. Все публичные свойства в классах - **read-only**. Некоторые генерируются динамически и защищены от записи, а остальные: измените их и что-то сломаете - виноваты вы.
2. Пути к страницам сайта определяются расположением файлов страниц в директории, указанной в **pages.folder** в конфиге **core.json**. По умолчанию это **view/pages**. То есть, если файл находится в _view/pages/users/profile.php_, то доступ к этой странице будет по адресу _site.com/users/profile_, а **название страницы** - _users/profile_.

---

## Конфиг

### core.json

- **site.name** - название сайта.
- **pages.folder** - директория расположения страниц сайта. _По умолчанию: view/pages_. Для других видов аналогично.
- **pages.defaultLayout** - *имя-шаблона / null*. Стандартный шаблон для всех страниц, где не задан свой шаблон. *По умолчанию: null*. Для других видов аналогично. Виды *Layout* и *Value* не имеют данной настройки.
- **log.enabled** - _true / false_ - логировать ли **все** ошибки. _По умолчанию: true_.
- **log.file** - путь к файлу лога. _По умолчанию: log.txt_.
- **errors.showMode** - _display / errorPage / errorDevPage_ / _none_ - что делать, когда происходит неожиданная ошибка, исключение и т.д. Значение _"display"_ выводит текст ошибки, _"errorPage"_ выводит красивую страницу о неожиданной ошибке (предпочтительный вариант в производственной версии), "_errorDevPage_" выводит красивую страницу с текстом и разбором ошибки (предпочтительный вариант во время разработки). _"none"_ не выводит ошибки вообще. **Замечание:** исключения типа **StrictException** изначально игнорируют эту настройку и выводятся всегда сразу на месте появления. _По умолчанию: display_.
- **errors.errorPage** - _название_ страницы неожиданной ошибки.
- **errors.errorDevPage** - _название_ страницы неожиданной ошибки с текстом и разбором ошибки.
- **errors.404.page** - *название* страницы ошибки 404. *По умолчанию: 404*. При чем, так можно устанавливать страницы и для других кодов http ошибок (403, 402 и т.д).
- **database.host** - хост БД для подключения.
- **database.username** - имя пользователя БД для подключения.
- **database.password** - пароль пользователя БД для подключения.
- **database.dbname** - имя БД для подключения.
- **actions.defaultFailRedirectMode**: *"back" / null* - что делать экшну при неуспешном выполнении, если не переопределен метод getFailRedirect(): "back" - перейти на предыдущую страницу, null - статический режим. При переходе на предыдущую страницу все ошибки и данные сохраняются в сессии и загружаются после редиректа, сразу стираясь. Это дает возможность, когда при неудачном выполнении один раз показать ошибки, а при перезагрузке страницы эти ошибки уже исчезнут. Но при статическом режиме, после неудачного выполнения экшна продолжает показываться страница, на которой он был выполнен, а тогда можно использовать информацию об ошибках и данных прямо из того же объекта экшна, что выполнялся, никуда их не сохраняя. Это более производительное решение, но тогда при перезагрузке страницы этот экшн будет выполнятся снова с теми же данными, даже если он не был вызван "правильным" путем. По умолчанию: *null*.
- **actions.validationConfigFolder** - директория расположения конфигов валидации экшнов. Может быть задан *null* для полного отключения механизма валидации экшнов через конфиги. Конфиг должен иметь имя вида "*ИмяКлассаЭкшна*.json" и располагаться по структуре пространства имен класса этого экшна. Например, если класс экшна находится в пространстве имен **engine\base**, то конфиг этого экшна должен находится в папке "*actions.validationConfigFolder*/**engine/base**". Если для экшна не существует файла с конфигом, он не будет учитываться при валидации данных этого экшна. По умолчанию: *public/actions*.
- **actions.noRuleMode** - "error" / "ignore" -  Что делать, если для конфиг-валидации экшна в экшне не установлен механизм обработки правила. Значение *error* выбрасывает исключение типа NoRuleError, а *ignore* просто пропускает правило. По умолчанию: *error*.

---

## Виды

### Терминология

1. **Вид** - все что связано с версткой: страница, блок, виджет, шаблон.
5. **Файл вида/страницы/...** - сам файл с версткой вида.
2. **Имя вида** - путь к файлу вида без расширения. Например: view/pages/users/profile.
3. **Имя страницы** - путь к файлу страницы в директории страниц без расширения. Например: директория страниц - view/pages, тогда имя страницы - users/profile.
4. **Имя блока, виджета, шаблона** - аналогично страницам соотвественно своим директориям расположения файлов видов.

### Общее о всех видах

1. **$this** во всех файлах видов указывает на объект текущего вида.

### Шаблоны (Layout)

1. Для вывода дочернего вида внутри файла шаблона нужно использовать **$this->child->content**

---

## Разное

### Система версионирования

Версии фреймворка устанавливаются по такой схеме:
***global.base.major.minor.fixes***, где:

- **global** - изменяется, когда проект переписывается с нуля с совершенно новой архитектурой;
- **base** - изменяется, когда проект переписывается заново на основе тех же принципов и архитектуры, определенные соответствующей *global* версией;
- **major** - изменяется, когда в проект добавляются какие-либо новые большие функциональные механизмы или, если хотя-бы один такой механизм был изменен так, что новая версия не совместима со старой;
- **minor** - изменяется, когда хотя-бы в один уже существующий механизм добавляется новый функционал;
- **fixes** - изменяется при фиксах багов и мелких улучшениях, после которых новая версия остается совместимой со старой.

При этом, всегда меняется только одна компонента версии (как можно более высокого уровня).