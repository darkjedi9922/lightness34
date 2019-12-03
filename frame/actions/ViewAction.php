<?php namespace frame\actions;

class ViewAction
{
    /** @var Action */
    private $action;

    /**
     * @throws Exception if the class is not a subclass of Action.
     */
    public function __construct(string $class, array $args = [])
    {
        if (!is_subclass_of($class, Action::class)) 
            throw new \Exception("Class $class is not a subclass of Action");
        $this->action = new $class($args);
    }

    /**
     * @param mixed $value
     */
    public function setArg(string $name, $value)
    {
        $this->action->setData(Action::ARGS, $name, $value);
    }

    /**
     * @param mixed $default
     * @return mixed
     */
    public function getPost(string $name, $default = null)
    {
        return $this->action->getData(Action::POST, $name, $default);
    }

    public function getUrl(): string
    {
        return $this->action->getUrl();
    }

    public function hasErrors(): bool
    {
        return $this->action->hasErrors();
    }

    public function hasError(int $code): bool
    {
        return $this->action->hasError($code);
    }

    public function isExecuted(): bool
    {
        return $this->action->isExecuted();
    }
}