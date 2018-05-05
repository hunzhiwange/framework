<?php declare(strict_types=1);
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
namespace Leevel\I18n\Translations;

use Exception;
use Leevel\Support\IArray;

/**
 * translations.gettext
 * This class borrows heavily from the Wordpress and is part of the Wordpress package
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.09.18
 * @link https://github.com/WordPress/WordPress/blob/master/wp-includes/pomo/
 * @version 1.0
 */
class Gettext extends Translations implements IArray
{

    /**
     * The gettext implementation of select_plural_form.
     *
     * It lives in this class, because there are more than one descendand, which will use it and
     * they can't share it effectively.
     *
     * @param int $count
     */
    public function gettext_select_plural_form($count)
    {
        if (! isset($this->_gettext_select_plural_form) || is_null($this->_gettext_select_plural_form)) {
            list($nplurals, $expression) = $this->nplurals_and_expression_from_header($this->get_header('Plural-Forms'));
            $this->_nplurals = $nplurals;
            $this->_gettext_select_plural_form = $this->make_plural_form_function($nplurals, $expression);
        }
        return call_user_func($this->_gettext_select_plural_form, $count);
    }

    /**
     *
     * @param string $header
     * @return array
     */
    public function nplurals_and_expression_from_header($header)
    {
        if (preg_match('/^\s*nplurals\s*=\s*(\d+)\s*;\s+plural\s*=\s*(.+)$/', $header, $matches)) {
            $nplurals = ( int ) $matches[1];
            $expression = trim($this->parenthesize_plural_exression($matches[2]));
            return array(
                $nplurals,
                $expression
            );
        } else {
            return array(
                2,
                'n != 1'
            );
        }
    }

    /**
     * Makes a function, which will return the right translation index, according to the
     * plural forms header
     *
     * @param int $nplurals
     * @param string $expression
     */
    public function make_plural_form_function($nplurals, $expression)
    {
        $expression = str_replace('n', '$n', $expression);
        $func_body = "
            \$index = (int)($expression);
            return (\$index < $nplurals)? \$index : $nplurals - 1;";

        $result = null;
        $funcAll = '$result = function($n) { ' . $func_body . ' };';
        eval($funcAll);

        return $result;
    }

    /**
     * Adds parentheses to the inner parts of ternary operators in
     * plural expressions, because PHP evaluates ternary oerators from left to right
     *
     * @param string $expression the expression without parentheses
     * @return string the expression with parentheses added
     */
    public function parenthesize_plural_exression($expression)
    {
        $expression .= ';';
        $res = '';
        $depth = 0;
        for ($i = 0; $i < strlen($expression); ++ $i) {
            $char = $expression[$i];
            switch ($char) {
                case '?':
                    $res .= ' ? (';
                    $depth ++;
                    break;
                case ':':
                    $res .= ') : (';
                    break;
                case ';':
                    $res .= str_repeat(')', $depth) . ';';
                    $depth = 0;
                    break;
                default:
                    $res .= $char;
            }
        }
        return rtrim($res, ';');
    }

    /**
     *
     * @param string $translation
     * @return array
     */
    public function make_headers($translation)
    {
        $headers = array();
        // sometimes \ns are used instead of real new lines
        $translation = str_replace('\n', "\n", $translation);
        $lines = explode("\n", $translation);
        foreach ($lines as $line) {
            $parts = explode(':', $line, 2);
            if (! isset($parts[1])) {
                continue;
            }
            $headers[trim($parts[0])] = trim($parts[1]);
        }
        return $headers;
    }

    /**
     *
     * @param string $header
     * @param string $value
     */
    public function set_header($header, $value)
    {
        parent::set_header($header, $value);
        if ('Plural-Forms' == $header) {
            list($nplurals, $expression) = $this->nplurals_and_expression_from_header($this->get_header('Plural-Forms'));
            $this->_nplurals = $nplurals;
            $this->_gettext_select_plural_form = $this->make_plural_form_function($nplurals, $expression);
        }
    }

    /**
     * 读取文件到数组
     *
     * @param string|array $strFilename
     * @return array
     */
    public function readToArray($mixFilename)
    {
        if (! is_array($mixFilename)) {
            $mixFilename = [
                $mixFilename
            ];
        }

        foreach ($mixFilename as $strFilename) {
            $this->import_from_file($strFilename);
        }

        return $this->toArray();
    }

    /**
     * 对象转数组
     *
     * @return array
     */
    public function toArray()
    {
        $arrData = [];
        foreach ($this->entries as $strKey => $objEntry) {
            $arrData[$strKey] = $objEntry->translations[0];
        }
        return $arrData;
    }
}
