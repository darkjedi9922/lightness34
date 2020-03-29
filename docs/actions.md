## Что такое *действия*

По сути - это обработчики форм. Но могут использоваться вне контекста форм для обработки **C**R**UD** запросов.

Алгоритм действия состоит из следующих частей:
1. Инициализация/валидация GET: `initialize()`
2. Валидация POST/FILES: `validate()`
3. Если нет ошибок POST/FILES, выполнение `succeed()`, иначе `fail()`
4. Если нет ошибок POST/FILES, редирект на `getSuccessRedirect()`, иначе `getFailRedirect()`. 

По умолчанию редирект при успехе и неудаче ведет на предыдущую страницу, если она есть, иначе на главную.

## Создание

Нужно создать класс, унаследованный от `frame\actions\ActionBody`. В нем нужно определить **один абстрактный метод** `succeed()` и, если нужно, переопределить стандартную реализацию других. **В этом классе можно переопределять любые методы.**

В методе `succeed()` указывается само ядро этого действия.

```php
<?php namespace engine\articles\actions;

class AddArticle extends frame\actions\ActionBody
{
    public function succeed(array $post, array $files)
    {
        // Пример тела действия.
        $article = new Article;
        $article->title = 'Some constant title';
        $article->content = 'Some constant text';
        $article->insert();
    }
}
```

Метод получает POST данные и загружаемые файлы. Как их использовать рассмотрим позже.

## Получение GET/POST/FILES

Нужно указать список данных и **их типы** в соответствующих методах `listGet()`, `listPost()`, `listFiles()`.

Типы полей являются **обертками** над их значениями и наследуются от `frame\actions\ActionField`. Во фреймворке уже есть несколько типов в `frame\actions\fields`, включая `MixedField`, которое может принимать любое значение. От него тоже можно наследоваться при определении своих типов.

Затем, GET-поля передаются в метод `initialize()`, а POST и FILES в `succeed()` и `fail()`. Подробнее о `fail()` в описании *валидации*. 

**Значения передаются в тех же обертках**, для получения примитива нужно обращаться к нему через метод `$field->get()`. Для FILES тип примитива - это объект `frame\actions\UploadedFile`.

```php
<?php namespace engine\comments\actions;

use frame\actions\fields\IntegerField;
use frame\actions\fields\StringField;
use frame\actions\fields\FileField;
use frame\actions\UploadedFile;

class AddArticle extends frame\actions\ActionBody
{
    public function listGet(): array
    {
        return ['category_id' => IntegerField::class];
    }

    public function listPost(): array
    {
        return [
            'title' => StringField::class,
            'text' => StringField::class
        ];
    }

    public function listFiles(): array
    {
        return ['image' => FileField::class];
    }

    private $category;

    public function initialize(array $get)
    {
        $this->category = $get['category_id']->get();
    }

    public function succeed(array $post, array $files)
    {
        $title = $post['title']->get();
        $text = $post['text']->get();
        
        /** @var UploadedFile $image */
        $image = $files['image']->get();

        // ...   
    }
}
```

Для FILES используется исключительно тип `FileField`. Никто не запрещает также наследоваться от него, конкретизируя (например, `AvatarImageField`).

Обертки типов полей используются для упрощения валидации зачений и дополнительных действий с ними (например, автоматическое кодирование спец. символов HTML).

## Валидация GET

Выполняется в методе `initialize()`. Так как GET-поля - часть запроса, в них следует выдавать HTTP-ошибки. Это делается с помощью выброса исключения `frame\errors\HttpError`. Для упрощения проверок и генерации ошибок 403 и 404 используется класс `frame\tools\Init`.

Также типы-обертки полей могут иметь встроенные методы для упрощения синтаксиа или сложности проверок.

```php
<?php namespace engine\comments\actions;

use frame\actions\fields\StringField;
use frame\actions\UploadedFile;
use frame\errors\HttpError;
use frame\tools\Init;

class AddArticle extends frame\actions\ActionBody
{
    public function listGet(): array
    {
        return ['category_name' => StringField::class];
    }

    public function initialize(array $get)
    {
        $category = $get['category_name'];

        // Способ 1. Самый примитивный.
        if (strlen($category->get()) < 3)
            throw new HttpError(404);

        // Способ 2. Есть встроенная проверка.
        if ($category->isTooShort(3))
            throw new HttpError(404);

        // Способ 3. Комбинация с Init (более желательно).
        // Init::require методы выбрасывают ошибку 404.
        Init::require(!$category->isTooShort(3));
    }

    public function succeed(array $post, array $files)
    {
        // ...   
    }
}
```

## Валидация POST/FILES

Выполняется в методе `validate()`, который должен вернуть `array` с кодами возникших ошибок. Коды ошибок следует выделять в константы класса.

Если есть хоть одна ошибка, то вместо `succeed()` выполняется `fail()`. Обычно нет необходимости его указывать, все зависит от специфики задачи. 

В примере ниже показана валидация POST, но с FILES схема та же.

```php
<?php namespace engine\comments\actions;

use frame\actions\fields\StringField;

class AddComment extends frame\actions\ActionBody
{
    const E_NO_TEXT = 1;
    const E_LONG_TEXT = 2;

    public function listPost(): array
    {
        return ['text' => StringField::class];
    }

    public function validate(array $post, array $files): array
    {
        $errors = [];
        
        /** @var StringField $text */ $text = $post['text'];
        
        if ($text->isEmpty()) $errors[] = static::E_NO_TEXT;
        else if ($text->isTooLong(255)) 
            $errors[] = static::E_LONG_TEXT;

        return $errors;
    }

    public function succeed(array $post, array $files)
    {
        // ...
    }

    public function fail(array $post, array $files)
    {
        // ...
    }
}
```

*Замечание:* **выброс исключений где-либо в действии не приведет к `fail()`.** Ошибка просто обработается на верхнем уровне приложения.

## Редирект после успешного/неудачного завершения

После `succeed()` выполняется редирект, возвращаемый в методе `getSuccessRedirect()`, а после `fail()` - на `getFailRedirect()`. По умолчанию оба редиректа возвращают предыдущий URL, если он есть.

Если нужно отключить редирект по умолчанию, нужно вернуть `null`.

```php
<?php namespace engine\articles\actions;

use frame\actions\fields\StringField;

class AddArticle extends frame\actions\ActionBody
{
    private $id;

    public function succeed(array $post, array $files)
    {
        $article = new Article;
        $article->title = 'Some constant title';
        $article->content = 'Some constant text';
        $this->id = $article->insert();
    }

    public function getSuccessRedirect(): ?string
    {
        return '/article?id=' . $this->id;
    }

    // В данном примере нет валидации, значит действие
    // всегда будет завершаться успешно. Этого редиректа
    // тут никогда не будет.
    public function getFailRedirect(): ?string
    {
        // ...
    }
```