<?php
namespace Queryyetsimple\Router\Match;

use Queryyetsimple\Http\Request;
use Queryyetsimple\Router\Router;

class PathInfo
{

    /**
     * 匹配路由与请求
     *
     * @param \Queryyetsimple\Router\Router $route
     * @param \Queryyetsimple\Http\Request $request
     * @return array
     */
    public function matche(Router $router, Request $request)
    {
        $result = [];

        $sPathInfo = $request->pathInfo();
        $arrPaths = explode('/', trim($sPathInfo, '/'));

        $apps = $router->getOption('apps');
        if (is_array($apps) && in_array($arrPaths[0], $apps)) {
            $result[Router::APP] = array_shift($arrPaths);
        }

        // 控制器名称
        if (isset($_GET[Router::CONTROLLER])) {
            $result[Router::CONTROLLER] = $_GET[Router::CONTROLLER];
        }

        // 方法名称
        if (isset($_GET[Router::ACTION])) {
            $result[Router::ACTION] = $_GET[Router::ACTION];
        }

        $argsProtected = $router->getOption('args_protected');
        $argsRegex = $router->getOption('args_regex');
        $argsStrict = $router->getOption('args_strict');

        for ($nI = 0, $nCnt = count($arrPaths); $nI < $nCnt; $nI ++) {
            if (is_numeric($arrPaths[$nI]) || in_array($arrPaths[$nI], $argsProtected) || $this->matchArgs($arrPaths[$nI], $argsRegex, $argsStrict)) {
                $result[Router::ARGS][] = $arrPaths[$nI];
            } else {
                if (! isset($result[Router::CONTROLLER])) {
                    $result[Router::CONTROLLER] = $arrPaths[$nI];
                } elseif (! isset($result[Router::ACTION])) {
                    $result[Router::ACTION] = $arrPaths[$nI];
                } else {
                    if (isset($arrPaths[$nI + 1])) {
                        $result[$arrPaths[$nI]] = (string) $arrPaths[++ $nI];
                    } else {
                        $result[Router::ARGS][] = $arrPaths[$nI];
                    }
                }
            }
        }

        return $result;
    }

    /**
     * 是否匹配参数正则
     *
     * @param array $strValue
     * @param array $arrRegex
     * @param bool $argsStrict
     * @return boolean
     */
    protected function matchArgs($strValue, array $arrRegex = [], bool $argsStrict)
    {
        if (! $arrRegex) {
            return false;
        }

        foreach ($arrRegex as $strRegex) {
            $strRegex = sprintf('/^(%s)%s/', $strRegex, $argsStrict ? '$' : '');
            if (preg_match($strRegex, $strValue, $arrRes)) {
                return true;
            }
        }

        return false;
    }
}
