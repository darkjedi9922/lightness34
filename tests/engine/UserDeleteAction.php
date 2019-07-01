<?php namespace tests\engine;

use frame\actions\Action;
use frame\actions\rules\ActionBaseRules;

class UserDeleteAction extends Action
{
    protected function initialize()
    {
        $baseRules = new ActionBaseRules;
        $this->setRule('mandatory', $baseRules->getMandatoryRule());
        $this->setRule('emptiness', $baseRules->getEmptinessRule());
        
        // Эта проверка в идеале выделена в другой класс и не имеет доступа к данным
        // этого.
        $this->setRule('userIdExists', function($rule, int $value, $result) {
            // Информация о пользователе берется из базы данных. Результат утрирован.
            if ($value === 1) $userInfo = ['id' => 1, 'login' => 'JustMortalUser'];
            else $userInfo = null;

            if ($rule == true && !$userInfo) {
                return $result->fail()->stop();
            }

            // В результат можно сохранить промежуточные данные для использования
            // его другими проверками и самим экшном.
            $result->setInterData('user', $userInfo);

            return $result->succeed();
        });

        // Эта проверка тоже в идеале выделена.
        $this->setRule('canDeleteUserId', function($rule, $value, $result) {
            // Для работы этой проверки требуется промежуточные данные. Если их нет,
            // будет выброшено исключение.
            $userInfo = $result->requireInterData('user');

            if ($userInfo['login'] === 'Admin') return $result->fail();
            return $result->succeed();
        });
    }

    protected function succeed()
    {
        $deletedUser = $this->requireInterData('post', 'id', 'user');
        // Используем данные, загруженные ранее, дальше...
        if (!$deletedUser || $deletedUser['id'] !== 1) 
            // Эта проверка лишь для процесса тестирования, убедиться что
            // переменная действительно была установлена и ее правда можно получить.
            throw new \Exception('deletedUser должен был быть установлен.');
    }
}