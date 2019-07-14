<?php namespace frame\actions;

use frame\Core;
use frame\LatePropsObject;
use frame\route\Router;
use frame\route\Request;
use frame\route\Response;
use frame\tools\transmitters\SessionTransmitter;
use frame\config\Json;
use frame\actions\UploadedFile;

use function lightlib\encode_specials;
use function lightlib\empty_recursive;
use frame\tools\Client;
use frame\errors\HttpError;
use frame\rules\ActionRules;

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
 * 
 * Замечание: аргументы (get-параметры) с именами, начинающимися со знака
 * подчеркивания (_) являются приватными, поэтому в пользовательских экшнах такие
 * использовать не стоит.
 */
abstract class Action extends LatePropsObject
{
    const NONE = 0;
    const SUCCESS = 1;
    const FAIL = -1;

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

    /**
     * @var Core Ссылка на экземпляр приложения для удобства
     */
    public $app;

    /**
     * @var int $status Статус: NONE, SUCCESS или FAIL.
     */
    public $status = self::NONE;

    /**
     * @var array Ошибки, возникшие во время валидации. OWN хранит коды ошибок после
     * validate(), остальные - массивы вида ['field' => ['rule1', 'rule2']] с именами
     * правил валидации данных соответствующего типа из конфига.
     */
    public $errors = [
        self::OWN => [], 
        self::ARGS => [], 
        self::POST => [],
        self::FILES => []
    ];

    /**
     * @var array [get => [name => value], post => [name => value]]
     */
    public $data = [
        self::ARGS => [], 
        self::POST => [], 
        self::FILES => []
    ];

    /**
     * @var array
     */
    private $config = null;

    /**
     * @var array [type => [field => [name => [value]]]]
     */
    private $interData = [];

    /** @var array Ассоциативный массив вида [string => callable] */
    private $ruleCallbacks = [];

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

    /**
     * @param array $get Параметры экшна.
     * @param string $noRuleMode Что делать, если для конфиг-валидации экшна в экшне
     * не установлен механизм обработки правила. Значения: 'error' (выбрасывает
     * исключение типа NoRuleError) или 'ignore' (пропускает правило).
     */
    public function __construct(array $args = [])
    {
        $this->app = Core::$app;
        $this->setDataAll(self::ARGS, $args);
        $this->load();
    }

    /**
     * @param string $type post|get|files. В случае files, value массива должно
     * быть UploadedFile типа.
     * @param array $data [name => value]
     */
    public function setDataAll($type, $data)
    {
        $safeValue = ($type === self::FILES ? $data : encode_specials($data));
        $this->data[$type] = $safeValue;
    }

    /**
     * @param string $type post|get.
     * @param string $name
     * @param string|UploadedFile|null $value Если передано null, считается что 
     * значения нет совсем.
     */
    public function setData($type, $name, $value)
    {
        $safeValue = ($type === self::FILES ? $value : encode_specials($value));
        $this->data[$type][$name] = $safeValue;
    }

    /**
     * Возвращает входящее значение, если оно есть, или значение по умолчанию, если 
     * его нет.
     * 
     * @param string $type post|get|files.
     * @param string $name
     * @return string|UploadedFile|null
     */
    public function getData($type, $name)
    {
        if (isset($this->data[$type][$name])) {
            $value = $this->data[$type][$name];
            if ($value !== '') return $value;
            return $this->getDataDefault($type, $name, true);
        }
        return $this->getDataDefault($type, $name, false);
    }

