<?php namespace engine\users\actions\fields;

use frame\actions\fields\IntegerField;
use engine\users\Gender;
use frame\route\InitRoute;

class GenderField extends IntegerField
{
    /** @throws HttpError NOT_FOUND */
    public function requireDefined()
    {
        InitRoute::require(Gender::selectIdentity($this->get()) !== null);
    }
}