<?php namespace frame\actions;

use frame\Core;
use frame\route\Router;
use frame\route\Request;
use frame\route\Response;
use frame\tools\transmitters\SessionTransmitter;
use frame\config\Json;
use frame\actions\UploadedFile;

use function lightlib\encode_specials;
use frame\tools\Client;
use frame\errors\HttpError;
use frame\rules\ActionRules;
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
 * 
 * Очень хорошей практикой будет активное использование механизма LatePropsObject
 * в дочерних экшнах. С помощью него можно определять используемые в экшне данные,
 * которые потом можно брать из него на обычных страницах, вместо того, чтобы повторно
 * создавать их.
 */
abstract class Action extends LatePropsObject
{
    /**
     * Type of Action data.
     */
    const OWN = 'own';
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

    const VALIDATION_CONFIG_FOLDER = 'public/actions';

    /** @var Core Ссылка на экземпляр приложения для удобства */
    public $app;

    /** @var array Ошибки типа OWN, возникшие после validate(). */
    public $errors = [];

    private $executed = false;

    /** @var array [string => ActionRules] */
    private $rules = [
        self::ARGS => null,
        self::POST => null,
        self::FILES => null
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

        $configFile = self::VALIDATION_CONFIG_FOLDER . '/' . $type . '.json';
        if (file_exists($configFile)) {
            $config = new Json($configFile);
            $action->setConfig($config->getData());
        }

        return $action;
    }

    public function __construct(array $args = [])
    {
        $this->app = Core::$app;
        $this->rules[self::ARGS] = new ActionRules;
        $this->rules[self::POST] = new ActionRules;
        $this->rules[self::FILES] = new ActionRules;
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
        $this->rules[$type]->setValues($safeValue);
    }

    /**
     * @param string $type post|get.
     * @param string|UploadedFile|null $value Если передано null, считается что 
     * значения нет совсем.
     */
    public function setData(string $type, string $name, $value)
    {
        $safeValue = ($type === self::FILES ? $value : encode_specials($value));
        $this->rules[$type]->setValue($name, $safeValue);
    }

    /**
     * Возвращает входящее значение, если оно есть, или значение по умолчанию, если 
     * его нет.
     * 
     * @param string $type post|get|files.
     * @return string|UploadedFile|null
     * @see Rules::getValue()
     */
    public function getData(string $type, string $name)
    {
        return $this->rules[$type]->getValue($name);
    }

    /**
     * @see Rules::getDefault()
     */
    public function getDataDefault(string $type,
        string $name, bool $existing = false)
    {
        return $this->rules[$type]->getDefault($name, $existing);
    }

    public function getDataArray()
    {
        $result = [];
        foreach ($this->rules as $type => $rules)
            $result[$type] = $rules->getValues();
        return $result;
    }

    /**
     * Если заданного значения нет, вернет null.
     * 
     * @param string $field Поле, по которому это значение связано (генерируется
     * в цепочке правил поля).
     * @return mixed|null
     */
    public function getInterData(string $type, string $field, string $name)
    {
        return $this->rules[$type]->getInterData($field, $name);
    }

    /**
     * @param string $field Поле, по которому значение связано (генерируется
     * в цепочке правил поля).
     * @return mixed
     * @throws \Exception Если заданного значения нет (или оно null).
     */
    public function requireInterData(string $type, string $field, string $name)
    {
        $data = $this->getInterData($type, $field, $name);
        if ($data === null) throw new \Exception('There is no "' . $type .
            '" inter data "' . $name . '" from "' . $field . '"');
        return $data;
    }

    /**
     * Триггерное url на выполнение экшна
     */
    public final function getUrl(): string
    {
        $get = array_merge($this->rules[Action::ARGS]->getValues(), [
            self::TOKEN => $this->getExpectedToken(),
        ]);
        return Router::toUrlOf('/' . str_replace('\\', '/', static::class), $get);
    }

