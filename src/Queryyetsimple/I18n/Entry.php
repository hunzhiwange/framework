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

namespace Leevel\I18n;

/**
 * 解析出每一项语言数据
 * This class borrows heavily from the Wordpress and is part of the Wordpress package.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.09.18
 * @see https://github.com/WordPress/WordPress/blob/master/wp-includes/pomo/
 *
 * @version 1.0
 */
class Entry
{
    /**
     * Whether the entry contains a string and its plural form, default is false.
     *
     * @var bool
     */
    public $is_plural = false;
    public $context;
    public $singular;
    public $plural;
    public $translations = [];
    public $translator_comments = '';
    public $extracted_comments = '';
    public $references = [];
    public $flags = [];

    /**
     * @param array $args associative array, support following keys:
     * @sub string singular the string to translate, if omitted and empty entry will be created
     * @sub string plural the plural form of the string, setting this will set {@see $is_plural} to true
     * @sub array translations translations of the string and possibly -- its plural forms
     * @sub string context (string) a string differentiating two equal strings used in different contexts
     * @sub string translator_comments comments left by translators
     * @sub string extracted_comments comments left by developers
     * @sub array references places in the code this strings is used, in relative_to_root_path/file.php:linenum form
     * @sub array flags flags like php-format
     */
    public function __construct($args = [])
    {
        // if no singular -- empty object
        if (!isset($args['singular'])) {
            return;
        }
        // get member variable values from args hash
        foreach ($args as $varname => $value) {
            $this->$varname = $value;
        }
        if (isset($args['plural']) && $args['plural']) {
            $this->is_plural = true;
        }
        if (!is_array($this->translations)) {
            $this->translations = [];
        }
        if (!is_array($this->references)) {
            $this->references = [];
        }
        if (!is_array($this->flags)) {
            $this->flags = [];
        }
    }

    /**
     * Generates a unique key for this entry.
     *
     * @return string|bool the key or false if the entry is empty
     */
    public function key()
    {
        if (null === $this->singular || '' === $this->singular) {
            return false;
        }
        // Prepend context and EOT, like in MO files
        $key = !$this->context ? $this->singular : $this->context.chr(4).$this->singular;
        // Standardize on \n line endings
        $key = str_replace([
            "\r\n",
            "\r",
        ], "\n", $key);

        return $key;
    }

    /**
     * @param object $other
     */
    public function merge_with(&$other)
    {
        $this->flags = array_unique(array_merge($this->flags, $other->flags));
        $this->references = array_unique(array_merge($this->references, $other->references));
        if ($this->extracted_comments != $other->extracted_comments) {
            $this->extracted_comments .= $other->extracted_comments;
        }
    }
}
