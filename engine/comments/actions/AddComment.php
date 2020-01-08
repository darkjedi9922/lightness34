<?php namespace engine\comments\actions;

use frame\actions\ActionBody;
use engine\comments\Comment;
use frame\Core;
use frame\tools\Init;
use frame\modules\Module;
use engine\comments\CommentsModule;
use engine\users\cash\user_me;

class AddComment extends ActionBody
{
    /** @var Module */
    private $module;
    /** @var int */
    private $materialId;

    public function listGet(): array
    {
        return [
            'module_id' => [self::GET_INT, 'A module id of the CommentsModule type'],
            'material_id' => [self::GET_INT, 'A material id to which the comment is added']
        ];
    }

    public function listPost(): array
    {
        return [
            'text' => [self::POST_TEXT, 'A text of the new comment']
        ];
    }

    public function initialize(array $get)
    {
        $this->module = Core::$app->findModule($get['module_id']);
        Init::require($this->module !== null);
        Init::require(get_class($this->module) === CommentsModule::class);
        Init::accessRight($this->module->getName(), 'add');
        $this->materialId = $get['material_id'];
    }

    public function succeed(array $post, array $files): array
    {
        $date = time();

        $comment = new Comment;
        $comment->text = str_replace('\\', '\\\\', $post['text']);
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