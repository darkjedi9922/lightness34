<?php namespace engine\admin\actions;

use engine\users\Encoder;
use engine\users\Group;
use frame\actions\ActionBody;
use frame\cash\config;
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
            'current-password' => self::POST_PASSWORD,
            'new-password' => self::POST_PASSWORD
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

        $currentPassword = Encoder::getPassword($post['current-password']);
        if ($currentPassword !== $this->config->password)
            $errors[] = self::E_WRONG_CURRENT_PASSWORD;

        $newPassword = $post['new-password'];
        if (empty($newPassword)) $errors[] = self::E_EMPTY_NEW_PASSWORD;

        return $errors;
    }

    public function succeed(array $post, array $files)
    {
        $this->config->password = Encoder::getPassword($post['new-password']);
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