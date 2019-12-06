<?php namespace frame\actions;

use frame\tools\Client;
use frame\errors\HttpError;

class ActionToken
{
    const GET_KEY = '_csrf';

    private $action;

    public function __construct(Action $action)
    {
        $this->action = $action;
    }

    public function tokenize()
    {
        $this->action->setData(
            Action::ARGS, 
            self::GET_KEY, 
            $this->getExpectedToken()
        );
    }

    /**
     * @throws HttpError BAD_REQUEST
     */
    public function validate()
    {
        if ($this->getActualToken() !== $this->getExpectedToken())
            throw new HttpError(
                HttpError::BAD_REQUEST,
                'Recieved TOKEN token does not match expected token.'
            );
    }

    public function getActualToken(): ?string
    {
        return $this->action->getData(Action::ARGS, self::GET_KEY);
    }

    public function getExpectedToken(): string
    {
        return md5('tkn_salt' . Client::getId());
    }
}