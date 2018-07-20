<?php

declare(strict_types=1);

/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Router;

use Leevel\Router\IRouter;
use Leevel\Router\MiddlewareParser;
use Leevel\Router\SwaggerRouter;
use Tests\TestCase;

/**
 * swagger 生成注解路由组件测试.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.04.10
 *
 * @version 1.0
 */
class SwaggerRouterTest extends TestCase
{
    public function testSwaggerHandle()
    {
        $swaggerRouter = new SwaggerRouter($this->createMiddlewareParser(), 'queryphp.cn', 'Tests\Router');

        $scanDir = __DIR__.'/Petstore';

        $swaggerRouter->addSwaggerScan($scanDir);
        $result = $swaggerRouter->handle();

        $data = <<<'eot'
array (
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
      'static' => 
      array (
        '/v2/pet/findByStatus' => 
        array (
          'scheme' => NULL,
          'domain' => '{suddomain:[A-Za-z]+}-vip.{domain}.queryphp.cn',
          'params' => 
          array (
            'args1' => 'hello',
            'args2' => 'world',
          ),
          'strict' => true,
          'bind' => '/blog/list?arg1=1&arg2=2',
          'middlewares' => 
          array (
            'handle' => 
            array (
            ),
            'terminate' => 
            array (
            ),
          ),
          'domain_regex' => '/^([A-Za-z]+)\\-vip\\.(\\S+)\\.queryphp\\.cn$/',
          'domain_var' => 
          array (
            0 => 'suddomain',
            1 => 'domain',
          ),
          'regex' => NULL,
          'var' => NULL,
        ),
        '/v2/test/hello4' => 
        array (
          'scheme' => NULL,
          'domain' => 'www.queryphp.cn',
          'params' => NULL,
          'strict' => NULL,
          'bind' => NULL,
          'middlewares' => NULL,
          'domain_regex' => NULL,
          'domain_var' => NULL,
          'regex' => NULL,
          'var' => NULL,
        ),
      ),
      'p' => 
      array (
        '/pet' => 
        array (
          '/v2/pet/{petId:[A-Za-z]+}' => 
          array (
            'scheme' => NULL,
            'domain' => 'www.queryphp.cn',
            'params' => NULL,
            'strict' => NULL,
            'bind' => 'test/handle2',
            'middlewares' => NULL,
            'domain_regex' => NULL,
            'domain_var' => NULL,
            'var' => 
            array (
              0 => 'petId',
            ),
          ),
          '/v2/pet/hello/{petId:[A-Za-z]+}' => 
          array (
            'scheme' => NULL,
            'domain' => 'www.queryphp.cn',
            'params' => NULL,
            'strict' => NULL,
            'bind' => 'test/handle2',
            'middlewares' => NULL,
            'domain_regex' => NULL,
            'domain_var' => NULL,
            'var' => 
            array (
              0 => 'petId',
            ),
          ),
          '/v2/pet/hello1/{petId:[A-Za-z]+}' => 
          array (
            'scheme' => NULL,
            'domain' => 'www.queryphp.cn',
            'params' => NULL,
            'strict' => NULL,
            'bind' => 'test/handle2',
            'middlewares' => NULL,
            'domain_regex' => NULL,
            'domain_var' => NULL,
            'var' => 
            array (
              0 => 'petId',
            ),
          ),
          '/v2/pet/hello2/{petId:[A-Za-z]+}' => 
          array (
            'scheme' => NULL,
            'domain' => 'www.queryphp.cn',
            'params' => NULL,
            'strict' => NULL,
            'bind' => 'test/handle2',
            'middlewares' => NULL,
            'domain_regex' => NULL,
            'domain_var' => NULL,
            'var' => 
            array (
              0 => 'petId',
            ),
          ),
          '/v2/pet/hello3/{petId:[A-Za-z]+}' => 
          array (
            'scheme' => NULL,
            'domain' => 'www.queryphp.cn',
            'params' => NULL,
            'strict' => NULL,
            'bind' => 'test/handle2',
            'middlewares' => NULL,
            'domain_regex' => NULL,
            'domain_var' => NULL,
            'var' => 
            array (
              0 => 'petId',
            ),
          ),
          '/v2/pet/hello4/{petId:[A-Za-z]+}' => 
          array (
            'scheme' => NULL,
            'domain' => 'www.queryphp.cn',
            'params' => NULL,
            'strict' => NULL,
            'bind' => 'test/handle2',
            'middlewares' => NULL,
            'domain_regex' => NULL,
            'domain_var' => NULL,
            'var' => 
            array (
              0 => 'petId',
            ),
          ),
          '/v2/pet/hello5/{petId:[A-Za-z]+}' => 
          array (
            'scheme' => NULL,
            'domain' => 'www.queryphp.cn',
            'params' => NULL,
            'strict' => NULL,
            'bind' => 'test/handle2',
            'middlewares' => NULL,
            'domain_regex' => NULL,
            'domain_var' => NULL,
            'var' => 
            array (
              0 => 'petId',
            ),
          ),
          '/v2/pet/hello6/{petId:[A-Za-z]+}' => 
          array (
            'scheme' => NULL,
            'domain' => 'www.queryphp.cn',
            'params' => NULL,
            'strict' => NULL,
            'bind' => 'test/handle2',
            'middlewares' => NULL,
            'domain_regex' => NULL,
            'domain_var' => NULL,
            'var' => 
            array (
              0 => 'petId',
            ),
          ),
          '/v2/pet/hello7/{petId:[A-Za-z]+}' => 
          array (
            'scheme' => NULL,
            'domain' => 'www.queryphp.cn',
            'params' => NULL,
            'strict' => NULL,
            'bind' => 'test/handle2',
            'middlewares' => NULL,
            'domain_regex' => NULL,
            'domain_var' => NULL,
            'var' => 
            array (
              0 => 'petId',
            ),
          ),
          '/v2/pet/hello8/{petId:[A-Za-z]+}' => 
          array (
            'scheme' => NULL,
            'domain' => 'www.queryphp.cn',
            'params' => NULL,
            'strict' => NULL,
            'bind' => 'test/handle2',
            'middlewares' => NULL,
            'domain_regex' => NULL,
            'domain_var' => NULL,
            'var' => 
            array (
              0 => 'petId',
            ),
          ),
          '/v2/pet/hello9/{petId:[A-Za-z]+}' => 
          array (
            'scheme' => NULL,
            'domain' => 'www.queryphp.cn',
            'params' => NULL,
            'strict' => NULL,
            'bind' => 'test/handle2',
            'middlewares' => NULL,
            'domain_regex' => NULL,
            'domain_var' => NULL,
            'var' => 
            array (
              0 => 'petId',
            ),
          ),
          '/v2/pet/hello10/{petId:[A-Za-z]+}' => 
          array (
            'scheme' => NULL,
            'domain' => 'www.queryphp.cn',
            'params' => NULL,
            'strict' => NULL,
            'bind' => 'test/handle2',
            'middlewares' => NULL,
            'domain_regex' => NULL,
            'domain_var' => NULL,
            'var' => 
            array (
              0 => 'petId',
            ),
          ),
          '/v2/pet/hello11/{petId:[A-Za-z]+}' => 
          array (
            'scheme' => NULL,
            'domain' => 'www.queryphp.cn',
            'params' => NULL,
            'strict' => NULL,
            'bind' => 'test/handle2',
            'middlewares' => NULL,
            'domain_regex' => NULL,
            'domain_var' => NULL,
            'var' => 
            array (
              0 => 'petId',
            ),
          ),
          '/v2/pet/hello12/{petId:[A-Za-z]+}' => 
          array (
            'scheme' => NULL,
            'domain' => 'www.queryphp.cn',
            'params' => NULL,
            'strict' => NULL,
            'bind' => 'test/handle2',
            'middlewares' => NULL,
            'domain_regex' => NULL,
            'domain_var' => NULL,
            'var' => 
            array (
              0 => 'petId',
            ),
          ),
          'regex' => 
          array (
            0 => '~^(?|/v2/pet/([A-Za-z]+)|/v2/pet/hello/([A-Za-z]+)()|/v2/pet/hello1/([A-Za-z]+)()()|/v2/pet/hello2/([A-Za-z]+)()()()|/v2/pet/hello3/([A-Za-z]+)()()()()|/v2/pet/hello4/([A-Za-z]+)()()()()()|/v2/pet/hello5/([A-Za-z]+)()()()()()()|/v2/pet/hello6/([A-Za-z]+)()()()()()()()|/v2/pet/hello7/([A-Za-z]+)()()()()()()()()|/v2/pet/hello8/([A-Za-z]+)()()()()()()()()())$~x',
            1 => '~^(?|/v2/pet/hello9/([A-Za-z]+)|/v2/pet/hello10/([A-Za-z]+)()|/v2/pet/hello11/([A-Za-z]+)()()|/v2/pet/hello12/([A-Za-z]+)()()())$~x',
          ),
          'map' => 
          array (
            0 => 
            array (
              2 => '/v2/pet/{petId:[A-Za-z]+}',
              3 => '/v2/pet/hello/{petId:[A-Za-z]+}',
              4 => '/v2/pet/hello1/{petId:[A-Za-z]+}',
              5 => '/v2/pet/hello2/{petId:[A-Za-z]+}',
              6 => '/v2/pet/hello3/{petId:[A-Za-z]+}',
              7 => '/v2/pet/hello4/{petId:[A-Za-z]+}',
              8 => '/v2/pet/hello5/{petId:[A-Za-z]+}',
              9 => '/v2/pet/hello6/{petId:[A-Za-z]+}',
              10 => '/v2/pet/hello7/{petId:[A-Za-z]+}',
              11 => '/v2/pet/hello8/{petId:[A-Za-z]+}',
            ),
            1 => 
            array (
              2 => '/v2/pet/hello9/{petId:[A-Za-z]+}',
              3 => '/v2/pet/hello10/{petId:[A-Za-z]+}',
              4 => '/v2/pet/hello11/{petId:[A-Za-z]+}',
              5 => '/v2/pet/hello12/{petId:[A-Za-z]+}',
            ),
          ),
        ),
      ),
      '_' => 
      array (
        '_' => 
        array (
          '/v2/45/{id}' => 
          array (
            'scheme' => NULL,
            'domain' => 'www.queryphp.cn',
            'params' => NULL,
            'strict' => NULL,
            'bind' => NULL,
            'middlewares' => NULL,
            'domain_regex' => NULL,
            'domain_var' => NULL,
            'var' => 
            array (
              0 => 'id',
            ),
          ),
          'regex' => 
          array (
            0 => '~^(?|/v2/45/(\\S+))$~x',
          ),
          'map' => 
          array (
            0 => 
            array (
              2 => '/v2/45/{id}',
            ),
          ),
        ),
      ),
    ),
    'post' => 
    array (
      'static' => 
      array (
        '/v2/pet' => 
        array (
          'scheme' => NULL,
          'domain' => NULL,
          'params' => NULL,
          'strict' => NULL,
          'bind' => 'Petstore/Pet/addPet',
          'middlewares' => NULL,
          'domain_regex' => NULL,
          'domain_var' => NULL,
          'regex' => NULL,
          'var' => NULL,
        ),
      ),
      'p' => 
      array (
        '/pet' => 
        array (
          '/v2/pet/{petId}' => 
          array (
            'scheme' => NULL,
            'domain' => NULL,
            'params' => NULL,
            'strict' => NULL,
            'bind' => 'Petstore/Pet/updatePetWithForm',
            'middlewares' => NULL,
            'domain_regex' => NULL,
            'domain_var' => NULL,
            'var' => 
            array (
              0 => 'petId',
            ),
          ),
          '/v2/pet/{petId}/uploadImage' => 
          array (
            'scheme' => NULL,
            'domain' => NULL,
            'params' => NULL,
            'strict' => NULL,
            'bind' => 'Petstore/Pet/uploadFile',
            'middlewares' => NULL,
            'domain_regex' => NULL,
            'domain_var' => NULL,
            'var' => 
            array (
              0 => 'petId',
            ),
          ),
          'regex' => 
          array (
            0 => '~^(?|/v2/pet/(\\S+)|/v2/pet/(\\S+)/uploadImage())$~x',
          ),
          'map' => 
          array (
            0 => 
            array (
              2 => '/v2/pet/{petId}',
              3 => '/v2/pet/{petId}/uploadImage',
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
            'strict' => NULL,
            'bind' => 'Petstore/Pet/deletePet',
            'middlewares' => NULL,
            'domain_regex' => NULL,
            'domain_var' => NULL,
            'var' => 
            array (
              0 => 'petId',
            ),
          ),
          'regex' => 
          array (
            0 => '~^(?|/v2/pet/(\\S+))$~x',
          ),
          'map' => 
          array (
            0 => 
            array (
              2 => '/v2/pet/{petId}',
            ),
          ),
        ),
      ),
    ),
  ),
)
eot;

