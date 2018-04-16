<?php /* 2018-04-11 10:00:03 */ ?>
<?php return array (
  'basepaths' => 
  array (
    0 => '/v2',
  ),
  'groups' => 
  array (
    0 => '/pet',
  ),
  'routers' => 
  array (
    'get' => 
    array (
      'p' => 
      array (
        '/pet' => 
        array (
          '/v2/pet/findByStatus' => 
          array (
            'scheme' => NULL,
            'domain' => '',
            'params' => 
            array (
              'args1' => 'hello',
              'args2' => 'world',
            ),
            'where' => 
            array (
              'hello' => '[0-9]+',
              'world' => '[A-Za-z]+',
            ),
            'strict' => true,
            'bind' => ':Tests\\Petstore\\Pet\\findByStatus',
            'regex' => '/^\\/v2\\/pet\\/findByStatus$/',
            'var' => 
            array (
            ),
          ),
          '/v2/pet/{petId}' => 
          array (
            'scheme' => NULL,
            'domain' => NULL,
            'params' => NULL,
            'where' => NULL,
            'strict' => NULL,
            'bind' => ':Tests\\Petstore\\Pet\\getPetById',
            'regex' => '/^\\/v2\\/pet\\/(\\S+)$/',
            'var' => 
            array (
              0 => 'petId',
            ),
          ),
        ),
      ),
    ),
    'delete' => 
    array (
      'p' => 
      array (
        '/pet' => 
        array (
          '/v2/pet/{petId}' => 
          array (
            'scheme' => NULL,
            'domain' => NULL,
            'params' => NULL,
            'where' => NULL,
            'strict' => NULL,
            'bind' => ':Tests\\Petstore\\Pet\\deletePet',
            'regex' => '/^\\/v2\\/pet\\/(\\S+)$/',
            'var' => 
            array (
              0 => 'petId',
            ),
          ),
        ),
      ),
    ),
    'post' => 
    array (
      'p' => 
      array (
        '/pet' => 
        array (
          '/v2/pet/{petId}' => 
          array (
            'scheme' => NULL,
            'domain' => NULL,
            'params' => NULL,
            'where' => NULL,
            'strict' => NULL,
            'bind' => ':Tests\\Petstore\\Pet\\updatePetWithForm',
            'regex' => '/^\\/v2\\/pet\\/(\\S+)$/',
            'var' => 
            array (
              0 => 'petId',
            ),
          ),
          '/v2/pet' => 
          array (
            'scheme' => NULL,
            'domain' => NULL,
            'params' => NULL,
            'where' => NULL,
            'strict' => NULL,
            'bind' => ':Tests\\Petstore\\Pet\\addPet',
            'regex' => '/^\\/v2\\/pet$/',
            'var' => 
            array (
            ),
          ),
          '/v2/pet/{petId}/uploadImage' => 
          array (
            'scheme' => NULL,
            'domain' => NULL,
            'params' => NULL,
            'where' => NULL,
            'strict' => NULL,
            'bind' => ':Tests\\Petstore\\Pet\\uploadFile',
            'regex' => '/^\\/v2\\/pet\\/(\\S+)\\/uploadImage$/',
            'var' => 
            array (
              0 => 'petId',
            ),
          ),
        ),
      ),
    ),
  ),
); ?>