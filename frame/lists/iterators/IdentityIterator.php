<?php namespace frame\lists\iterators;

use frame\database\QueryResult;
use frame\database\Identity;

class IdentityIterator extends QueryResultIterator
{
    private $class;

    /**
     * @throws Exception if $identityClass is not an Identity
     */
    public function __construct(QueryResult $query, string $identityClass)
    {
        if (!is_subclass_of($identityClass, Identity::class))
            throw new \Exception("The '$identityClass' is not an Identity");

        parent::__construct($query);
        $this->class = $identityClass;
    }

    /**
     * @return Identity
     */
    protected function createItem(array $line)
    {
        $identity = $this->class;
        return new $identity($line);
    }

    /**
     * @param Identity $current
     * @return int
     */
    protected function createKey($current, array $line)
    {
        return $current->id;
    }
}