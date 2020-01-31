<?php namespace frame\actions\fields;

class BaseField
{
    private $value;

    /**
     * If the value has not been recieved from a form and it has a default value for
     * that case, returns it. Otherwise returns null.
     * 
     * @return static|null
     */
    public static function createDefault()
    {
        return null;
    }

    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Whether save (transmit) this field.
     */
    public function canBeSaved(): bool
    {
        return true;
    }

    public function get()
    {
        return $this->value;
    }
}