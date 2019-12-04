<?php namespace frame\actions;

use frame\actions\UploadedFile;
use frame\errors\HttpError;
use frame\tools\Client;

use function lightlib\encode_specials;

/**
 * Класс служит для обработки форм, но можно использовать для запуска
 * определенных процессов/скриптов по ссылке.
 * 
 * Чтобы создать новый экшн, нужно наследоваться от данного класса и реализовать
 * все абстрактные методы этого класса.
 * 
 * Создание экземпляра класса делается с помощью статического метода instance().
 * Это нужно, чтобы экшн, который выполнялся в коде до кода страницы, не создавался
 * заново, а использовался тот же сохраненный в Core объект.
 * 
 * Действия после завершения выполнения экшна контролируются через методы вида
 * get*Redirect. В них указывается либо адрес, куда нужно перейти (на какую
 * страницу), либо null, если не нужно никуда переходить.
 * 
 * Чтобы использовать экшн, нужно добавить нужный экземпляр на страницу,
 * а в атрибут action форм или просто в url ссылок указать значение,
 * возвращаемое методом getUrl().
 * 
 * Ошибки, возникшие во время обработки, определены константами
 * вида E_NAME_OF_ERROR.
 * 
 * Корректная работа checkbox:
 * <input type="hidden" name="property" value="0">
 * <input type="checkbox" name="property" value="1">
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

    /**
     * @deprecated Use setToken() to set token and getToken() to get it.
     */
    const TOKEN = '_csrf';

    private $body;

    /** @var array Ошибки после validate(). */
    private $errors = [];

    private $data = [
        self::ARGS => [],
        self::POST => [],
        self::FILES => []
    ];

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

    public function setToken(string $token)
    {
        $this->setData('get', self::TOKEN, $token);
    }

    public function getToken(): ?string
    {
        return $this->getData('get', self::TOKEN);
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
        if ($type === self::ARGS && isset($this->listGet()[$name]))
            settype($safeValue, $this->listGet()[$name][0]);
        else if ($type === self::POST && isset($this->listPost()[$name])) {
            $type = $this->listPost()[$name][0];
            if ($type === self::POST_PASSWORD) $type = self::POST_TEXT;
            settype($safeValue, $type);
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
        $this->assertToken($this->data[self::ARGS][self::TOKEN] ?? '');
        $this->validateGet($this->data[self::ARGS]);
        $this->validatePost($this->data[self::POST]);
        $this->body->initialize($this->data[self::ARGS]);
        $this->errors = $this->body->validate(
            $this->data[self::POST],
            $this->data[self::FILES]
        );
        if (!$this->hasErrors()) {
            $this->body->succeed(
                $this->data[self::POST],
                $this->data[self::FILES]
            );
        } else {
            $this->body->fail(
                $this->data[self::POST],
                $this->data[self::FILES]
            );
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

    public function getExpectedToken(): string
    {
        return md5('tkn_salt' . Client::getId());
    }

    private function assertToken(string $token): void
    {
        if ($token != $this->getExpectedToken()) 
            throw new HttpError(HttpError::BAD_REQUEST,
                'Recieved TOKEN token does not match expected token.');
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
                "Get field '$field' is not set."
            );
        }
    }
}