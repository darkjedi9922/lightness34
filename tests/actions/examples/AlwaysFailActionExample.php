<?php namespace tests\actions\examples;

use frame\actions\ActionBody;

class AlwaysFailActionExample extends ActionBody
{
    public function validate(array $post, array $files): array
    {
        // Return some error to always fail.
        return [12];
    }

    public function succeed(array $post, array $files): array
    {
        // We will never get here.
        return [];
    }

    public function fail(array $post, array $files): array
    {
        return ['doctor' => 'exterminate!'];
    }
}