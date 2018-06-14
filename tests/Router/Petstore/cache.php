<?php /* 2018-04-11 10:00:03 */ ?>
<?php return [
    'basepaths' => [
        0 => '/v2',
    ],
    'groups' => [
        0 => '/pet',
    ],
    'routers' => [
        'get' => [
            'p' => [
                '/pet' => [
                    '/v2/pet/findByStatus' => [
                        'scheme' => null,
                        'domain' => '',
                        'params' => [
                            'args1' => 'hello',
                            'args2' => 'world',
                        ],
                        'where' => [
                            'hello' => '[0-9]+',
                            'world' => '[A-Za-z]+',
                        ],
                        'strict' => true,
                        'bind'   => ':Tests\\Petstore\\Pet\\findByStatus',
                        'regex'  => '/^\\/v2\\/pet\\/findByStatus$/',
                        'var'    => [
                        ],
                    ],
                    '/v2/pet/{petId}' => [
                        'scheme' => null,
                        'domain' => null,
                        'params' => null,
                        'where'  => null,
                        'strict' => null,
                        'bind'   => ':Tests\\Petstore\\Pet\\getPetById',
                        'regex'  => '/^\\/v2\\/pet\\/(\\S+)$/',
                        'var'    => [
                            0 => 'petId',
                        ],
                    ],
                ],
            ],
        ],
        'delete' => [
            'p' => [
                '/pet' => [
                    '/v2/pet/{petId}' => [
                        'scheme' => null,
                        'domain' => null,
                        'params' => null,
                        'where'  => null,
                        'strict' => null,
                        'bind'   => ':Tests\\Petstore\\Pet\\deletePet',
                        'regex'  => '/^\\/v2\\/pet\\/(\\S+)$/',
                        'var'    => [
                            0 => 'petId',
                        ],
                    ],
                ],
            ],
        ],
        'post' => [
            'p' => [
                '/pet' => [
                    '/v2/pet/{petId}' => [
                        'scheme' => null,
                        'domain' => null,
                        'params' => null,
                        'where'  => null,
                        'strict' => null,
                        'bind'   => ':Tests\\Petstore\\Pet\\updatePetWithForm',
                        'regex'  => '/^\\/v2\\/pet\\/(\\S+)$/',
                        'var'    => [
                            0 => 'petId',
                        ],
                    ],
                    '/v2/pet' => [
                        'scheme' => null,
                        'domain' => null,
                        'params' => null,
                        'where'  => null,
                        'strict' => null,
                        'bind'   => ':Tests\\Petstore\\Pet\\addPet',
                        'regex'  => '/^\\/v2\\/pet$/',
                        'var'    => [
                        ],
                    ],
                    '/v2/pet/{petId}/uploadImage' => [
                        'scheme' => null,
                        'domain' => null,
                        'params' => null,
                        'where'  => null,
                        'strict' => null,
                        'bind'   => ':Tests\\Petstore\\Pet\\uploadFile',
                        'regex'  => '/^\\/v2\\/pet\\/(\\S+)\\/uploadImage$/',
                        'var'    => [
                            0 => 'petId',
                        ],
                    ],
                ],
            ],
        ],
    ],
]; ?>