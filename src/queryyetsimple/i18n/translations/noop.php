<?php
/*
 * This file is part of the ************************ package.
 * ##########################################################
 * #   ____                          ______  _   _ ______   #
 * #  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
 * # |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
 * #  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
 * #       \__   | \___ |_|    \__  || |    | | | || |      #
 * #     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
 * #                          |___ /  Since 2010.10.03      #
 * ##########################################################
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2017 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace queryyetsimple\i18n\translations;

/**
 * translations.noop
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.09.18
 * @see https://github.com/WordPress/WordPress/blob/master/wp-includes/pomo/
 * @version 1.0
 */
class noop extends translations
{

    /**
     * prop
     *
     * @var array
     */
    public $entries = array();
    public $headers = array();

    /**
     * (non-PHPdoc)
     *
     * @see \queryyetsimple\i18n\translations\translations::add_entry()
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
