<?php namespace frame\actions;

use function lightlib\encode_specials;

use frame\Core;
use frame\LatePropsObject;
use frame\route\Router;
use frame\route\Request;
use frame\route\Response;
use frame\tools\transmitters\SessionTransmitter;
use frame\tools\Json;
use frame\actions\RuleResult;
use frame\actions\NoRuleError;
use frame\actions\RuleCheckFailedException;
use frame\errors\NotImplementedException;
use function lightlib\empty_recursive;

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
 * @todo Выделить методы, которые нужно переопределять в отдельный класс (например,
 * ActionBody) ибо методов уже слишком много, трудно ориентироваться.
 */
abstract class Action extends LatePropsObject
{
    const NONE = 0;
    const SUCCESS = 1;
    const FAIL = -1;

    const NO_RULE_ERROR = 'error';
    const NO_RULE_IGNORE = 'ignore';

    const OWN = 'own';
    const DATA_GET = 'get';
    const DATA_POST = 'post';

    /**
     * @var Action|null Текущий активированный экшн.
     * Определяется при срабатывании ActionMacro.
     * Используется в служебных целях фреймворка.
     */
    public static $_current = null;

    /**
     * @var Core Ссылка на экземпляр приложения для удобства
     */
    public $app;

    /**
     * @var string $name Имя экшна. Складывается из id экшна и имени класса
     */
    public $name = '';

    /**
     * @var int $status Статус: NONE, SUCCESS или FAIL.
     */
    public $status = self::NONE;

    /**
     * @var array Ошибки, возникшие во время валидации. OWN хранит коды ошибок после
     * validate(), остальные - массивы вида ['field' => ['rule1', 'rule2']] с именами
     * правил валидации данных соответствующего типа из конфига.
     */
    public $errors = [self::OWN => [], self::DATA_GET => [], self::DATA_POST => []];

    /**
     * @var array [get => [name => value], post => [name => value]]
     */
    public $data = [self::DATA_GET => [], self::DATA_POST => []];

    /**
     * @var Json.
     */
    private $config = null;

    /**
     * @var array Ассоциативный массив вида [string => callable]
     */
    private $ruleCallbacks = [];

    /**
     * @var string
     */
    private $noRuleMode = self::NO_RULE_ERROR;

    /**
     * @var array [type => [field => [name => [value]]]]
     */
    private $interData = [];

    /**
     * @param array $get Параметры экшна
     * @param int $id Id экшна. Нужен, если на одной странице используется несколько экшнов
     * одного класса с разными параметрами, чтобы понимать какой из них выполнять
     * @return static
     */
    public static function instance($get = [], $id = '')
    {
        if (isset(static::$_current) && static::$_current->name === $id . '_' . static::class) 
            return static::$_current;
        
        $noRuleMode = Core::$app->config->{'actions.noRuleMode'};
        $action = new static($get, $id, $noRuleMode);
        return $action;
    }

    /**
     * Warning: Создание объекта непосредственно через конструктор создает отдельный
     * независимый экземпляр экшна, независимо от состояния всего приложения. 
     * Это больше подходит для тестирования. Для получения экземпляра экшна при
     * работе приложения (с инициализированным Core) требуется использовать
     * статический метод instance().
     * 
     * @param array $get Параметры экшна.
     * @param int $id Id экшна. Нужен, если на одной странице используется несколько 
     * экшнов одного класса с разными параметрами, чтобы понимать какой из них 
     * выполнять.
     * @param string $noRuleMode Что делать, если для конфиг-валидации экшна в экшне
     * не установлен механизм обработки правила. Значения: 'error' (выбрасывает
     * исключение типа NoRuleError) или 'ignore' (пропускает правило).
     */
    public function __construct(
        $get = [],
        $id = '',
        $noRuleMode = self::NO_RULE_ERROR)
    {
        $this->app = Core::$app;
        $this->name = $id . '_' . static::class;
        $this->noRuleMode = $noRuleMode;
        $this->setDataAll(self::DATA_GET, $get);
        $this->load();
    }

    /**
     * @param string $type post|get.
     * @param array $data [name => value]
     */
    public function setDataAll($type, $data)
    {
        $this->data[$type] = encode_specials($data);
    }

    /**
     * @param string $type post|get.
     * @param string $name
     * @param string|null $value Если передано null, считается что значения нет 
     * совсем.
     */
    public function setData($type, $name, $value)
    {
        $this->data[$type][$name] = encode_specials($value);
    }

    /**
     * Возвращает входящее значение, если оно есть, или значение по умолчанию, если 
     * его нет.
     * 
     * @param string $type post|get.
     * @param string $name
     * @return mixed
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
     * @param string $type post|get.
     * @param string $name.
     * @param bool $existing Если false, возвращает значение, когда поле не было
     * передано совсем, а если true, то когда оно было передано, но равняется пустой
     * строке.
     * @return mixed
     */
    public function getDataDefault($type, $name, $existing = false)
    {
        if ($this->config
            && isset($this->config->$type[$name]['default'])) {
            $defaultRule = $this->config->$type[$name]['default'];
            if (count($defaultRule) == 1) return $defaultRule[0];
        } else $defaultRule = [null, ''];

        if ($existing) return $defaultRule[1];
        else return $defaultRule[0];
    }

