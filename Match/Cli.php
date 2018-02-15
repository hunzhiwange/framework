<?php

namespace Queryyetsimple\Router\Match;

use Queryyetsimple\Http\Request;
use Queryyetsimple\Router\Router;
use Queryyetsimple\Console\Cli as ConsoleCli;

class Cli
{

    /**
     * 匹配路径
     *
     * @param \Queryyetsimple\Router\Router $route
     * @param \Queryyetsimple\Http\Request $request
     * @return array
     */
    public function matche(Router $router, Request $request)
    { 
        list($node, $querys, $options) = (new ConsoleCli)->parse();

        $result = [];

        if ($node) {
            $result = $router->parseNodeUrl($node);
        }

        if ($querys) {
            $result = array_merge($result, $querys);
        }

        $result[Router::ARGS] = $options;

        return $result;
    }
}
