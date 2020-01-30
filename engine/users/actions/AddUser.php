<?php namespace engine\users\actions;

use engine\users\actions\fields\EmailField;
use engine\users\actions\fields\GenderField;
use engine\users\actions\fields\NameField;
use engine\users\actions\fields\UserAvatarField;
use engine\users\actions\fields\UserLogin;
use engine\users\actions\fields\UserPassword;
use engine\users\Encoder;
use engine\users\Group;
use engine\users\User;
use frame\actions\ActionBody;
use frame\actions\UploadedFile;
use frame\auth\Auth;
use frame\cash\prev_router;
use frame\tools\Client;
use frame\tools\Init;

class AddUser extends ActionBody
{
    const E_NO_LOGIN = 1;
    const E_LONG_LOGIN = 2;
    const E_LOGIN_EXISTS = 3;
    const E_INCORRECT_LOGIN = 4;
    const E_NO_PASSWORD = 12;
    const E_LONG_PASSWORD = 5;
    const E_INCORRECT_PASSWORD = 6;
    const E_INCORRECT_EMAIL = 7;
    const E_INCORRECT_NAME = 8;
    const E_INCORRECT_SURNAME = 9;
    const E_AVATAR_TYPE = 10;
    const E_AVATAR_SIZE = 11;

    private $login;

    public function listPost(): array
    {
        return [
            'login' => self::POST_TEXT,
            'password' => self::POST_PASSWORD,
            'email' => self::POST_TEXT,
            'name' => self::POST_TEXT,
            'surname' => self::POST_TEXT,
            'gender_id' => self::POST_INT
        ];
    }

    public function initialize(array $get)
    {
        $amIGuest = !(new Auth)->isLogged();
        if (!$amIGuest) $prevRoute = prev_router::get();
        Init::access($amIGuest || $prevRoute && $prevRoute->isInNamespace('admin'));
    }

    public function validate(array $post, array $files): array
    {
        $gender = new GenderField($post['gender_id']);
        $gender->requireDefined();
        
        $errors = [];

        $login = new UserLogin($post['login']);
        if ($login->isEmpty()) $errors[] = self::E_NO_LOGIN;
        else {
            if ($login->isTooLongByConfig()) $errors[] = self::E_LONG_LOGIN;
            if ($login->isIncorrect()) $errors[] = self::E_INCORRECT_LOGIN;
            if ($errors == [] && $login->isTaken()) $errors[] = self::E_LOGIN_EXISTS;
        }

        $password = new UserPassword($post['password']);
        if ($password->isEmpty()) $errors[] = self::E_NO_PASSWORD;
        else {
            if ($password->isTooLongByConfig()) $errors[] = self::E_LONG_PASSWORD;
            if ($password->isIncorrect()) $errors[] = self::E_INCORRECT_PASSWORD;
        }

        $email = new EmailField($post['email']);
        if (!$email->isEmpty()) {
            if ($email->isIncorrect()) $errors[] = self::E_INCORRECT_EMAIL;
        }

        $name = new NameField($post['name']);
        if ($name->isIncorrect()) $errors[] = self::E_INCORRECT_NAME;

        $surname = new NameField($post['surname']);
        if ($surname->isIncorrect()) $errors[] = self::E_INCORRECT_SURNAME;

        $avatar = new UserAvatarField($files['avatar']);
        if (!$avatar->isEmpty()) {
            if (!$avatar->isValidByteSize()) $errors[] = self::E_AVATAR_SIZE;
            if (!$avatar->isImage()) $errors[] = self::E_AVATAR_TYPE;
        }

        return $errors;
    }

    public function succeed(array $post, array $files): array
    {
        $user = new User;
        $user->login = $post['login'];
        $user->password = Encoder::getPassword($post['password']);
        $user->sid = Encoder::getSid($user->login, $user->password);
        $user->email = $post['email'];
        $user->name = $post['name'];
        $user->surname = $post['surname'];
        $user->gender_id = $post['gender_id'];
        $user->group_id = Group::USER_ID;
        $user->registration_date = time();
        $user->last_user_agent = Client::getUserAgent();
        
        /** @var UploadedFile $avatar */ $avatar = $files['avatar'];
        if (!$avatar->isEmpty()) {
            $user->avatar = $avatar->moveUnique(User::AVATAR_FOLDER);
        }

        $id = $user->insert();
        $this->login = $user->login;
        return ['id' => $id, 'login' => $user->login];
    }

    public function getSuccessRedirect(): ?string
    {
        $prevRouter = prev_router::get();
        if ($prevRouter && $prevRouter->isInNamespace('admin'))
            return "/admin/users/profile/{$this->login}";
        else return '';
    }
}