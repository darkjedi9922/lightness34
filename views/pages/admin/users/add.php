<?php /** @var frame\views\Page $self */

use engine\users\actions\AddUser;
use engine\users\Gender;
use frame\actions\ViewAction;
use frame\stdlib\cash\config;
use frame\lists\base\IdentityList;
use frame\tools\JsonEncoder;
use frame\auth\InitAccess;

InitAccess::accessRight('users', 'add');
$add = new ViewAction(AddUser::class);

$genders = [];
foreach (new IdentityList(Gender::class) as $gender) {
    $genders[] = [
        'label' => $gender->name,
        'value' => $gender->id
    ];
}

$config = config::get('users');
$avatarMaxSizeValue = $config->{'avatar.max_size.value'};
$avatarMaxSizeUnit = $config->{'avatar.max_size.unit'};

$pageProps = [
    'form' => [
        'url' => $add->getUrl(),
        'genders' => [
            'values' => $genders,
            'currentValue' => 1
        ],
        'errorMap' => [
            AddUser::E_NO_LOGIN => [
                'hint' => 'Логин не указан',
                'bindField' => 'login'
            ],
            AddUser::E_LONG_LOGIN => [
                'hint' => 'Логин слишком длинный',
                'bindField' => 'login'
            ],
            AddUser::E_INCORRECT_LOGIN => [
                'hint' => 'Логин содержит недопустимые символы',
                'bindField' => 'login'
            ],
            AddUser::E_LOGIN_EXISTS => [
                'hint' => 'Такой логин уже занят',
                'bindField' => 'login'
            ],
            AddUser::E_NO_PASSWORD => [
                'hint' => 'Пароль не указан',
                'bindField' => 'password'
            ],
            AddUser::E_LONG_PASSWORD => [
                'hint' => 'Пароль слишком длинный',
                'bindField' => 'password'
            ],
            AddUser::E_INCORRECT_PASSWORD => [
                'hint' => 'Пароль содержит недопустимые символы',
                'bindField' => 'password'
            ],
            AddUser::E_INCORRECT_EMAIL => [
                'hint' => 'Email указан некорректно',
                'bindField' => 'email'
            ],
            AddUser::E_INCORRECT_NAME => [
                'hint' => 'Имя содержит недопустимые символы',
                'bindField' => 'name'
            ],
            AddUser::E_INCORRECT_SURNAME => [
                'hint' => 'Фамилия содержит недопустимые символы',
                'bindField' => 'surname'
            ],
            AddUser::E_AVATAR_SIZE => [
                'hint' => "Размер превышает $avatarMaxSizeValue $avatarMaxSizeUnit",
                'bindField' => 'avatar'
            ],
            AddUser::E_AVATAR_TYPE => [
                'hint' => 'Некорректный тип файла',
                'bindField' => 'avatar'
            ]
        ]
    ]
];
$pageProps = JsonEncoder::forHtmlAttribute($pageProps);
?>

<div id="add-user-page" data-props="<?= $pageProps ?>"></div>