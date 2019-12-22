<?php namespace engine\statistics\macros;

use frame\macros\Macro;
use engine\statistics\stats\RouteStat;

use function lightlib\encode_specials;

class CollectErrorRouteStat extends Macro
{
    private $stat;

    public function __construct(RouteStat $stat)
    {
        $this->stat = $stat;
    }

    public function exec(...$args)
    {
        /** @var \Throwable $error */
        $error = $args[0];
        $this->stat->code_info = encode_specials($error->getMessage());
    }
}