    /**
     * Если заданной данной нет, вернет null.
     * 
     * @param string $field Поле, по которому эта данная связана (генерируется
     * в цепочке правил поля).
     * @return mixed|null
     */
    public function getInterData(string $type, string $field, string $name)
    {
        if (!isset($this->interData[$type])) return null;
        if (!isset($this->interData[$type][$field])) return null;
        if (!isset($this->interData[$type][$field][$name])) return null;
        return $this->interData[$type][$field][$name];
    }

    /**
     * @param string $field Поле, по которому эта данная связана (генерируется
     * в цепочке правил поля).
     * @return mixed
     * @throws \Exception Если заданной данной нет (или она null).
     */
    public function requireInterData(string $type, string $field, string $name)
    {
        $data = $this->getInterData($type, $field, $name);
        if ($data === null) throw new \Exception('There is no "' . $type .
            '" inter data "' . $name . '" from "' . $field . '"');
        return $data;
    }

    /**
     * @param Router $router Устройство заданного роутера будет использовано для 
     * построения url.
     * @return string Триггерное url на выполнение экшна
     * 
     * @todo Чтобы убрать зависимость экшна от Router (в целом-то он от него не
     * зависит, можно вынести этот метод в какой-нибудь ActionSetup или метод в 
     * view).
     */
    public final function getUrl($router)
    {
        $queryData = array_merge([$this->name], $this->data[Action::DATA_GET]);
        return $router->toUrl(['action' => http_build_query($queryData, '', ';')]);
    }

