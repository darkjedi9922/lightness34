<?php namespace engine\comments\actions;

use frame\actions\ActionBody;
use frame\actions\fields\IntegerField;
use engine\comments\Comment;
use engine\comments\CommentsModule;
use frame\tools\Init;
use frame\modules\Modules;
use engine\users\cash\my_rights;

class DeleteComment extends ActionBody
{
    /** @var Comment */
    private $comment;

    public function listGet(): array
    {
        return [
            'id' => IntegerField::class
        ];
    }

    public function initialize(array $get)
    {
        /** @var IntegerField $id */
        $id = $get['id'];
        $comment = Comment::selectIdentity($id->get());
        Init::require($comment !== null);
        
        /** @var CommentsModule|null $module */
        $module = Modules::getDriver()->findById($comment->module_id);
        Init::require($module !== null);
        
        $rights = my_rights::get($module->getName());
        Init::access($rights->canOneOf([
            'delete-all' => null,
            'delete-own' => [$comment]
        ]));

        $this->comment = $comment;
    }

    public function succeed(array $post, array $files)
    {
        $this->comment->delete();
    }
}