<?php namespace frame\api;

abstract class Api
{
    /** @return mixed|null */
    public abstract function exec();
}