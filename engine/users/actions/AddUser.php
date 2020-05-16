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
use frame\stdlib\cash\prev_route;
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
            'login' => UserLogin::class,
            'password' => UserPassword::class,
            'email' => EmailField::class,
            'name' => NameField::class,
            'surname' => NameField::class,
            'gender_id' => GenderField::class
        ];
    }

    public function initialize(array $get)
    {
        Init::accessRight('users', 'add');
    }

    public function validate(array $post, array $files): array
    {
        /** @var GenderField $gender */ $gender = $post['gender_id'];
        $gender->requireDefined();
        
        $errors = [];

        /** @var UserLogin $login */ $login = $post['login'];
        if ($login->isEmpty()) $errors[] = self::E_NO_LOGIN;
        else {
            if ($login->isTooLongByConfig()) $errors[] = self::E_LONG_LOGIN;
            if ($login->isIncorrect()) $errors[] = self::E_INCORRECT_LOGIN;
            if ($errors == [] && $login->isTaken()) $errors[] = self::E_LOGIN_EXISTS;
        }

        /** @var UserPassword $password */ $password = $post['password'];
        if ($password->isEmpty()) $errors[] = self::E_NO_PASSWORD;
        else {
            if ($password->isTooLongByConfig()) $errors[] = self::E_LONG_PASSWORD;
            if ($password->isIncorrect()) $errors[] = self::E_INCORRECT_PASSWORD;
        }

        /** @var EmailField $email */ $email = $post['email'];
        if (!$email->isEmpty()) {
            if ($email->isIncorrect()) $errors[] = self::E_INCORRECT_EMAIL;
        }

        /** @var NameField $name */ $name = $post['name'];
        if ($name->isIncorrect()) $errors[] = self::E_INCORRECT_NAME;

        /** @var NameField $surname */ $surname = $post['surname'];
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
        $user->login = $post['login']->get();
        $user->password = Encoder::getPassword($post['password']->get());
        $user->sid = Encoder::getSid($user->login, $user->password);
        $user->email = $post['email']->get();
        $user->name = $post['name']->get();
        $user->surname = $post['surname']->get();
        $user->gender_id = $post['gender_id']->get();
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
        $prevRouter = prev_route::get();
        if ($prevRouter && $prevRouter->isInNamespace('admin'))
            return "/admin/users/profile/{$this->login}";
        else return '';
    }
}