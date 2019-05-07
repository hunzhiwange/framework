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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Kernel\Bootstrap;

use Exception;
use Leevel\I18n\I18n;
use Leevel\I18n\II18n;
use Leevel\I18n\Load;
use Leevel\Kernel\IApp;

/**
 * 读取语言包.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.05.03
 *
 * @version 1.0
 */
class LoadI18n
{
    /**
     * 响应.
     *
     * @param \Leevel\Kernel\IApp $app
     */
    public function handle(IApp $app): void
    {
        $i18nDefault = $app
            ->container()
            ->make('option')
            ->get('i18n\\default');

        if ($app->isCachedI18n($i18nDefault)) {
            $data = (array) include $app->i18nCachedPath($i18nDefault);
        } else {
            $load = (new Load([$app->i18nPath()]))
                ->setI18n($i18nDefault)
                ->addDir($this->getExtend($app));

            $data = $load->loadData();
        }

        $app
            ->container()
            ->instance('i18n', $i18n = new I18n($i18nDefault));

        $app
            ->container()
            ->alias('i18n', [II18n::class, I18n::class]);

        $i18n->addtext($i18nDefault, $data);
    }

    /**
     * 获取扩展语言包.
     *
     * @param \Leevel\Kernel\IApp $app
     *
     * @return array
     */
    public function getExtend(IApp $app): array
    {
        $extend = $app
            ->container()
            ->make('option')
            ->get('_composer.i18ns', []);

        $path = $app->path();

        $extend = array_map(function (string $item) use ($path) {
            if (!is_file($item)) {
                $item = $path.'/'.$item;
            }

            if (!is_dir($item)) {
                $e = sprintf('I18n dir %s is not exist.', $item);

                throw new Exception($e);
            }

            return $item;
        }, $extend);

        return $extend;
    }
}
