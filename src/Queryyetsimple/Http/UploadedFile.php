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
namespace Queryyetsimple\Http;

/**
 * 上传文件
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2018.02.26
 * @version 1.0
 */
class UploadedFile extends File
{

    /**
     * 文件原始名字
     * 
     * @var string
     */
    protected $originalName;

    /**
     * 文件类型
     * 
     * @var string
     */ 
    protected $mimeType;

    /**
     * 上传错误
     * 
     * @var int|null
     */
    protected $error;

    /**
     * 上传错误消息格式化
     * 
     * @var array
     */
    protected static $errors = [
        UPLOAD_ERR_INI_SIZE => 'The file "%s" exceeds your upload_max_filesize ini directive (limit is %d KiB).',
        UPLOAD_ERR_FORM_SIZE => 'The file "%s" exceeds the upload limit defined in your form.',
        UPLOAD_ERR_PARTIAL => 'The file "%s" was only partially uploaded.',
        UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
        UPLOAD_ERR_CANT_WRITE => 'The file "%s" could not be written on disk.',
        UPLOAD_ERR_NO_TMP_DIR => 'File could not be uploaded: missing temporary directory.',
        UPLOAD_ERR_EXTENSION => 'File upload was stopped by a PHP extension.',
    ];

    /**
     * 构造函数
     * $_FILES['foo'](tmp_name, name, type, error)
     *
     * @param string $path
     * @param string $originalName
     * @param string|null $mimeType
     * @param int|null $error
     * @return void
     */
    public function __construct(string $path, string $originalName, string $mimeType = null, int $error = null)
    {
        $this->originalName = $originalName;
        $this->mimeType = $mimeType ?: 'application/octet-stream';
        $this->error = $error ?: UPLOAD_ERR_OK;

        parent::__construct($path);
    }

    /**
     * 返回文件原始名字
     *
     * @return string|null
     */
    public function getOriginalName()
    {
        return $this->originalName;
    }

    /**
     * 返回文件原始名字扩展
     *
     * @return string
     */
    public function getOriginalExtension()
    {
        return pathinfo($this->originalName, PATHINFO_EXTENSION);
    }

    /**
     * 返回文件类型
     *
     * @return string|null
     */
    public function getClientMimeType()
    {
        return $this->mimeType;
    }

    /**
     * 返回上传错误
     *
     * @return int
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * 文件是否上传成功
     *
     * @return bool
     */
    public function isValid()
    {
        return UPLOAD_ERR_OK === $this->error && is_uploaded_file($this->getPathname());
    }
    
    /**
     * {@inheritdoc}
     */
    public function move($directory, $name = null)
    {
        if ($this->isValid()) {
            $target = $this->getTargetFile($directory, $name);

            $this->moveToTarget($this->getPathname(), $target);

            return parent::__construct($target);
        }

        throw new FileException($this->getErrorMessage());
    }

    /**
     * 返回文件最大上传字节
     *
     * @return int
     */
    public static function getMaxFilesize()
    {
        $iniMax = strtolower(ini_get('upload_max_filesize'));

        if ('' === $iniMax) {
            return PHP_INT_MAX;
        }

        $max = ltrim($iniMax, '+');
        if (0 === strpos($max, '0x')) {
            $max = intval($max, 16);
        } elseif (0 === strpos($max, '0')) {
            $max = intval($max, 8);
        } else {
            $max = (int) $max;
        }

        switch (substr($iniMax, -1)) {
            case 't': 
                $max *= 1024;
            case 'g': 
                $max *= 1024;
            case 'm': 
                $max *= 1024;
            case 'k': 
                $max *= 1024;
        }

        return $max;
    }

    /**
     * 上传错误
     *
     * @return string
     */
    public function getErrorMessage()
    {
        $errorCode = $this->error;
        $maxFilesize = UPLOAD_ERR_INI_SIZE === $errorCode ? self::getMaxFilesize() / 1024 : 0;

        $message = isset(self::$errors[$errorCode]) ? self::$errors[$errorCode] : 'The file "%s" was not uploaded due to an unknown error.';
        $message = sprintf($message, $this->getOriginalName(), $maxFilesize);

        return $message;
    }
}
