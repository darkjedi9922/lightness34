<?php namespace frame\rules;

use frame\rules\errors\NoRuleException;

class Rules
{
    const RULE_DIR = ROOT_DIR . '/rules';

    /** @var array Ассоциативный массив вида [string => callable] */
    private $ruleCallbacks = [];
    private $values;
    private $rules;
    private $errors = [];
    private $interData = [];

    public static function loadRule(string $rule): ?callable
    {
        $file = self::RULE_DIR . '/' . $rule . '.php';
        if (file_exists($file)) return require($file);
        return null;
    }

    public function __construct(array $values = [], array $rules = [])
    {
        $this->values = $values;
        $this->rules = $rules;
    }

    public function setValues(array $values)
    {
        $this->values = $values;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function setRules(array $rules)
    {
        $this->rules = $rules;
    }

    public function getRules(): array
    {
        return $this->rules;
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

    public function validate()
    {
        foreach ($this->getValidation() as $rule);
    }

    /**
     * Во время прохода генератора полностью выполняется валидация правил.
     * Значением генератора является RuleResult после каждой проверки каждого
     * правила. Это можно использовать для "декорирования" валидации в специфических
     * случаях.
     */
    public function getValidation(): \Generator
    {
        $this->errors = [];
        $this->interData = [];

        // Проходимся по каждому полю
        foreach ($this->rules as $field => $rules) {
            $this->interData[$field] = [];

            // Правил может не быть.
            if (!isset($rules['rules'])) continue;

            $value = $this->values[$field] ?? null;
            $result = new RuleResult;

            // Проходимся по каждому правилу проверок поля
            foreach ($rules['rules'] as $rule => $ruleValue) {
                $check = $this->getRuleCallback($rule);

                // Т.к. для всей цепочки проверок правила используется один и тот
                // же экземпляр класса, перед каждой обработкой необходимо
                // восстанавливать результат после предыдущей обработки.
                $result->restoreResult();
                /** @var RuleResult|null $result */
                $result = $check($ruleValue, $value, $result);

                // Каждая проверка должна вернуть результат с одним из двух
                // состояний: провал и успех.
                if (!$result || !$result->hasResult())
                    throw new RuleRuntimeException(
                        $this,
                        $field,
                        $rule,
                        'Rule result state has not changed.'
                    );

                yield $result;

                if ($result->isFail()) {
                    if (!isset($this->errors[$field])) $this->errors[$field] = [];
                    // Вместо int-кода ошибки, добавляем имя правила. Это лучше для 
                    // читаемости и в целом красиво. Пока что нет необходимости 
                    // оптимизировать это, чтобы хранить числа вместо строк.
                    $this->errors[$field][] = $rule;
                }
                if ($result->isStopped()) break;
            }

            $this->interData[$field] = $result->getInterDataAll();
        }
    }

    public function hasError(string $field, string $rule): bool
    {
        return isset($this->errors[$field])
            && in_array($rule, $this->errors[$field]);
    }

    /** @return mixed|null */
    public function getInterData(string $field, string $data)
    {
        return $this->interData[$field][$data] ?? null;
    }
}