<?php namespace engine\users\actions;

use engine\users\User;
use engine\users\Encoder;
use frame\tools\Init;
use engine\users\cash\user_me;
use frame\actions\fields\IntegerField;
use frame\actions\fields\PasswordField;
use frame\actions\fields\StringField;
use frame\stdlib\drivers\database\MySqlDriver;
use frame\auth\Auth;

/**
 * Параметры: id: id пользователя
 * Он должен существовать.
 * Права: редактирование профиля данного пользователя.
 * Данные:
 * login (не обязательно): новый логин
 * password (не обязательно): новый пароль
 * email (не обязательно): новый email
 * name (не обязательно): новое имя
 * surname (не обязательно): новая фамилия
 * gender_id (не обязательно): id нового пола
 * avatar (не обязательно, input type: file): файл нового аватара
 */
class ProfileEditAction extends ProfileAction
{
    /** @var User */
    private $user = null;
    private $me = null;
    /** @var SqlDriver */
    private $database = null;
    /** @var UploadedFile */
    private $avatar = null;

    public function listGet(): array
    {
        return [
            'id' => IntegerField::class
        ];
    }

    public function listPost(): array
    {
        return [
            'login' => StringField::class,
            'password' => PasswordField::class, // can be empty to left the old value
            'email' => StringField::class,
            'name' => StringField::class,
            'surname' => StringField::class,
            'gender_id' => IntegerField::class
        ];
    }

    public function initialize(array $get)
    {
        parent::initialize($get);

        $this->user = User::selectIdentity($get['id']->get());
        
        Init::require($this->user !== null);
        Init::accessOneRight('users', [
            'edit-all' => [$this->user], 
            'edit-own' => [$this->user]
        ]);

        $this->me = user_me::get();
        $this->database = MySqlDriver::getDriver();
    }

    public function validate(array $post, array $files): array
    {
        $errors = [];

        foreach ($post as $key => $value) {
            $value = $value->get();
            switch ($key) {
                case 'login': 
                    $errors = array_merge(
                        $errors,
                        $this->validateLogin($value, $this->user->login)
                    );
                    break;
                
                case 'password':
                    if ($value) $errors = array_merge(
                        $errors,
                        $this->validatePassword($value)
                    );
                    break;
                
                case 'email':
                    $errors = array_merge(
                        $errors,
                        $this->validateEmail($value, $this->user->email)
                    );
                    break;

                case 'name':
                    $errors = array_merge(
                        $errors,
                        $this->validateName($value, $this->user->name)   
                    );
                    break;

                case 'surname':
                    $errors = array_merge(
                        $errors,
                        $this->validateSurname($value, $this->user->surname)
                    );
                    break;

                case 'gender_id':
                    $errors = array_merge(
                        $errors,
                        $this->validateGender($value, $this->user->gender_id)
                    );
                    break;
            }
        }

        $this->avatar = $files['avatar'];
        if (!$this->avatar->isEmpty()) {
            $errors = array_merge(
                $errors,
                $this->validateAvatar($this->avatar)
            );
        }

        return $errors;
    }

    public function succeed(array $post, array $files)
    {
        foreach ($post as $key => $value) {
            $value = $value->get();
            switch ($key) {
                case 'login': 
                    $this->user->login = $value;
                    break;
                case 'password':
                    if ($value) $this->user->password = Encoder::getPassword($value);
                    break;
                case 'email':
                    $this->user->email = $value;
                    break;
                case 'name':
                    $this->user->name = $value;
                    break;
                case 'surname':
                    $this->user->surname = $value;
                    break;
                case 'gender_id':
                    $this->user->gender_id = $value;
                    break;
            }
        }

        $this->user->sid = Encoder::getSid(
            $this->user->login,
            $this->user->password
        );
        
        if ($this->user->id === user_me::get()->id) {
            $auth = new Auth;
            $auth->login($this->user->sid, $auth->isRemembered());
        }

        $avatar = $files['avatar'];
        if (!$avatar->isEmpty()) {
            if ($this->user->hasAvatar()) unlink($this->user->getAvatarUrl());
            $this->user->avatar = $avatar->moveUnique(User::AVATAR_FOLDER);
        }

        $this->user->update();
    }
}