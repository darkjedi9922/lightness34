<?php namespace tests\engine;

class JsonValidatedConfiguredAction extends JsonValidatedAction
{
    protected function getConfig(): array
    {
        return [
            "get" => [
                "user_id" => [
                    "rules" => [
                        "base/emptiness" => true
                    ],
                    "default" => ["some-user"]
                ]
            ],
            "post" => [
                "username" => [
                    "rules" => [
                        "base/mandatory" => true,
                        "base/emptiness" => false,
                        "base/min-length" => 4,
                        "base/max-length" => 4
                    ],
                    "errorRules" => [
                        "base/max-length"
                    ]
                ],
                "alter" => [
                    "default" => ["Doctor Who", "TARDIS"],
                    "rules" => [
                        "base/mandatory" => true
                    ]
                ],
                "enemy" => [
                    "default" => ["Dalek"]
                ]
            ],
            "files" => [
                "avatar" => [
                    "default" => ["no-avatar.jpg"],
                    "rules" => [
                        "file/must-load" => false
                    ]
                ]
            ]
        ];
    }
}