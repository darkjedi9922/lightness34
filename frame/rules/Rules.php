<?php namespace frame\rules;

use frame\rules\errors\NoRuleException;
use frame\actions\RuleResult;

class Rules
{
    const RULE_DIR = ROOT_DIR . '/rules';

    /**
     * @var array Ассоциативный массив вида [string => callable]
     */
    private $ruleCallbacks = [];

    public static function loadRule(string $rule): ?callable
    {
        $file = self::RULE_DIR . '/' . $rule . '.php';
        if (file_exists($file)) return require($file);
        return null;
    }

    /**
     * Устанавливает callback-функцию, которая будет вызываться при проверке
     * поля, заданной в json-настройках валидации.
     * 
     * Callback-функция вида (mixed $rule, $mixed $value, RuleResult $result): bool,
     * где $rule - значение правила, $value - проверяемое значение. Если проверяемого
     * значения изначально нет, будет передано null. Callback возвращает должен
     * возвратить переданный $result, предварительно установив новое значение
     * результата в нем.
     * 
     * Если проверка не пройдена, в post errors добавится имя ошибки, равное $name.
     * Но вместо этого может быть выброшено исключение.
     * @see RuleCheckFailedException.
     * 
     * При этом callback может выбросить исключение StopRuleException с результатом
     * проверки.
     * @see StopRuleException.
     */
    public function setRuleCallback(string $name, callable $callback)
    {
        $this->ruleCallbacks[$name] = $callback;
    }

    /**
     * Сначала ищет правило среди напрямую установленных в объект экшна правил, а
     * потом, если не находит, загружает его из директории с правилами.
     * @throws NoRuleException Если обработчик правила не установлен и не найден.
     */
    public function getRuleCallback(string $rule): callable
    {
        if (isset($this->ruleCallbacks[$rule])) return $this->ruleCallbacks[$rule];
        $callback = self::loadRule($rule);
        if (!$callback) throw new NoRuleException($rule);
        return $callback;
    }
}