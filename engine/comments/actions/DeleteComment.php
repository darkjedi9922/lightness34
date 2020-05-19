<?php namespace engine\comments\actions;

use frame\actions\ActionBody;
use frame\actions\fields\IntegerField;
use engine\comments\Comment;
use engine\comments\CommentsModule;
use frame\auth\InitAccess;
use frame\route\InitRoute;
use frame\modules\Modules;
use engine\users\User;

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
        InitRoute::require($comment !== null);
        
        /** @var CommentsModule|null $module */
        $module = Modules::getDriver()->findById($comment->module_id);
        InitRoute::require($module !== null);
        
        $rights = User::getMyRights($module->getName());
        InitAccess::access($rights->canOneOf([
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