    /**
     * После данного метода скрипт завершает свое выполнение.
     * Кодирует спецсимволы полученных POST данных.
     */
    public final function exec()
    {
        $this->assertToken($this->rules[self::ARGS]->getValue(self::TOKEN) ?? '');
        $this->initialize();
        $this->rules[self::ARGS]->validate();
        $this->rules[self::POST]->validate();
        $this->rules[self::FILES]->validate();
        $this->errors = $this->validate();
        $this->executed = true;
        if ($this->hasErrors()) {
            $this->succeed();
            $this->save();
            Response::setUrl(Router::toUrlOf($this->getSuccessRedirect()));
        } else {
            $this->fail();
            $this->save();
            Response::setUrl(Router::toUrlOf($this->getFailRedirect()));
        }
    }

    public function isExecuted(): bool
    {
        return $this->executed;
    }

    /**
     * Возвращает есть ли ошибка типа OWN (после валидации).
     * @param int $error Код ошибки.
     */
    public function hasError(int $error): bool
    {
        return in_array($error, $this->errors[self::OWN]);
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors) 
            || $this->rules[self::ARGS] && $this->rules[self::ARGS]->hasErrors()
            || $this->rules[self::POST] && $this->rules[self::POST]->hasErrors()
            || $this->rules[self::FILES] && $this->rules[self::FILES]->hasErrors();
    }

    /**
     * Возвращает есть ли у заданного значения ошибка rule.
     * @param string|int $error Имя провалившегося rule правила.
     */
    public function hasDataError(string $type, string $data, $error): bool
    {
        return $this->rules[$type] && $this->rules[$type]->hasError($data, $error);
    }

    public function setConfig(?array $config)
    {
        foreach ($this->rules as $type => $rules)
            $rules->setRules($config[$type] ?? []);
    }

    public function getConfig(): array
    {
        $result = [];
        foreach ($this->rules as $type => $rules)
            $result[$type] = $rules->getRules();
        return $result;
    }

    public function setRuleCallback(string $rule, callable $callback)
    {
        foreach ($this->rules as $rules)
            $rules->setRuleCallback($rule, $callback);
        
    }

    public function getExpectedToken(): string
    {
        return md5('tkn_salt' . Client::getId());
    }

    /**
     * Is run first
     * Suggests override if it is needed
     */
    protected function initialize()
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
    protected function validate()
    {
        return []; // Here is nothing to validate
    }

    /**
     * Is run third in case of the success
     */
    abstract protected function succeed();

    /**
     * Is run third in case of the fail
     */
    protected function fail()
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
     */
    protected function getSuccessRedirect(): string
    {
        if (Request::hasReferer()) return Request::getReferer();
        else return '/';
    }

    /**
     * Возвращает адрес веб-страницы, на которую нужно перейти после неудачного
     * (с ошибками во время валидации данных) завершения экшна.
     */
    protected function getFailRedirect(): string
    {
        if (Request::hasReferer()) return Request::getReferer();
        else return '/';
    }

    private function getIdName(): string
    {
        return static::class . '_' . 
            ($this->rules[self::ARGS]->getValue(self::ID) ?? '');
    }

    /**
     * Сохраняет свое состояние перед редиректом.
     * Сохраняются статус, ошибки и введенные данные.
     * Файлы не сохраняются.
     */
    private function save()
    {
        $this->doBeforeSave();
        $idName = $this->getIdName();
        $sessions = new SessionTransmitter;
        $sessions->setData($idName, 1);
        if ($this->hasErrors()) {
            $sessions->setData($idName . '_errors', serialize($this->errors));
            $sessions->setData($idName . '_data', serialize($this->getDataArray()));
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
            $this->executed = true;
            $sessions->removeData($idName . '_status');
            if ($sessions->isSetData($idName . '_errors')) {
                $this->errors = unserialize($sessions->getData($idName . '_errors'));
                $sessions->removeData($idName . '_errors');
            }
            if ($sessions->isSetData($idName . '_data')) {
                $data = unserialize($sessions->getData($idName . '_data'));
                foreach ($this->rules as $type => $rules)
                    $rules->setValues($data[$type] ?? []);
                $sessions->removeData($idName . '_data');
            }
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