    /**
     * Возвращает установленное значение default поля в конфиге экшна.
     * Значение по умолчанию устанавливается в виде [значение1, значение2] или
     * [значение]. Значение 1 используется когда поле не было передано вообще,
     * значение 2 - когда поле было передано, но оно равно пустой строке. В последнем
     * случае будет использоваться одно значение на оба случая.
     * 
     * Если значение по умолчанию не установлено, вернет null при $existing = false,
     * или пустую строку при $existing = true.
     * 
     * @param string $type post|get|files.
     * @param string $name.
     * @param bool $existing Если false, возвращает значение, когда поле не было
     * передано совсем, а если true, то когда оно было передано, но равняется пустой
     * строке.
     * @return string|null
     */
    public function getDataDefault($type, $name, $existing = false)
    {
        if (isset($this->config[$type][$name]['default'])) {
            $defaultRule = $this->config[$type][$name]['default'];
            if (count($defaultRule) == 1) return $defaultRule[0];
        } else $defaultRule = [null, ''];

        if ($existing) return $defaultRule[1];
        else return $defaultRule[0];
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
        if (!isset($this->interData[$type][$field][$name])) return null;
        return $this->interData[$type][$field][$name];
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
     * @return string Триггерное url на выполнение экшна
     */
    public final function getUrl()
    {
        $get = array_merge($this->data[Action::ARGS], [
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
        $this->assertToken($this->data[self::ARGS][self::TOKEN] ?? '');
        $this->initialize();
        $this->errors[self::ARGS] = $this->ruleValidate(self::ARGS);
        $this->errors[self::POST] = $this->ruleValidate(self::POST);
        $this->errors[self::FILES] = $this->ruleValidate(self::FILES);
        $this->errors[self::OWN] = $this->validate();
        if (empty_recursive($this->errors)) {
            $this->succeed();
            $this->status = self::SUCCESS;
            $this->save();
            Response::setUrl(Router::toUrlOf($this->getSuccessRedirect()));
        } else {
            $this->fail();
            $this->status = self::FAIL;
            $this->save();
            Response::setUrl(Router::toUrlOf($this->getFailRedirect()));
        }
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return $this->status === self::SUCCESS;
    }

    /**
     * @return bool
     */
    public function isFail()
    {
        return $this->status === self::FAIL;
    }

    /**
     * Возвращает есть ли ошибка типа OWN (после validate()).
     * 
     * @param int $error Код ошибки.
     * @return bool
     */
    public function hasError($error)
    {
        return in_array($error, $this->errors[self::OWN]);
    }

    /**
     * Возвращает есть ли у заданного значения ошибка rule.
     * 
     * @param string $type get|post.
     * @param string $data Имя значения.
     * @param string|int $error Имя провалившегося rule правила.
     * @return bool
     */
    public function hasDataError($type, $data, $error)
    {
        return isset($this->errors[$type][$data])
            && in_array($error, $this->errors[$type][$data]);
    }

    /**
     * @param array|null $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @return array|null
     */
    public function getConfig()
    {
        return $this->config;
    }

    public function setRuleCallback(string $rule, callable $callback)
    {
        $this->ruleCallbacks[$rule] = $callback;
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
        return static::class . '_' . ($this->data[self::ARGS][self::ID] ?? '');
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
        $sessions->setData($idName . '_status', $this->status);
        if ($this->isFail()) {
            $sessions->setData($idName . '_errors', serialize($this->errors));
            $sessions->setData($idName . '_data', serialize($this->data));
        }
    }

    /**
     * Загружает свое сохраненное состояние после редиректа.
     */
    private function load()
    {
        $idName = $this->getIdName();
        $sessions = new SessionTransmitter;
        if ($sessions->isSetData($idName . '_status')) {
            $this->status = $sessions->getData($idName . '_status');
            $sessions->removeData($idName . '_status');
            if ($sessions->isSetData($idName . '_errors')) {
                $this->errors = unserialize($sessions->getData($idName . '_errors'));
                $sessions->removeData($idName . '_errors');
            }
            if ($sessions->isSetData($idName . '_data')) {
                $this->data = unserialize($sessions->getData($idName . '_data'));
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

    /**
     * Возвращает массив вида ['field' => ['rule1', 'rule2']] с именами
     * правил валидации данных соответствующего типа из конфига.
     * @see ActionRules
     */
    private function ruleValidate(string $type)
    {
        $rules = new ActionRules($this->data[$type], $this->config[$type] ?? []);
        $rules->setRuleCallbacks($this->ruleCallbacks);
        $rules->validate();
        $this->errors[$type] = $rules->getErrors();
        $this->interData[$type] = $rules->getInterDataArray();
    }
}