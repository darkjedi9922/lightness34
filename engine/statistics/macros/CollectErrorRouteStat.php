<?php namespace engine\statistics\macros;

use engine\statistics\stats\RouteStat;

use function lightlib\encode_specials;

class CollectErrorRouteStat extends BaseStatCollector
{
    private $stat;

    public function __construct(RouteStat $stat)
    {
        $this->stat = $stat;
    }

    protected function collect(...$args)
    {
        /** @var \Throwable $error */
        $error = $args[0];
        $this->stat->code_info = encode_specials($error->getMessage());
    }
}