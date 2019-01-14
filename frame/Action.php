<?php namespace frame;

use function lightlib\encode_specials;

use frame\Core;
use frame\LatePropsObject;
use frame\route\Router;
use frame\route\Request;
use frame\route\Response;
use frame\tools\transmitters\SessionTransmitter;
use frame\tools\Json;
use frame\actions\NoRuleError;
use frame\actions\StopRuleException;

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
     * @var array Коды ошибок, которые возникли в процессе выполненя валидации
     */
    public $errors = [];

    /**
     * @var array Переданные данные через POST
     */
    public $post = [];

    /**
     * @var array Параметры, заданные в конструкторе
     */
    public $params = [];

    /**
     * @var Json Настройки валидации.
     */
    private $validationJson = null;

    /**
     * @var array Ассоциативный массив вида [string => [callable, bool]
     */
    private $ruleCallbacks = [];

    /**
     * @var array Массив вида ['field' => [1, 2, 3]] с кодами ошибок post полей,
     * которые возникли во время выполнения экшна.
     */
    private $postErrors = [];

    /**
     * @var string
     */
    private $noRuleMode = self::NO_RULE_ERROR;

    /**
     * @param array $params Параметры экшна
     * @param int $id Id экшна. Нужен, если на одной странице используется несколько экшнов
     * одного класса с разными параметрами, чтобы понимать какой из них выполнять
     * @return static
     */
    public static function instance($params = [], $id = '')
    {
        if (isset(static::$_current) && static::$_current->name === $id . '_' . static::class) 
            return static::$_current;
        
        $noRuleMode = Core::$app->config->{'actions.noRuleMode'};
        $action = new static($params, $id, $noRuleMode);
        return $action;
    }

    /**
     * Warning: Создание объекта непосредственно через конструктор создает отдельный
     * независимый экземпляр экшна, независимо от состояния всего приложения. 
     * Это больше подходит для тестирования. Для получения экземпляра экшна при
     * работе приложения (с инициализированным Core) требуется использовать
     * статический метод instance().
     * 
     * @param array $params Параметры экшна.
     * @param int $id Id экшна. Нужен, если на одной странице используется несколько 
     * экшнов одного класса с разными параметрами, чтобы понимать какой из них 
     * выполнять.
     * @param string $noRuleMode Что делать, если для конфиг-валидации экшна в экшне
     * не установлен механизм обработки правила. Значения: 'error' (выбрасывает
     * исключение типа NoRuleError) или 'ignore' (пропускает правило).
     */
    public function __construct(
        $params = [],
        $id = '',
        $noRuleMode = self::NO_RULE_ERROR)
    {
        $this->app = Core::$app;
        $this->name = $id . '_' . static::class;
        $this->params = $params;
        $this->noRuleMode = $noRuleMode;
        $this->load();
    }

    /**
     * @return string Триггерное url на выполнение экшна
     */
    public final function getUrl()
    {
        $this->params['action'] = $this->name;
        return Core::$app->router->toUrl(['action' => http_build_query($this->params, '', ';')]);
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return string|null|mixed
     */
    public final function getParameter($name, $default = null)
    {
        return isset($this->params[$name]) ? $this->params[$name] : $default;
    }

    /**
     * @param string $name
     * @param string|null $value Если передано null, удаляет значение.
     */
    public function setPostOne($name, $value)
    {
        $_POST[$name] = $value;
    }

    /**
     * После данного метода скрипт завершает свое выполнение.
     * Кодирует спецсимволы полученных POST данных.
     */
    public final function exec()
    {
        $this->initialization();
        $this->post = $this->encodeSpecials($_POST);
        $this->postErrors = $this->configValidatePost($this->post);
        $this->errors = $this->validate($this->post, $_FILES);
        if (empty($this->postErrors) && empty($this->errors)) {
            $this->successBody($this->post, $_FILES);
            $this->status = self::SUCCESS;
            if ($this->getSuccessRedirect() !== null) {
                $this->save();
                Response::setUrl(Router::toUrlOf($this->getSuccessRedirect()));
            }
        } else {
            $this->failBody($this->post, $_FILES);
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
     * @param int $error Код ошибки
     * @return bool
     */
    public function hasError($error)
    {
        return in_array($error, $this->errors);
    }

    /**
     * Возвращает есть ли у заданного post-поля заданный код ошибки.
     * 
     * @param string|int $error Имя или код ошибки.
     * @return bool
     */
    public function hasPostError($field, $error)
    {
        return isset($this->postErrors[$field]) && in_array($error, $this->postErrors[$field]);
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return string|null|mixed
     */
    public function getPost($name, $default = null)
    {
        if (isset($this->post[$name])) return $this->post[$name];
        else return $default;
    }

    /**
     * @param Json|null $config
     */
    public function setValidationConfig($config)
    {
        $this->validationJson = $config;
    }

    /**
     * @return Json|null
     */
    public function getValidationConfig()
    {
        return $this->validationJson ? $this->validationJson : null;
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
     * 
     * При этом callback может выбросить исключение StopRuleException с результатом
     * проверки. Тогда все оставшиеся правила проверяемого поля не будут обработаны.
     * Это нужно, когда нет смысла проверять значение поля дальше, например, если
     * значение поля не было передано вообще и тогда проверять дальше нечего.
     * 
     * @param string $name Имя проверки
     * @param callable $callback
     * @param bool $onlyPresentValues Запускать проверку только когда значение было
     * передано 
     * 
     */
    public function setRule($name, $callback, $onlyPresentValues = false)
    {
        $this->ruleCallbacks[$name][0] = $callback;
        $this->ruleCallbacks[$name][1] = $onlyPresentValues;
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
     * @param array $post
     * @param array $files
     * @return array Коды ошибок
     */
    protected function validate($post, $files)
    {
        return []; // Here is nothing to validate
    }

    /**
     * Is run third in case of the success
     * 
     * @param array $post
     * @param array $files
     */
    abstract protected function successBody($data, $files);

    /**
     * Is run third in case of the fail
     * 
     * @param array $post
     * @param array $files
     */
    protected function failBody($data, $file)
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
        if ($this->isFail()) $sessions->setData($this->name . '_post-errors', serialize($this->postErrors));
        if ($this->isFail()) $sessions->setData($this->name.'_errors', serialize($this->errors));
        if ($this->isFail()) $sessions->setData($this->name.'_data', serialize($this->post));
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
            if ($sessions->isSetData($this->name . '_post-errors')) {
                $this->postErrors = unserialize($sessions->getData($this->name . '_post-errors'));
                $sessions->removeData($this->name . '_post-errors');
            }
            if ($sessions->isSetData($this->name . '_errors')) {
                $this->errors = unserialize($sessions->getData($this->name . '_errors'));
                $sessions->removeData($this->name . '_errors');
            }
            if ($sessions->isSetData($this->name . '_data')) {
                $this->post = unserialize($sessions->getData($this->name . '_data'));
                $sessions->removeData($this->name . '_data');
            }
            $this->afterLoad();
        }
    }
    
    /**
     * Кодирует спецсимволы во благо безопасности
     * 
     * @param array $data
     * @return array
     */
    private function encodeSpecials($data) : array
    {
        foreach ($data as $key => $value) {
            if (is_array($data[$key])) $data[$key] = $this->encodeSpecials($data[$key]);
            else $data[$key] = encode_specials($value);
        }
        return $data;
    }

    /**
     * Возвращает массив вида ['field' => [1, 2, 3]] с кодами ошибок post полей.
     * @return array
     */
    private function configValidatePost($data)
    {
        $errors = [];
        if (!$this->validationJson) return $errors;
        if (!$this->validationJson->isset('post')) return $errors;

        // Проходимся по каждому полю
        foreach ($this->validationJson->get('post') as $field => $rules) {
            if (!isset($rules['rules'])) continue;
            $fieldValue = isset($data[$field]) ? $data[$field] : null;

            // Проходимся по каждому правилу проверок поля
            foreach ($rules['rules'] as $rule => $ruleValue) {
                if (isset($this->ruleCallbacks[$rule])) {
                    $onlyPresentValues = $this->ruleCallbacks[$rule][1];
                    if ($onlyPresentValues && $fieldValue === null) continue;
                    $check = $this->ruleCallbacks[$rule][0];
                    try {
                        $result = $check($ruleValue, $fieldValue);
                        if (!$result) {
                            if (!isset($errors[$field])) $errors[$field] = [];
                            // Вместо int-кода ошибки, добавляем имя правила.
                            // @todo В будущем это можно улучшить, присвоив каждому
                            // правилу числовой id. Где-то в библиотеке даже была
                            // функция, которая превращает строку в число, суммируя
                            // коды символов в слове.
                            $errors[$field][] = $rule;
                        }
                    } catch (StopRuleException $e) {
                        if ($e->isFail()) {
                            // @todo Убрать дублирование.
                            if (!isset($errors[$field])) $errors[$field] = [];
                            $errors[$field][] = $rule;
                        }
                        break;
                    }
                } else {
                    if ($this->noRuleMode == self::NO_RULE_ERROR) {
                        throw new NoRuleError;
                    }
                }
            }
        }

        return $errors;
    }
}