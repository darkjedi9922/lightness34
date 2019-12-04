<?php namespace frame\actions;

use frame\route\Router;
use frame\route\Request;
use frame\route\Response;
use frame\tools\transmitters\SessionTransmitter;
use frame\actions\UploadedFile;

use function lightlib\encode_specials;
use frame\tools\Client;
use frame\errors\HttpError;

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
abstract class Action
{
    /** Type of Action data. */
    const ARGS = 'get';
    const POST = 'post';
    const FILES = 'files';

    /** Type of a GET field. */
    const GET_INT = 'int';
    const GET_STRING = 'string';

    /** Type of a POST field. */
    const POST_INT = 'int';
    const POST_TEXT = 'string';

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

    /** @var array Ошибки после validate(). */
    private $errors = [];

    private $executed = false;

    private $data = [
        self::ARGS => [],
        self::POST => [],
        self::FILES => []
    ];

    public function __construct(array $args = [])
    {
        $this->setDataAll(self::ARGS, $args);
        $this->load();
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
        else if ($type === self::POST && isset($this->listPost()[$name]))
            settype($safeValue, $this->listPost()[$name][0]);
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
        $this->initialize($this->data[self::ARGS]);
        $this->errors = $this->validate(
            $this->data[self::POST],
            $this->data[self::FILES]
        );
        $redirect = null;
        if (!$this->hasErrors()) {
            $this->succeed(
                $this->data[self::POST],
                $this->data[self::FILES]
            );
            $this->save();
            $redirect = $this->getSuccessRedirect();
        } else {
            $this->fail(
                $this->data[self::POST],
                $this->data[self::FILES]
            );
            $redirect = $this->getFailRedirect();
        }
        $this->executed = true;
        if ($redirect !== null) {
            $this->save();
            Response::setUrl(Router::toUrlOf($redirect));
        }
    }

    public function isExecuted(): bool
    {
        return $this->executed;
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

    /**
     * Declares the list of get parameters that Action required. If a parameter
     * listed in this method is not set when executing an action, then an error
     * HttpError:NOT_FOUND raised.
     * 
     * Returns an array of the form ['param_name' => [GET_TYPE, 'description']]
     * The GET_TYPE is Action constants declaring the type of a parameter such as
     * GET_INT, GET_TEXT etc.
     */
    public function listGet(): array
    {
        return [];
    }

    /**
     * The same as listGet() but for the post data with POST_TYPE field types.
     */
    public function listPost(): array
    {
        return [];
    }

    /**
     * Is run first
     * Suggests override if it is needed
     */
    protected function initialize(array $get)
    {
        // Here is nothing to initialize
    }

    /**
     * Is run second
     * Returns array of error codes
     * Suggests override if it is needed
     * 
     * @return array Коды ошибок
     */
    protected function validate(array $post, array $files)
    {
        return []; // Here is nothing to validate
    }

    /**
     * Is run third in case of the success
     */
    abstract protected function succeed(array $post, array $files);

    /**
     * Is run third in case of the fail
     */
    protected function fail(array $post, array $files)
    {
        // Here is nothing to do
    }

    /**
     * Определяет названия переданных post данных, которые нужно временно сохранять.
     * Используется, чтобы вывести введенные данные в форме после возвращения на 
     * страницу, например.
     * 
     * Не рекомендуется сохранять пароли и другие секретные данные.
     */
    protected function getPostToSave(): array 
    { 
        return []; // Here is nothing to save
    }

    /**
     * Возвращает адрес веб-страницы, на которую нужно перейти после успешного
     * (без ошибок во время валидации данных) завершения экшна.
     * 
     * Если вернет null, редиректа не будет.
     */
    protected function getSuccessRedirect(): ?string
    {
        if (Request::hasReferer()) return Request::getReferer();
        else return '/';
    }

    /**
     * Возвращает адрес веб-страницы, на которую нужно перейти после неудачного
     * (с ошибками во время валидации данных) завершения экшна.
     * 
     * Если вернет null, редиректа не будет.
     */
    protected function getFailRedirect(): ?string
    {
        if (Request::hasReferer()) return Request::getReferer();
        else return '/';
    }

    private function getIdName(): string
    {
        return static::class . '_' . $this->getId();
    }

    /**
     * Сохраняет свое состояние перед редиректом.
     * Сохраняются статус, ошибки и введенные post данные.
     * Файлы не сохраняются.
     */
    private function save()
    {
        $idName = $this->getIdName();
        $sessions = new SessionTransmitter;
        $sessions->setData($idName, serialize([
            $this->executed,
            $this->assemblePostToSave(),
            $this->errors
        ]));
    }

    /**
     * Загружает свое сохраненное состояние после редиректа.
     */
    private function load()
    {
        $idName = $this->getIdName();
        $sessions = new SessionTransmitter;
        if ($sessions->isSetData($idName)) {
            list(
                $this->executed,
                $this->data['post'], 
                $this->errors
            ) = unserialize($sessions->getData($idName));
            $sessions->removeData($idName);
        }
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
        $list = $this->listGet();
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
        $list = $this->listPost();
        foreach ($list as $field => $desc) {
            if (!isset($post[$field])) throw new HttpError(
                HttpError::NOT_FOUND,
                "Get field '$field' is not set."
            );
        }
    }

    private function assemblePostToSave(): array
    {
        $result = [];
        foreach ($this->getPostToSave() as $name) {
            if (isset($this->data['post'][$name]))
                $result[$name] = $this->data['post'][$name];
        }
        return $result;
    }
}