<?php namespace frame\actions;

class ViewAction
{
    /** @var Action */
    private $action;
    private $router;
    private $executed = false;

    /**
     * @throws Exception if the class is not a subclass of ActionBody.
     */
    public function __construct(string $class, array $args = [])
    {
        if (!is_subclass_of($class, ActionBody::class)) 
            throw new \Exception("Class $class is not a subclass of ActionBody");
        
        $transmitter = new ActionTransmitter;
        $this->action = $transmitter->load($class, $args[Action::ID] ?? '');
        if ($this->action) {
            $this->executed = true;
            $this->action->setDataAll(Action::ARGS, $args);
        } else $this->action = new Action(new $class, $args);

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
        return $this->executed;
    }
}