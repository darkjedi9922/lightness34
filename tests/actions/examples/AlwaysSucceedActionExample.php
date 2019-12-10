<?php namespace tests\actions\examples;

use frame\actions\ActionBody;

class AlwaysSucceedActionExample extends ActionBody
{
    public function succeed(array $post, array $files): array
    {
        return ['resultAnswer' => 42];
    }
}