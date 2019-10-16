<?php namespace tests\engine;

use frame\actions\Action;

class UserDeleteAction extends Action
{
    protected function getConfig(): array
    {
        return [
            "post" => [
                "id" => [
                    "rules" => [
                        "base/mandatory" => true,
                        "base/emptiness" => false,
                        "userIdExists" => true
                    ]
                ],
                'answer' => [
                    'default' => [42]
                ],
                'question' => [
                    'default' => ['...']
                ]
            ]
        ];
    }

    protected function initialize()
    {
        // Это правило в идеале выделено и не имеет доступа к данным этого.
        $this->setRuleCallback('userIdExists', 
            function($rule, int $value, $result) 
        {
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

        // Это правило тоже в идеале выделено.
        $this->setRuleCallback('canDeleteUserId', 
            function($rule, $value, $result) 
        {
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
            throw new \Exception('deletedUser должен был быть установлен (post id user = 1)');
    }

    protected function getSuccessRedirect(): ?string
    {
        return null;
    }

    protected function getFailRedirect(): ?string
    {
        return null;
    }
}