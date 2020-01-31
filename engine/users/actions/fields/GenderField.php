<?php namespace engine\users\actions\fields;

use frame\actions\fields\IntegerField;
use engine\users\Gender;
use frame\tools\Init;

class GenderField extends IntegerField
{
    /** @throws HttpError NOT_FOUND */
    public function requireDefined()
    {
        Init::require(Gender::selectIdentity($this->get()) !== null);
    }
}