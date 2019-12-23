<?php namespace engine\admin\actions;

use frame\cash\prev_router;
use engine\admin\Auth;
use frame\actions\ActionBody;
use frame\config\Json;
use frame\tools\Init;

/**
 * Права: доступ в админ-панель.
 * Данные:
 * password: пароль
 */
class LoginAction extends ActionBody
{
    const E_WRONG_PASSWORD = 1;

    /** @var Auth $auth */
    private $auth;

    public function listPost(): array
    {
        return [
            'password' => [self::POST_PASSWORD, 'The password to admin panel']
        ];
    }

    public function initialize(array $get)
    {
        Init::accessRight('admin', 'enter');
        $this->auth = new Auth;
    }

    public function validate(array $post, array $files): array
    {
        $errors = [];
        $config = new Json('config/admin.json');

        $password = $post['password'] ?? '';

        if ($password !== $config->password) $errors[] = static::E_WRONG_PASSWORD;

        return $errors;
    }

    public function succeed(array $post, array $files)
    {
        $this->auth->login($_SERVER['REMOTE_ADDR']);
    }

    public function fail(array $post, array $files)
    {
        $this->auth->logout();
    }

    public function getSuccessRedirect(): ?string
    {
        // Если "я" пытался перейти на какую-либо страницу в админ-панели,
        // но по каким-то причинам пришлось авторизоваться, нужно перейти 
        // на изначально желаемую страницу.
        $prevRouter = prev_router::get();
        if ($prevRouter && $prevRouter->getPathPart(0) === 'admin' 
            && $prevRouter->getPathPart(1)) return $prevRouter->toUrl();
        // Иначе переходим на главную.
        else return '/admin/home';
    }
}