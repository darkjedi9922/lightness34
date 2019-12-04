<?php namespace frame\actions;

class ViewAction
{
    /** @var Action */
    private $action;
    private $router;

    /**
     * @throws Exception if the class is not a subclass of Action.
     */
    public function __construct(string $class, array $args = [])
    {
        if (!is_subclass_of($class, Action::class)) 
            throw new \Exception("Class $class is not a subclass of Action");
        
        $transmitter = new ActionTransmitter;
        $this->action = $transmitter->load($class, $args[Action::ID] ?? '');
        if ($this->action) $this->action->setDataAll(Action::ARGS, $args);
        else $this->action = new $class($args);

        $this->router = new ActionRouter;
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
        return $this->router->getTriggerUrl($this->action);
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