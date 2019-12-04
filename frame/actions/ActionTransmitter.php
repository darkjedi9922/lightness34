<?php namespace frame\actions;

use frame\tools\transmitters\SessionTransmitter;

class ActionTransmitter
{
    /**
     * Сохраняет состояние экшна. Сохраняются статус, ошибки и введенные post данные.
     * Файлы не сохраняются.
     */
    public function save(Action $action)
    {
        $idName = $action->getIdName();
        $sessions = new SessionTransmitter;
        $sessions->setData($idName, serialize([
            $action->isExecuted(),
            $this->assemblePostToSave($action),
            $action->getErrors()
        ]));
    }

    private function assemblePostToSave(Action $action): array
    {
        $result = [];
        $post = $action->getDataArray()['post'];
        foreach ($action->getPostToSave() as $name) {
            if (isset($post[$name]))
                $result[$name] = $post[$name];
        }
        return $result;
    }
}