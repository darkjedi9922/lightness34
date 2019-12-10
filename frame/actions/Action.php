<?php namespace frame\actions;

use frame\actions\UploadedFile;
use frame\errors\HttpError;
use frame\tools\Client;

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
        array $errors
    ): Action {
        $action = new Action($body);
        $action->data[self::POST] = $post;
        $action->errors = $errors;
        return $action;
    }

    public function __construct(ActionBody $body, array $args = [])
    {
        $this->body = $body;
        $this->setDataAll(self::ARGS, $args);
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
        foreach ($data as $key => $value) $this->setData($type, $key, $value);
    }

    /**
     * @param string $type post|get.
     * @param string|UploadedFile|null $value Если передано null, считается что 
     * значения нет совсем.
     */
    public function setData(string $type, string $name, $value)
    {
        $safeValue = ($type === self::FILES ? $value : encode_specials($value));
        if ($type === self::ARGS && isset($this->body->listGet()[$name]))
            settype($safeValue, $this->body->listGet()[$name][0]);
        else if ($type === self::POST && isset($this->body->listPost()[$name])) {
            $postType = $this->body->listPost()[$name][0];
            if ($postType === ActionBody::POST_PASSWORD) 
                $postType = ActionBody::POST_TEXT;
            settype($safeValue, $postType);
        }
        $this->data[$type][$name] = $safeValue;
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
        return $this->data[$type][$name] ?? $default;
    }

    public function getDataArray(): array
    {
        return $this->data;
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
        $this->validateGet($this->data[self::ARGS]);
        $this->validatePost($this->data[self::POST]);
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
    private function validateGet(array $get)
    {
        $list = $this->body->listGet();
        foreach ($list as $field => $desc) {
            if (!isset($get[$field])) throw new HttpError(
                HttpError::NOT_FOUND,
                "Get field '$field' is not set." 
            );
        }
    }

    /**
     * @throws HttpError NOT_FOUND
     */
    private function validatePost(array $post)
    {
        $list = $this->body->listPost();
        foreach ($list as $field => $desc) {
            if (!isset($post[$field])) throw new HttpError(
                HttpError::NOT_FOUND,
                "Post field '$field' is not set."
            );
        }
    }
}