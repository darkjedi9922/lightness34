<?php namespace engine\users\actions;

use frame\auth\Auth;
use frame\database\Records;
use engine\users\Encoder;
use frame\actions\ActionBody;
use frame\actions\fields\BooleanField;
use frame\actions\fields\PasswordField;
use frame\actions\fields\StringField;
use frame\tools\Init;

class LoginAction extends ActionBody
{
    const E_NO_LOGIN = 1;
    const E_NO_PASSWORD = 2;
    const E_WRONG_PASSWORD = 3;

    private $sid;

    public function listPost(): array
    {
        return [
            'login' => StringField::class,
            'password' => PasswordField::class,
            'remember' => BooleanField::class
        ];
    }

    public function initialize(array $get)
    {
        Init::accessLogged(false);
    }

    public function validate(array $post, array $files): array
    {
        $errors = [];

        $login = $post['login']->get();
        $password = $post['password']->get();

        if (!$login) $errors[] = self::E_NO_LOGIN;
        if (!$password) $errors[] = self::E_NO_PASSWORD;

        if (empty($errors)) {
            $password = Encoder::getPassword($password);
            $data = Records::from('users', ['login' => $login])
                ->select(['password', 'sid'])->readLine();
            if ($data === null || $password !== $data['password']) 
                $errors[] = self::E_WRONG_PASSWORD;
            else $this->sid = $data['sid'];
        }

        return $errors;
    }

    public function succeed(array $post, array $files)
    {
        (new Auth)->login($this->sid, $post['remember']->get());
    }

    public function getSuccessRedirect(): ?string
    {
        return '/articles';
    }
}