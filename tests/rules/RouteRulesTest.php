<?php

use PHPUnit\Framework\TestCase;
use frame\rules\RouteRules;
use frame\errors\HttpError;
use frame\route\Router;

class RouteRulesTest extends TestCase
{
    public function testThrowsError404IfRulesAreNotMatched()
    {
        // Параметр login задан и он пуст.
        $router = new Router('?login');

        $rules = new RouteRules($router, [
            'login' => [
                'rules' => [
                    // Параметр login может быть не задан.
                    'base/mandatory' => false,
                    // Но если он задан, он не должен быть пустым.
                    'base/emptiness' => false
                ]
            ]
        ]);

        $this->expectExceptionCode(HttpError::NOT_FOUND);
        
        // Правила не выполняются, возникнет ошибка 404.
        $rules->assert();
    }

    public function testDoesNotThrowsError404IfRulesAreMatched()
    {
        // Параметр login задан и он не пуст.
        $rules = new RouteRules(new Router('?login=admin'), [
            'login' => [
                'rules' => [
                    // Параметр login может быть не задан.
                    'base/mandatory' => false,
                    // Но если он задан, он не должен быть пустым.
                    'base/emptiness' => false
                ]
            ]
        ]);

        // Правила выполняются, ошибки не будет (код 0).
        $rules->assert();

        // Тут тоже все по правилам - логин не задан вообще.
        $rules->setRouter(new Router(''));
        $rules->assert();

        // Если никаких исключений не было, оно дойдет до сюда.
        $this->assertTrue(true);
    }
}