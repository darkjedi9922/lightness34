<?php namespace frame\actions;

use frame\tools\transmitters\SessionTransmitter;

class ActionTransmitter
{
    private $sessions;

    public function __construct()
    {
        $this->sessions = new SessionTransmitter;
    }

    /**
     * Сохраняет состояние экшна. Сохраняются статус, ошибки и введенные post данные.
     * Файлы не сохраняются.
     */
    public function save(Action $action)
    {
        $idName = get_class($action->getBody()) . '_' . $action->getId();
        $this->sessions->setData($idName, serialize([
            $this->assemblePostToSave($action),
            $action->getErrors()
        ]));
    }

    /**
     * Загружает заданный экшн из сохраненного состояния. Если он не был сохранен,
     * вернет null.
     */
    public function load(string $class, string $id = ''): ?Action
    {
        $idName = $class . '_' . $id;
        if (!$this->sessions->isSetData($idName)) return null;
        list($post, $errors) = unserialize($this->sessions->getData($idName));
        $action = Action::fromState(new $class, $post, $errors);
        $this->sessions->removeData($idName);
        return $action;
    }

    private function assemblePostToSave(Action $action): array
    {
        $result = [];
        $post = $action->getDataArray()['post'];
        foreach ($action->getBody()->getPostToSave() as $name) {
            if (isset($post[$name]))
                $result[$name] = $post[$name];
        }
        return $result;
    }
}