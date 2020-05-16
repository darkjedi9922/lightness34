<?php namespace frame\actions;

use frame\route\SessionTransmitter;

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
     * 
     * Среди post данных сохраняются лишь те, что указаны в ActionBody::listPost(),
     * за исключением полей, у которых ActionField::canBeSaved() возвращает false.
     */
    public function save(Action $action)
    {
        $idName = get_class($action->getBody()) . '_' . $action->getId();
        $this->sessions->setData($idName, serialize([
            $this->assemblePostToSave($action),
            $action->getErrors(),
            $action->getResult()
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
        list($post, $errors, $result) = unserialize($this->sessions->$idName);
        $action = Action::fromState(new $class, $post, $errors, $result);
        $this->sessions->removeData($idName);
        return $action;
    }

    private function assemblePostToSave(Action $action): array
    {
        $result = [];
        $post = $action->getDataArray()['post'];
        foreach ($post as $field => $value) {
            if ($value instanceof ActionField && $value->canBeSaved())
                $result[$field] = $value->get();
        }
        return $result;
    }
}