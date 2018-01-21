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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Queryyetsimple\Aop;

// https://github.com/jeremeamia/super_closure
class Token
{
    /**
     * @var string The token name. Always null for literal tokens.
     */
    public $name;
    /**
     * @var int|null The token's integer value. Always null for literal tokens.
     */
    public $value;
    /**
     * @var string The PHP code of the token.
     */
    public $code;

    /**
     * @var int|null The line number of the token in the original code.
     */
    public $line;
    /**
     * Constructs a token object.
     *
     * @param string   $code
     * @param int|null $value
     * @param int|null $line
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($code, $value = null, $line = null)
    {
        if (is_array($code)) {
            list($value, $code, $line) = array_pad($code, 3, null);
        }
        $this->code = $code;
        $this->value = $value;
        $this->line = $line;
        $this->name = $value ? token_name($value) : null;
    }
    /**
     * Determines if the token's value/code is equal to the specified value.
     *
     * @param mixed $value The value to check.
     *
     * @return bool True if the token is equal to the value.
     */
    public function is($value)
    {
        return ($this->code === $value || $this->value === $value);
    }
    public function __toString()
    {
        return $this->code;
    }
}
