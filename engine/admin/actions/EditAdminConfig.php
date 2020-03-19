<?php namespace engine\admin\actions;

use engine\users\Encoder;
use engine\users\Group;
use frame\actions\ActionBody;
use frame\actions\fields\PasswordField;
use frame\stdlib\cash\config;
use frame\tools\Init;
use frame\config\Json;

class EditAdminConfig extends ActionBody
{
    const E_WRONG_CURRENT_PASSWORD = 1;
    const E_EMPTY_NEW_PASSWORD = 2;

    /** @var Json */
    private $config;

    public function listPost(): array
    {
        return [
            'current-password' => PasswordField::class,
            'new-password' => PasswordField::class
        ];
    }

    public function initialize(array $get)
    {
        Init::accessGroup(Group::ROOT_ID);
        $this->config = config::get('admin');
    }

    public function validate(array $post, array $files): array
    {
        $errors = [];

        $currentPassword = Encoder::getPassword($post['current-password']->get());
        if ($currentPassword !== $this->config->password)
            $errors[] = self::E_WRONG_CURRENT_PASSWORD;

        /** @var PasswordField $newPassword */ $newPassword = $post['new-password'];
        if ($newPassword->isEmpty()) $errors[] = self::E_EMPTY_NEW_PASSWORD;

        return $errors;
    }

    public function succeed(array $post, array $files)
    {
        $this->config->password = Encoder::getPassword($post['new-password']->get());
        $this->config->save();
    }

    public function getSuccessRedirect(): ?string
    {
        return null;
    }

    public function getFailRedirect(): ?string
    {
        return null;
    }
}