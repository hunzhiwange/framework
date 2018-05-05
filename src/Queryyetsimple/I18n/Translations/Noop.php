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

/**
 * translations.noop
 * This class borrows heavily from the Wordpress and is part of the Wordpress package
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.09.18
 * @link https://github.com/WordPress/WordPress/blob/master/wp-includes/pomo/
 * @version 1.0
 */
class Noop extends Translations
{

    /**
     * prop
     *
     * @var array
     */
    public $entries = array();
    public $headers = array();

    /**
     * {@inheritdoc}
     */
    public function add_entry($entry)
    {
        return true;
    }

    /**
     *
     * @param string $header
     * @param string $value
     */
    public function set_header($header, $value)
    {
    }

    /**
     *
     * @param array $headers
     */
    public function set_headers($headers)
    {
    }

    /**
     *
     * @param string $header
     * @return false
     */
    public function get_header($header)
    {
        return false;
    }

    /**
     *
     * @param Translation_Entry $entry
     * @return false
     */
    public function translate_entry(&$entry)
    {
        return false;
    }

    /**
     *
     * @param string $singular
     * @param string $context
     */
    public function translate($singular, $context = null)
    {
        return $singular;
    }

    /**
     *
     * @param int $count
     * @return bool
     */
    public function select_plural_form($count)
    {
        return 1 == $count ? 0 : 1;
    }

    /**
     *
     * @return int
     */
    public function get_plural_forms_count()
    {
        return 2;
    }

    /**
     *
     * @param string $singular
     * @param string $plural
     * @param int $count
     * @param string $context
     */
    public function translate_plural($singular, $plural, $count, $context = null)
    {
        return 1 == $count ? $singular : $plural;
    }

    /**
     *
     * @param object $other
     */
    public function merge_with(&$other)
    {
    }
}
