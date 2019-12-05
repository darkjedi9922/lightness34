<?php namespace engine\users\actions;

use frame\actions\ActionBody;
use frame\actions\UploadedFile;
use frame\errors\NotImplementedException;
use frame\tools\Init;
use engine\users\User;
use engine\users\Gender;
use frame\cash\config;

use function lightlib\bytes;

abstract class ProfileAction extends ActionBody
{
    const E_NO_LOGIN = 1;
    const E_LONG_LOGIN = 2;
    const E_LOGIN_EXISTS = 3;
    const E_INCORRECT_LOGIN = 4;
    const E_LONG_PASSWORD = 5;
    const E_INCORRECT_PASSWORD = 6;
    const E_INCORRECT_EMAIL = 7;
    const E_INCORRECT_NAME = 8;
    const E_INCORRECT_SURNAME = 9;
    const E_AVATAR_TYPE = 10;
    const E_AVATAR_SIZE = 11;

    /** @var config */
    private $config;

    public function initialize(array $get)
    {
        $this->config = config::get('users');
    }

    public function succeed(array $post, array $files)
    {
        throw new NotImplementedException("This method have to be overrided.");
    }

    protected function validateLogin(string $value, ?string $current = null): array
    {
        $errors = [];

        if ($current !== null && $value === $current) return $errors;

        if (!$value) {
            $errors[] = static::E_NO_LOGIN;
            return $errors;
        }
        
        if (strlen($value) > $this->config->{'login.max_length'})
            $errors[] = static::E_LONG_LOGIN;

        if (preg_match('/[^a-zA-Z0-9-_]/', $value)) {
            $errors[] = static::E_INCORRECT_LOGIN;
            return $errors;
        }

        if (User::select(['login' => $value]) !== null)
            $errors[] = static::E_LOGIN_EXISTS;

        return $errors;
    }

    protected function validatePassword(string $value): array
    {
        $errors = [];

        if (strlen($value) > $this->config->{'password.max_length'})
            $errors[] = static::E_LONG_PASSWORD;

        if (preg_match('/[^a-zA-Z0-9]/', $value)) 
            $errors[] = static::E_INCORRECT_PASSWORD;
    
        return $errors;
    }

    protected function validateEmail(string $value, ?string $current = null): array
    {
        $errors = [];

        if ($current !== null && $value === $current) return $errors;

        if (!preg_match('/^[-._a-z0-9]+@(?:[a-z0-9][-a-z0-9]+\.)+[a-z]{2,6}$/', 
            $value)) 
        {
            $errors[] = static::E_INCORRECT_EMAIL;
        }
    
        return $errors;
    }

    protected function validateName(string $value, ?string $current = null): array
    {
        $errors = [];

        if ($current !== null && $value === $current) return $errors;;

        if (preg_match('/[^a-zA-ZА-Яа-я]/u', $value))
            $errors[] = static::E_INCORRECT_NAME;
    
        return $errors;
    }

    protected function validateSurname(string $value, ?string $current = null): array
    {
        $errors = [];

        if ($current !== null && $value === $current) return $errors;;

        if (preg_match('/[^a-zA-ZА-Яа-я]/u', $value))
            $errors[] = static::E_INCORRECT_SURNAME;

        return $errors;
    }

    protected function validateGender(int $id, ?int $current = null): array
    {
        if ($current !== null && $id === $current) return [];
        Init::require(Gender::selectIdentity($id) !== null);
        return [];
    }

    protected function validateAvatar(UploadedFile $avatar): array
    {
        $errors = [];

        $maxByteSize = bytes(
            $this->config->{'avatar.max_size.value'},
            $this->config->{'avatar.max_size.unit'}
        );
        if ($avatar->hasSizeError($maxByteSize)) {
            $errors[] = static::E_AVATAR_SIZE;
            return $errors;
        }
        
        if (!in_array($avatar->getMime(), [
            'image/jpg',
            'image/jpeg',
            'image/png',
            'image/gif'
        ])) {
            $errors[] = static::E_AVATAR_TYPE;
        }

        return $errors;
    }
}