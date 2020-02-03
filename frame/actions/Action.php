<?php namespace frame\actions;

use frame\actions\fields\BaseField;
use frame\actions\fields\BooleanField;
use frame\actions\UploadedFile;
use frame\errors\HttpError;
use frame\core\Core;

use function lightlib\encode_specials;

/**
 * Класс служит для обработки форм, но можно использовать для запуска других
 * определенных процессов/скриптов.
 * 
 * Чтобы создать новый экшн, нужно наследоваться от ActionBody реализовать все
 * абстрактные методы этого класса и, если нужно, переопределить другие его методы.
 */
class Action
{
    const EVENT_START = 'action-exec-start';
    const EVENT_END = 'action-exec-end';

    /** Type of Action data. */
    const ARGS = 'get';
    const POST = 'post';
    const FILES = 'files';

    /** 
     * Имена GET-параметров, используемых для работы самого экшна.
     * Задавать в параметрах можно только ID.
     * 
     * ID нужен, если на одной странице используется несколько экшнов одного типа 
     * с разными параметрами, чтобы понимать какой из них выполнять.
     */
    const ID = 'action';

    private $body;

    /** @var array Ошибки после validate(). */
    private $errors = [];

    private $data = [
        self::ARGS => [],
        self::POST => [],
        self::FILES => []
    ];

    private $result = [];

    public static function fromState(
        ActionBody $body, 
        array $post,
        array $errors,
        array $result
    ): Action {
        $action = new Action($body);
        $action->data[self::POST] = $post;
        $action->errors = $errors;
        $action->result = $result;
        return $action;
    }

    public function __construct(ActionBody $body, array $args = [])
    {
        $this->body = $body;
        $this->setDataAll(self::ARGS, $args);
        $this->setDataAll(self::POST, []);
    }

    public function getBody(): ActionBody
    {
        return $this->body;
    }

    /**
     * @param string $type post|get|files. В случае files, value массива должно
     * быть UploadedFile типа.
     * @param array $data [name => value]
     */
    public function setDataAll(string $type, array $data)
    {
        $this->data[$type] = [];
        foreach ($data as $field => $value) 
            $this->setData($type, $field, $value);
    }

    /**
     * @param string $type post|get.
     * @param string|UploadedFile|null $value Если передано null, считается что 
     * значения нет совсем.
     */
    public function setData(string $type, string $name, $value)
    {
        switch ($type) {
            case self::ARGS: 
                $fieldType = $this->body->listGet()[$name] ?? null;
                break;
            case self::POST:
                $fieldType = $this->body->listPost()[$name] ?? null;
                break;
            case self::FILES:
                $fieldType = $this->body->listFiles()[$name] ?? null;
                break;
            default: $fieldType = null;
        }

        if ($fieldType !== null && $value !== null) {
            $value = new $fieldType($value);
        }

        $this->data[$type][$name] = $value;
    }

    /**
     * Возвращает входящее значение, если оно есть, или значение по умолчанию, если 
     * его нет.
     * 
     * @param string $type post|get|files
     * @param string|UploadedFile|null $default
     * @return string|UploadedFile|null
     */
    public function getData(string $type, string $name, $default = null)
    {
        $value = $this->data[$type][$name] ?? null;
        if ($value === null) return $default;
        if ($value instanceof BaseField) return $value->get();
        return $value;
    }

    public function getDataArray(bool $unpack = false): array
    {
        if (!$unpack) return $this->data;
        
        $result = [];
        foreach ($this->data as $type => $typedData) {
            if (!isset($result[$type])) $result[$type] = [];
            foreach ($typedData as $field => $value)
                $result[$type][$field] = $value instanceof BaseField
                    ? $value->get()
                    : $value;
        }
        return $result;
    }

    public final function getId(): string
    {
        return $this->data[self::ARGS][self::ID] ?? '';
    }

    /**
     * После данного метода скрипт завершает свое выполнение.
     * Кодирует спецсимволы полученных POST данных.
     */
    public final function exec()
    {
        Core::$app->emit(self::EVENT_START, $this);
        $this->prepareData(self::ARGS, $this->body->listGet());
        $this->prepareData(self::POST, $this->body->listPost());
        $this->body->initialize($this->data[self::ARGS]);
        $this->errors = $this->body->validate(
            $this->data[self::POST],
            $this->data[self::FILES]
        );
        if (!$this->hasErrors()) {
            $this->result = $this->body->succeed(
                $this->data[self::POST],
                $this->data[self::FILES]
            ) ?? [];
        } else {
            $this->result = $this->body->fail(
                $this->data[self::POST],
                $this->data[self::FILES]
            ) ?? [];
        }
        Core::$app->emit(self::EVENT_END, $this);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Возвращает есть ли ошибка после валидации validate().
     * @param int $error Код ошибки.
     */
    public function hasError(int $error): bool
    {
        return in_array($error, $this->errors);
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public function getResult(): array
    {
        return $this->result;
    }

    /**
     * @throws HttpError NOT_FOUND
     */
    private function prepareData(string $type, array $desc)
    {
        foreach ($desc as $field => $fieldType) {
            $value = $this->data[$type][$field] ?? null;
            if ($value === null) {
                $default = $fieldType::createDefault();
                if ($default !== null) $this->data[$type][$field] = $default;
                else throw new HttpError(
                    HttpError::NOT_FOUND,
                    "$type field '$field' is not set" 
                );
            }
        }
    }
}