        $this->assertSame(
            $data,
            $this->varExport(
                $result
            )
        );
    }

    public function testParseBindBySource()
    {
        $swaggerRouter = new SwaggerRouter($this->createMiddlewareParser(), 'queryphp.cn', 'NotFound\Tests\Router');

        $scanDir = __DIR__.'/Petstore';

        $swaggerRouter->addSwaggerScan($scanDir);
        $result = $swaggerRouter->handle();

        $data = <<<'eot'
array (
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
      'static' => 
      array (
        '/v2/pet/findByStatus' => 
        array (
          'scheme' => NULL,
          'domain' => '{suddomain:[A-Za-z]+}-vip.{domain}.queryphp.cn',
          'params' => 
          array (
            'args1' => 'hello',
            'args2' => 'world',
          ),
          'strict' => true,
          'bind' => '/blog/list?arg1=1&arg2=2',
          'middlewares' => 
          array (
            'handle' => 
            array (
            ),
            'terminate' => 
            array (
            ),
          ),
          'domain_regex' => '/^([A-Za-z]+)\\-vip\\.(\\S+)\\.queryphp\\.cn$/',
          'domain_var' => 
          array (
            0 => 'suddomain',
            1 => 'domain',
          ),
          'regex' => NULL,
          'var' => NULL,
        ),
        '/v2/test/hello4' => 
        array (
          'scheme' => NULL,
          'domain' => 'www.queryphp.cn',
          'params' => NULL,
          'strict' => NULL,
          'bind' => NULL,
          'middlewares' => NULL,
          'domain_regex' => NULL,
          'domain_var' => NULL,
          'regex' => NULL,
          'var' => NULL,
        ),
      ),
      'p' => 
      array (
        '/pet' => 
        array (
          '/v2/pet/{petId:[A-Za-z]+}' => 
          array (
            'scheme' => NULL,
            'domain' => 'www.queryphp.cn',
            'params' => NULL,
            'strict' => NULL,
            'bind' => 'test/handle2',
            'middlewares' => NULL,
            'domain_regex' => NULL,
            'domain_var' => NULL,
            'var' => 
            array (
              0 => 'petId',
            ),
          ),
          '/v2/pet/hello/{petId:[A-Za-z]+}' => 
          array (
            'scheme' => NULL,
            'domain' => 'www.queryphp.cn',
            'params' => NULL,
            'strict' => NULL,
            'bind' => 'test/handle2',
            'middlewares' => NULL,
            'domain_regex' => NULL,
            'domain_var' => NULL,
            'var' => 
            array (
              0 => 'petId',
            ),
          ),
          '/v2/pet/hello1/{petId:[A-Za-z]+}' => 
          array (
            'scheme' => NULL,
            'domain' => 'www.queryphp.cn',
            'params' => NULL,
            'strict' => NULL,
            'bind' => 'test/handle2',
            'middlewares' => NULL,
            'domain_regex' => NULL,
            'domain_var' => NULL,
            'var' => 
            array (
              0 => 'petId',
            ),
          ),
          '/v2/pet/hello2/{petId:[A-Za-z]+}' => 
          array (
            'scheme' => NULL,
            'domain' => 'www.queryphp.cn',
            'params' => NULL,
            'strict' => NULL,
            'bind' => 'test/handle2',
            'middlewares' => NULL,
            'domain_regex' => NULL,
            'domain_var' => NULL,
            'var' => 
            array (
              0 => 'petId',
            ),
          ),
          '/v2/pet/hello3/{petId:[A-Za-z]+}' => 
          array (
            'scheme' => NULL,
            'domain' => 'www.queryphp.cn',
            'params' => NULL,
            'strict' => NULL,
            'bind' => 'test/handle2',
            'middlewares' => NULL,
            'domain_regex' => NULL,
            'domain_var' => NULL,
            'var' => 
            array (
              0 => 'petId',
            ),
          ),
          '/v2/pet/hello4/{petId:[A-Za-z]+}' => 
          array (
            'scheme' => NULL,
            'domain' => 'www.queryphp.cn',
            'params' => NULL,
            'strict' => NULL,
            'bind' => 'test/handle2',
            'middlewares' => NULL,
            'domain_regex' => NULL,
            'domain_var' => NULL,
            'var' => 
            array (
              0 => 'petId',
            ),
          ),
          '/v2/pet/hello5/{petId:[A-Za-z]+}' => 
          array (
            'scheme' => NULL,
            'domain' => 'www.queryphp.cn',
            'params' => NULL,
            'strict' => NULL,
            'bind' => 'test/handle2',
            'middlewares' => NULL,
            'domain_regex' => NULL,
            'domain_var' => NULL,
            'var' => 
            array (
              0 => 'petId',
            ),
          ),
          '/v2/pet/hello6/{petId:[A-Za-z]+}' => 
          array (
            'scheme' => NULL,
            'domain' => 'www.queryphp.cn',
            'params' => NULL,
            'strict' => NULL,
            'bind' => 'test/handle2',
            'middlewares' => NULL,
            'domain_regex' => NULL,
            'domain_var' => NULL,
            'var' => 
            array (
              0 => 'petId',
            ),
          ),
          '/v2/pet/hello7/{petId:[A-Za-z]+}' => 
          array (
            'scheme' => NULL,
            'domain' => 'www.queryphp.cn',
            'params' => NULL,
            'strict' => NULL,
            'bind' => 'test/handle2',
            'middlewares' => NULL,
            'domain_regex' => NULL,
            'domain_var' => NULL,
            'var' => 
            array (
              0 => 'petId',
            ),
          ),
          '/v2/pet/hello8/{petId:[A-Za-z]+}' => 
          array (
            'scheme' => NULL,
            'domain' => 'www.queryphp.cn',
            'params' => NULL,
            'strict' => NULL,
            'bind' => 'test/handle2',
            'middlewares' => NULL,
            'domain_regex' => NULL,
            'domain_var' => NULL,
            'var' => 
            array (
              0 => 'petId',
            ),
          ),
          '/v2/pet/hello9/{petId:[A-Za-z]+}' => 
          array (
            'scheme' => NULL,
            'domain' => 'www.queryphp.cn',
            'params' => NULL,
            'strict' => NULL,
            'bind' => 'test/handle2',
            'middlewares' => NULL,
            'domain_regex' => NULL,
            'domain_var' => NULL,
            'var' => 
            array (
              0 => 'petId',
            ),
          ),
          '/v2/pet/hello10/{petId:[A-Za-z]+}' => 
          array (
            'scheme' => NULL,
            'domain' => 'www.queryphp.cn',
            'params' => NULL,
            'strict' => NULL,
            'bind' => 'test/handle2',
            'middlewares' => NULL,
            'domain_regex' => NULL,
            'domain_var' => NULL,
            'var' => 
            array (
              0 => 'petId',
            ),
          ),
          '/v2/pet/hello11/{petId:[A-Za-z]+}' => 
          array (
            'scheme' => NULL,
            'domain' => 'www.queryphp.cn',
            'params' => NULL,
            'strict' => NULL,
            'bind' => 'test/handle2',
            'middlewares' => NULL,
            'domain_regex' => NULL,
            'domain_var' => NULL,
            'var' => 
            array (
              0 => 'petId',
            ),
          ),
          '/v2/pet/hello12/{petId:[A-Za-z]+}' => 
          array (
            'scheme' => NULL,
            'domain' => 'www.queryphp.cn',
            'params' => NULL,
            'strict' => NULL,
            'bind' => 'test/handle2',
            'middlewares' => NULL,
            'domain_regex' => NULL,
            'domain_var' => NULL,
            'var' => 
            array (
              0 => 'petId',
            ),
          ),
          'regex' => 
          array (
            0 => '~^(?|/v2/pet/([A-Za-z]+)|/v2/pet/hello/([A-Za-z]+)()|/v2/pet/hello1/([A-Za-z]+)()()|/v2/pet/hello2/([A-Za-z]+)()()()|/v2/pet/hello3/([A-Za-z]+)()()()()|/v2/pet/hello4/([A-Za-z]+)()()()()()|/v2/pet/hello5/([A-Za-z]+)()()()()()()|/v2/pet/hello6/([A-Za-z]+)()()()()()()()|/v2/pet/hello7/([A-Za-z]+)()()()()()()()()|/v2/pet/hello8/([A-Za-z]+)()()()()()()()()())$~x',
            1 => '~^(?|/v2/pet/hello9/([A-Za-z]+)|/v2/pet/hello10/([A-Za-z]+)()|/v2/pet/hello11/([A-Za-z]+)()()|/v2/pet/hello12/([A-Za-z]+)()()())$~x',
          ),
          'map' => 
          array (
            0 => 
            array (
              2 => '/v2/pet/{petId:[A-Za-z]+}',
              3 => '/v2/pet/hello/{petId:[A-Za-z]+}',
              4 => '/v2/pet/hello1/{petId:[A-Za-z]+}',
              5 => '/v2/pet/hello2/{petId:[A-Za-z]+}',
              6 => '/v2/pet/hello3/{petId:[A-Za-z]+}',
              7 => '/v2/pet/hello4/{petId:[A-Za-z]+}',
              8 => '/v2/pet/hello5/{petId:[A-Za-z]+}',
              9 => '/v2/pet/hello6/{petId:[A-Za-z]+}',
              10 => '/v2/pet/hello7/{petId:[A-Za-z]+}',
              11 => '/v2/pet/hello8/{petId:[A-Za-z]+}',
            ),
            1 => 
            array (
              2 => '/v2/pet/hello9/{petId:[A-Za-z]+}',
              3 => '/v2/pet/hello10/{petId:[A-Za-z]+}',
              4 => '/v2/pet/hello11/{petId:[A-Za-z]+}',
              5 => '/v2/pet/hello12/{petId:[A-Za-z]+}',
            ),
          ),
        ),
      ),
      '_' => 
      array (
        '_' => 
        array (
          '/v2/45/{id}' => 
          array (
            'scheme' => NULL,
            'domain' => 'www.queryphp.cn',
            'params' => NULL,
            'strict' => NULL,
            'bind' => NULL,
            'middlewares' => NULL,
            'domain_regex' => NULL,
            'domain_var' => NULL,
            'var' => 
            array (
              0 => 'id',
            ),
          ),
          'regex' => 
          array (
            0 => '~^(?|/v2/45/(\\S+))$~x',
          ),
          'map' => 
          array (
            0 => 
            array (
              2 => '/v2/45/{id}',
            ),
          ),
        ),
      ),
    ),
    'post' => 
    array (
      'static' => 
      array (
        '/v2/pet' => 
        array (
          'scheme' => NULL,
          'domain' => NULL,
          'params' => NULL,
          'strict' => NULL,
          'bind' => NULL,
          'middlewares' => NULL,
          'domain_regex' => NULL,
          'domain_var' => NULL,
          'regex' => NULL,
          'var' => NULL,
        ),
      ),
      'p' => 
      array (
        '/pet' => 
        array (
          '/v2/pet/{petId}' => 
          array (
            'scheme' => NULL,
            'domain' => NULL,
            'params' => NULL,
            'strict' => NULL,
            'bind' => NULL,
            'middlewares' => NULL,
            'domain_regex' => NULL,
            'domain_var' => NULL,
            'var' => 
            array (
              0 => 'petId',
            ),
          ),
          '/v2/pet/{petId}/uploadImage' => 
          array (
            'scheme' => NULL,
            'domain' => NULL,
            'params' => NULL,
            'strict' => NULL,
            'bind' => NULL,
            'middlewares' => NULL,
            'domain_regex' => NULL,
            'domain_var' => NULL,
            'var' => 
            array (
              0 => 'petId',
            ),
          ),
          'regex' => 
          array (
            0 => '~^(?|/v2/pet/(\\S+)|/v2/pet/(\\S+)/uploadImage())$~x',
          ),
          'map' => 
          array (
            0 => 
            array (
              2 => '/v2/pet/{petId}',
              3 => '/v2/pet/{petId}/uploadImage',
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
            'strict' => NULL,
            'bind' => NULL,
            'middlewares' => NULL,
            'domain_regex' => NULL,
            'domain_var' => NULL,
            'var' => 
            array (
              0 => 'petId',
            ),
          ),
          'regex' => 
          array (
            0 => '~^(?|/v2/pet/(\\S+))$~x',
          ),
          'map' => 
          array (
            0 => 
            array (
              2 => '/v2/pet/{petId}',
            ),
          ),
        ),
      ),
    ),
  ),
)
eot;

        $this->assertSame(
            $data,
            $this->varExport(
                $result
            )
        );
    }

    public function testAddSwaggerScanCheckDir()
    {
        $this->expectException(\InvalidArgumentException::class);

        $swaggerRouter = new SwaggerRouter($this->createMiddlewareParser());

        $scanDir = __DIR__.'/Petstore__';

        $swaggerRouter->addSwaggerScan($scanDir);
    }

    protected function createMiddlewareParser(): MiddlewareParser
    {
        $router = $this->createMock(IRouter::class);

        $this->assertInstanceof(IRouter::class, $router);

        return new MiddlewareParser($router);
    }
}
