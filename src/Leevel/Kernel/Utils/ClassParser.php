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

namespace Leevel\Kernel\Utils;

/**
 * 从文件获取类名字.
 * 直接从文章拷贝过来.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.02.28
 *
 * @version 1.0
 *
 * @see http://jarretbyrne.com/2015/06/197/
 *
 * @codeCoverageIgnore
 */
class ClassParser
{
    /**
     * 从文件读取类名.
     *
     * @param string $pathToFile
     *
     * @return string
     */
    public function handle(string $pathToFile): string
    {
        // Grab the contents of the file
        $contents = file_get_contents($pathToFile);

        // Start with a blank namespace and class
        $namespace = $className = '';

        // Set helper values to know that we have found the namespace/class token and need to collect the string values after them
        $gettingNamespace = $gettingClass = false;

        // Go through each token and evaluate it as necessary
        foreach (token_get_all($contents) as $token) {
            // If this token is the namespace declaring, then flag that the next tokens will be the namespace name
            if (is_array($token) && T_NAMESPACE === $token[0]) {
                $gettingNamespace = true;
            }

            // If this token is the class declaring, then flag that the next tokens will be the class name
            if (is_array($token) && T_CLASS === $token[0]) {
                $gettingClass = true;
            }

            // While we're grabbing the namespace name...
            if (true === $gettingNamespace) {
                // If the token is a string or the namespace separator...
                if (is_array($token) && in_array($token[0], [T_STRING, T_NS_SEPARATOR], true)) {
                    // Append the token's value to the name of the namespace
                    $namespace .= $token[1];
                } elseif (';' === $token) {
                    // If the token is the semicolon, then we're done with the namespace declaration
                    $gettingNamespace = false;
                }
            }

            // While we're grabbing the class name...
            if (true === $gettingClass) {
                // If the token is a string, it's the name of the class
                if (is_array($token) && T_STRING === $token[0]) {
                    // Store the token's value as the class name
                    $className = $token[1];

                    // Got what we need, stope here
                    break;
                }
            }
        }

        // Build the fully-qualified class name and return it
        return $namespace ? $namespace.'\\'.$className : $className;
    }
}
