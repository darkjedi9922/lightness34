<?php namespace engine\comments\actions;

use frame\actions\ActionBody;
use engine\comments\Comment;
use frame\tools\Init;
use frame\modules\Module;
use engine\comments\CommentsModule;
use engine\users\cash\user_me;
use frame\actions\fields\IntegerField;
use frame\actions\fields\StringField;
use frame\modules\Modules;

class AddComment extends ActionBody
{
    /** @var Module */
    private $module;
    /** @var int */
    private $materialId;

    public function listGet(): array
    {
        return [
            'module_id' => IntegerField::class,
            'material_id' => IntegerField::class
        ];
    }

    public function listPost(): array
    {
        return [
            'text' => StringField::class
        ];
    }

    public function initialize(array $get)
    {
        $this->module = Modules::getDriver()->findById($get['module_id']->get());
        Init::require($this->module !== null);
        Init::require(get_class($this->module) === CommentsModule::class);
        Init::accessRight($this->module->getName(), 'add');
        $this->materialId = $get['material_id']->get();
    }

    public function succeed(array $post, array $files): array
    {
        $date = time();

        $comment = new Comment;
        $comment->text = str_replace('\\', '\\\\', $post['text']->get());
        $comment->module_id = $this->module->getId();
        $comment->material_id = $this->materialId;
        $comment->author_id = user_me::get()->id;
        $comment->date = $date;
        $id = $comment->insert();

        return [
            'id' => $id,
            'date' => date('d.m.Y H:i', $date)
        ];
    }
}