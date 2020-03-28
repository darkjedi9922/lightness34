## Что такое *действия*

По сути - это обработчики форм. Но могут использоваться вне контекста форм для обработки **C**R**UD** запросов.

## Создание

Нужно создать класс, унаследованный от `frame\actions\ActionBody`. В нем нужно определить абстрактный метод `succeed()` и, если нужно, переопределить стандартную реализацию других. **В этом классе можно переопределять любые методы.**

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

**Значения передаются в тех же обертках**, для получения примитива нужно обращаться к нему через метод `$field->get()`.

```php
<?php namespace engine\comments\actions;

use frame\actions\fields\IntegerField;
use frame\actions\fields\StringField;
use frame\actions\fields\FileField;

class AddArticle extends frame\actions\ActionBody
{
    public listGet(): array
    {
        return ['category_id' => IntegerField::class];
    }

    public listPost(): array
    {
        return [
            'title' => STringField::class,
            'text' => StringField::class
        ];
    }

    public listFiles(): array
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
        
        // ...   
    }
}
```

Для FILES используется исключительно тип `FileField`. Никто не запрещает также наследоваться от него, конкретизируя (например, `AvatarImageField`).

Обертки типов полей используются для упрощения валидации зачений и дополнительных действий с ними (например, автоматическое кодирование спец. символов HTML).

## Реальный пример

```php
<?php namespace engine\articles\actions;

use frame\actions\fields\StringField;

// Действие условно определяется глаголом. В данном примере
// будем создавать действие ДобавитьСтатью.

// Создаем класс, наследуясь от ActionBody.
class AddArticle extends frame\actions\ActionBody
{
    // Определяем константы возможных ошибок.
    // Значения этих констант - уникальные целые числа.
    // Порядок не важен.
    const E_NO_TITLE = 1;
    const E_NO_TEXT = 2;
    const E_LONG_TITLE = 3;

    // Приватные данные, которые можем использовать
    // во время обработки.
    private $id;

    // Указываем массив требуемых POST данных и их типов.
    // Если какое-либо значение не будет передано, приложение
    // завершится ошибкой с кодом 404.
    public function listPost(): array
    {
        return [
            // Стандартные типы определены в frame\actions\fields.
            // В данном случае мы ожидаем строки. При этом,
            // строковые поля сразу кодируют спецсимволы HTML.
            'title' => StringField::class,
            'text' => StringField::class
        ];
    }

    // В инициализации проверяются GET значения.
    public function initialize(array $get)
    {
        // В данном примере не запрашиваем никаких GET данных,
        // но проверяем право на создание статьи. Если юзер
        // не может этого делать, Init автоматически завершит
        // выполнение ошибкой 403.
        Init::accessRight('articles', 'add');
    }

    // Валидируем пришедшие POST-данные и загружаемые файлы.
    // Никаких файлов не запрашивали, значит массив файлов
    // будет пуст.
    public function validate(array $post, array $files): array
    {
        $errors = [];
        $config = new JsonConfig('config/articles');
        /** @var StringField $title */ $title = $post['title'];
        /** @var StringField $text */ $text = $post['text'];

        if ($title->isEmpty()) $errors[] = static::E_NO_TITLE;
        else if ($title->isTooLong($config->{'title.maxLength'})) 
            $errors[] = static::E_LONG_TITLE;

        if ($text->isEmpty()) $errors[] = static::E_NO_TEXT;

        return $errors;
    }

    public function succeed(array $post, array $files)
    {
        $article = new Article;
        $article->title = $post['title']->get();
        $article->content = $post['text']->get();
        $article->author_id = user_me::get()->id;
        $article->date = time();
        $this->id = $article->insert();
    }

    public function getSuccessRedirect(): string
    {
        $prevRouter = prev_router::get();
        if ($prevRouter && $prevRouter->isInNamespace('admin'))
            return '/admin/article?id=' . $this->id;
        return '/article?id=' . $this->id;
    }
}
```