    /**
     * После данного метода скрипт завершает свое выполнение.
     * Кодирует спецсимволы полученных POST данных.
     */
    public final function exec()
    {
        $this->initialization();
        $this->errors[self::DATA_GET] = $this->ruleValidate(self::DATA_GET);
        $this->errors[self::DATA_POST] = $this->ruleValidate(self::DATA_POST);
        $this->errors[self::OWN] = $this->validate($this->data);
        if (empty_recursive($this->errors)) {
            $this->successBody($this->data);
            $this->status = self::SUCCESS;
            if ($this->getSuccessRedirect() !== null) {
                $this->save();
                Response::setUrl(Router::toUrlOf($this->getSuccessRedirect()));
            }
        } else {
            $this->failBody($this->data);
            $this->status = self::FAIL;
            if ($this->getFailRedirect() !== null) {
                $this->save();
                Response::setUrl(Router::toUrlOf($this->getFailRedirect()));
            }
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
     * @param Json|null $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @return Json|null
     */
    public function getConfig()
    {
        return $this->config ? $this->config : null;
    }

    /**
     * Устанавливает callback-функцию, которая будет вызываться при проверке
     * поля, заданной в json-настройках валидации.
     * 
     * Callback-функция вида (mixed $rule, $mixed $value): bool,
     * где $rule - значение правила, $value - проверяемое значение. Если проверяемого
     * значения изначально нет, будет передано null. Callback возвращает true, если
     * проверка пройдена, иначе false.
     * 
     * Если проверка не пройдена, в post errors добавится имя ошибки, равное $name.
     * Но вместо этого может быть выброшено исключение.
     * @see RuleCheckFailedException.
     * 
     * При этом callback может выбросить исключение StopRuleException с результатом
     * проверки.
     * @see StopRuleException.
     * 
     * @param string $name Имя проверки
     * @param callable $callback
     */
    public function setRule($name, $callback)
    {
        $this->ruleCallbacks[$name] = $callback;
    }

    /**
     * @return callable|null
     * @throws NoRuleError Если обработчик правила не установлен при условии, если 
     * флаг noRuleMode для экшна задан как error
     */
    public function getRuleCallback(string $rule)
    {
        if (!isset($this->ruleCallbacks[$rule])) {
            if ($this->noRuleMode == self::NO_RULE_ERROR) throw new NoRuleError;
            return null;
        }

        return $this->ruleCallbacks[$rule];
    }

    /**
     * Is run first
     * Suggests override if it is needed
     */
    protected function initialization()
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
    abstract protected function successBody();

    /**
     * Is run third in case of the fail
     */
    protected function failBody()
    {
        // Here is nothing to do
    }

    /**
     * Выполняется перед сохранением состояния экшна.
     * Оно сохраняется при успехе/неудаче, только если соответсвующие редиректы не null.
     * Тут можно очистить данные, которые не нужно сохранять (например, пароли).
     * Suggests override if it is needed
     */
    protected function beforeSave() 
    { 
        // Here is nothing to do 
    }

    /**
     * Выполняется после загрузки состояния экшна. Оно загружается, только если было сохранено. 
     * Оно сохраняется при успехе/неудаче, только если соответсвующие редиректы не null.
     * Suggests override if it is needed
     */
    protected function afterLoad()
    {
        // Here is nothing to do
    }

    /**
     * Возвращает адрес веб-страницы, на которую нужно перейти после успешного
     * (без ошибок во время валидации данных) завершения экшна или null, 
     * если не нужно никуда переходить.
     * 
     * Suggests override if it is needed.
     * 
     * @return string|null
     */
    protected function getSuccessRedirect()
    {
        if (Request::hasReferer()) return Request::getReferer();
        else return '/';
    }

    /**
     * Возвращает адрес веб-страницы, на которую нужно перейти после неудачного
     * (с ошибками во время валидации данных) завершения экшна или null, 
     * если не нужно никуда переходить.
     * 
     * Suggests override if it is needed.
     * 
     * @return string|null
     */
    protected function getFailRedirect()
    {
        if (Core::$app->config->{'actions.defaultFailRedirectMode'} === 'back') {
            if (Request::hasReferer()) return Request::getReferer();
            else return '/';
        } else return null;
    }

    /**
     * Сохраняет свое состояние перед редиректом.
     * Сохраняются статус, ошибки и введенные данные.
     * Файлы не сохраняются.
     */
    private function save()
    {
        $this->beforeSave();
        $sessions = new SessionTransmitter;
        $sessions->setData($this->name.'_status', $this->status);
        if ($this->isFail()) $sessions->setData($this->name.'_errors', serialize($this->errors));
        if ($this->isFail()) $sessions->setData($this->name.'_data', serialize($this->data));
    }

    /**
     * Загружает свое сохраненное состояние после редиректа.
     */
    private function load()
    {
        $sessions = new SessionTransmitter;
        if ($sessions->isSetData($this->name . '_status')) {
            $this->status = $sessions->getData($this->name . '_status');
            $sessions->removeData($this->name . '_status');
            if ($sessions->isSetData($this->name . '_errors')) {
                $this->errors = unserialize($sessions->getData($this->name . '_errors'));
                $sessions->removeData($this->name . '_errors');
            }
            if ($sessions->isSetData($this->name . '_data')) {
                $this->data = unserialize($sessions->getData($this->name . '_data'));
                $sessions->removeData($this->name . '_data');
            }
            $this->afterLoad();
        }
    }

    /**
     * Возвращает массив вида ['field' => ['rule1', 'rule2']] с именами
     * правил валидации данных соответствующего типа из конфига.
     * 
     * @param string $type get|post.
     * @return array
     * @throws NoRuleError|RuleCheckFailedException Подробнее в описании классов 
     * этих исключений.
     */
    private function ruleValidate($type)
    {
        $errors = [];
        if (!$this->config) return $errors;
        if (!$this->config->isset($type)) return $errors;

        $data = $this->data[$type];
        $this->interData[$type] = [];

        // Проходимся по каждому полю
        foreach ($this->config->$type as $field => $rules) {

            $this->interData[$type][$field] = [];

            // Правил может не быть.
            if (!isset($rules['rules'])) continue;

            $fieldValue = isset($data[$field]) ? $data[$field] : null;
            $result = new RuleResult;

            // Проходимся по каждому правилу проверок поля
            foreach ($rules['rules'] as $rule => $ruleValue) {
                $check = $this->getRuleCallback($rule);
                // При noRuleMode = ignore, метод вернет null, иначе будет выброшено
                // исключение еще в getRuleCallback.
                if (!$check) continue;

                // Т.к. для всей цепочки проверок правила используется один и тот
                // же экземпляр класса, перед каждой обработкой необходимо
                // восстанавливать результат после предыдущей обработки.
                $result->restoreResult();
                $result = $check($ruleValue, $fieldValue, $result);

                // Каждая проверка должна вернуть результат с одним из двух
                // состояний: провал и успех.
                if (!$result->hasResult()) 
                    throw new \Exception('Rule result state has not changed.');
                if ($result->isFail()) $this->_setError($type, $errors, $field, $rule);
                if ($result->isStopped()) break;
            }

            $this->interData[$type][$field] = $result->getInterDataAll();
        }

        return $errors;
    }

    private function _setError($type, &$errors, $field, $rule)
    {
        // Не проверяем config и post и $field на наличие, т.к. эта функция 
        // вызывается только там, где это уже проверено и используется.
        if (isset($this->config->$type[$field]['errorRules'])
            && in_array($rule, $this->config->$type[$field]['errorRules']))
        {
            throw new RuleCheckFailedException($this, $type, $field, $rule);
        }

        if (!isset($errors[$field])) $errors[$field] = [];
        // Вместо int-кода ошибки, добавляем имя правила.
        // @todo В будущем это можно улучшить, присвоив каждому
        // правилу числовой id. Где-то в библиотеке даже была
        // функция, которая превращает строку в число, суммируя
        // коды символов в слове.
        $errors[$field][] = $rule;
    }
}