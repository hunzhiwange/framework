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

namespace Leevel\Http;

/**
 * 上传文件.
 *
 * - This class borrows heavily from the Symfony4 Framework and is part of the symfony package.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.02.26
 *
 * @version 1.0
 *
 * @see Symfony\Component\HttpFoundation (https://github.com/symfony/symfony)
 */
class UploadedFile extends File
{
    /**
     * 文件原始名字.
     *
     * @var string
     */
    protected string $originalName;

    /**
     * 文件类型.
     *
     * @var string
     */
    protected string $mimeType;

    /**
     * 上传错误.
     *
     * @var int
     */
    protected int $error;

    /**
     * 上传错误消息格式化.
     *
     * @var array
     */
    protected static array $errors = [
        UPLOAD_ERR_INI_SIZE   => 'The file %s exceeds your upload_max_filesize ini directive (limit is %d KiB).',
        UPLOAD_ERR_FORM_SIZE  => 'The file %s exceeds the upload limit defined in your form.',
        UPLOAD_ERR_PARTIAL    => 'The file %s was only partially uploaded.',
        UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
        UPLOAD_ERR_CANT_WRITE => 'The file %s could not be written on disk.',
        UPLOAD_ERR_NO_TMP_DIR => 'File could not be uploaded: missing temporary directory.',
        UPLOAD_ERR_EXTENSION  => 'File upload was stopped by a PHP extension.',
    ];

    /**
     * 是否为测试.
     *
     * @var bool
     */
    protected bool $test = false;

    /**
     * 构造函数
     * $_FILES['foo'](tmp_name, name, type, error).
     *
     * @param string      $path
     * @param string      $originalName
     * @param null|string $mimeType
     * @param null|int    $error
     */
    public function __construct(string $path, string $originalName, ?string $mimeType = null, ?int $error = null)
    {
        $this->originalName = $originalName;
        $this->mimeType = $mimeType ?: 'application/octet-stream';
        $this->error = $error ?: UPLOAD_ERR_OK;
        $this->test = 5 === func_num_args();

        parent::__construct($path);
    }

    /**
     * 返回文件原始名字.
     *
     * @return null|string
     */
    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }

    /**
     * 返回文件原始名字扩展.
     *
     * @return string
     */
    public function getOriginalExtension(): string
    {
        return pathinfo($this->originalName, PATHINFO_EXTENSION);
    }

    /**
     * 返回文件类型.
     *
     * @return null|string
     */
    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    /**
     * 返回上传错误.
     *
     * @return int
     */
    public function getError(): int
    {
        return $this->error;
    }

    /**
     * 文件是否上传成功.
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return UPLOAD_ERR_OK === $this->error &&
            ($this->test ? true : is_uploaded_file($this->getPathname()));
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Leevel\Http\FileException
     * @codeCoverageIgnore
     */
    public function move(string $directory, ?string $name = null): File
    {
        if ($this->isValid()) {
            if ($this->test) {
                return parent::move($directory, $name);
            }

            $target = $this->getTargetFile($directory, $name);

            $this->moveToTarget($this->getPathname(), $target, true);

            return new File($target);
        }

        throw new FileException($this->getErrorMessage());
    }

    /**
     * 返回文件最大上传字节
     *
     * @return int
     * @codeCoverageIgnoreStart
     */
    public static function getMaxFilesize(): int
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
                // no break
            case 'g':
                $max *= 1024;
                // no break
            case 'm':
                $max *= 1024;
                // no break
            case 'k':
                $max *= 1024;
        }

        return $max;
    }

    /**
     * 上传错误.
     *
     * @return string
     */
    public function getErrorMessage(): string
    {
        $errorCode = $this->error;
        $maxFilesize = UPLOAD_ERR_INI_SIZE === $errorCode ? self::getMaxFilesize() / 1024 : 0;

        $message = isset(self::$errors[$errorCode]) ?
            self::$errors[$errorCode] :
            'The file %s was not uploaded due to an unknown error.';

        $message = sprintf($message, $this->getOriginalName(), $maxFilesize);

        return $message;
    }
}
