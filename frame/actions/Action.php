<?php namespace frame\actions;

use frame\route\Router;
use frame\route\Request;
use frame\route\Response;
use frame\tools\transmitters\SessionTransmitter;
use frame\actions\UploadedFile;

use function lightlib\encode_specials;
use frame\tools\Client;
use frame\errors\HttpError;
use frame\LatePropsObject;

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
abstract class Action extends LatePropsObject
{
    /**
     * Type of Action data.
     */
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
    const TOKEN = 'csrf';

    /** @var array Ошибки после validate(). */
    private $errors = [];

    private $executed = false;

    private $data = [
        self::ARGS => [],
        self::POST => [],
        self::FILES => []
    ];

    public static function fromTriggerUrl(string $url): Action
    {
        $router = new Router($url);
        $type = $router->pagename;
        $class = '\\' . str_replace('/', '\\', $type);

        $action = new $class($router->args);
        $action->setDataAll(Action::POST, $_POST);
        $action->setDataAll(Action::FILES, array_map(function ($filedata) {
            return new UploadedFile($filedata);
        }, $_FILES));

        return $action;
    }

    public function __construct(array $args = [])
    {
        $this->setDataAll(self::ARGS, $args);
        $this->load();
    }

    /**
     * @param string $type post|get|files. В случае files, value массива должно
     * быть UploadedFile типа.
     * @param array $data [name => value]
     */
    public function setDataAll(string $type, array $data)
    {
        $safeValue = ($type === self::FILES ? $data : encode_specials($data));
        $this->data[$type] = $safeValue;
    }

    /**
     * @param string $type post|get.
     * @param string|UploadedFile|null $value Если передано null, считается что 
     * значения нет совсем.
     */
    public function setData(string $type, string $name, $value)
    {
        $safeValue = ($type === self::FILES ? $value : encode_specials($value));
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
     * Триггерное url на выполнение экшна
     */
    public final function getUrl(): string
    {
        $get = array_merge([
            self::ID => '',
            self::TOKEN => $this->getExpectedToken(),
        ], $this->data[Action::ARGS]);
        return Router::toUrlOf('/' . str_replace('\\', '/', static::class), $get);
    }

    /**
     * После данного метода скрипт завершает свое выполнение.
     * Кодирует спецсимволы полученных POST данных.
     */
    public final function exec()
    {
        $this->assertToken($this->data[self::ARGS][self::TOKEN] ?? '');
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
     * Выполняется перед сохранением состояния экшна. Оно сохраняется при 
     * успехе/неудаче, только если соответсвующие редиректы не null. Тут можно 
     * очистить данные, которые не нужно сохранять (например, пароли).
     */
    protected function doBeforeSave() 
    { 
        // Here is nothing to do 
    }

    /**
     * Выполняется после загрузки состояния экшна. Оно загружается, только если было 
     * сохранено. Оно сохраняется при успехе/неудаче, только если соответсвующие 
     * редиректы не null.
     */
    protected function doAfterLoad()
    {
        // Here is nothing to do
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
     * Сохраняются статус, ошибки и введенные данные.
     * Файлы не сохраняются.
     */
    private function save()
    {
        if ($this->hasErrors()) {
            $this->doBeforeSave();
            $idName = $this->getIdName();
            $sessions = new SessionTransmitter;
            $sessions->setData($idName, serialize([
                $this->executed,
                $this->data,
                $this->errors
            ]));
        }
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
                $this->data, 
                $this->errors
            ) = unserialize($sessions->getData($idName));
            $sessions->removeData($idName);
            $this->doAfterLoad();
        }
    }

    private function assertToken(string $token): void
    {
        if ($token != $this->getExpectedToken()) 
            throw new HttpError(HttpError::BAD_REQUEST,
                'Recieved TOKEN token does not match expected token.');
    }
}