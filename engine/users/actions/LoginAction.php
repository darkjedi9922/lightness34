<?php namespace engine\users\actions;

use frame\auth\Auth;
use frame\database\Records;
use engine\users\Encoder;
use frame\actions\Action;
use frame\errors\HttpError;

class LoginAction extends Action
{
    const E_NO_LOGIN = 1;
    const E_NO_PASSWORD = 2;
    const E_WRONG_PASSWORD = 3;

    private $sid;
    /** @var Auth $auth */
    private $auth;

    protected function initialize()
    {
        $this->auth = new Auth;
        if ($this->auth->isLogged()) throw new HttpError(HttpError::FORBIDDEN);
    }

    public function validate(): array
    {
        $errors = [];

        $login = $this->getData('post', 'login');
        $password = $this->getData('post', 'password');

        if (!$login) $errors[] = self::E_NO_LOGIN;
        if (!$password) $errors[] = self::E_NO_PASSWORD;

        if (empty($errors)) {
            if ($login === null || $password === null) return [];
            $password = Encoder::getPassword($password);
            $data = Records::select('users', ['login' => $login])
                ->load(['password', 'sid'])->readLine();
            if ($data === null || $password !== $data['password']) 
                $errors[] = self::E_WRONG_PASSWORD;
            else $this->sid = $data['sid'];
        }

        return $errors;
    }

    public function succeed()
    {
        $remember = (bool) $this->getData('post', 'remember');
        $this->auth->login($this->sid, $remember);
    }

    protected function getSuccessRedirect(): ?string
    {
        return '/articles';
    }
}