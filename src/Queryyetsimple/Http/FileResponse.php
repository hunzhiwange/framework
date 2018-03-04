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

use DateTime;
use SplFileInfo;
use SplFileObject;
use LogicException;
use InvalidArgumentException;

/**
 * FILE 响应请求
 * This class borrows heavily from the Symfony2 Framework and is part of the symfony package
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2018.02.27
 * @version 1.0
 * @see Symfony\Component\HttpFoundation (https://github.com/symfony/symfony)
 */
class FileResponse extends Response
{

    /**
     * 文件
     * 
     * @var \Queryyetsimple\Http\File
     */
    protected $file;

    /**
     * 下载附件
     *
     * @var string
     */
    const DISPOSITION_ATTACHMENT = 'attachment';

    /**
     * 文件直接读取
     *
     * @var string
     */
    const DISPOSITION_INLINE = 'inline';

    /**
     * 构造函数
     * 
     * @param \SplFileObject|\SplFileInfo|string $file
     * @param integer $status
     * @param array $headers
     * @param null|string $contentDisposition
     * @param bool $autoEtag
     * @param bool $autoLastModified
     * @return void
     */
    public function __construct($file, int $status = 200, array $headers = [], string $contentDisposition = null, bool $autoEtag = false, bool $autoLastModified = true)
    {
        parent::__construct(null, $status, $headers);

        $this->setFile($file, $contentDisposition, $autoEtag, $autoLastModified);
    }

    /**
     * 创建一个文件响应
     * 
     * @param \SplFileObject|\SplFileInfo|string $file
     * @param integer $status
     * @param array $headers
     * @param null|string $contentDisposition
     * @param bool $autoEtag
     * @param bool $autoLastModified
     * @return static
     */
    public static function create($file = null, int $status = 200, array $headers = [], $contentDisposition = null, $autoEtag = false, $autoLastModified = true)
    {
        return new static($file, $status, $headers, $contentDisposition, $autoEtag, $autoLastModified);
    }

    /**
     * 设置文件
     *
     * @param \SplFileObject|\SplFileInfo|string $file 
     * @param string $contentDisposition
     * @param bool $autoEtag
     * @param bool $autoLastModified
     * @return $this
     */
    public function setFile($file, $contentDisposition = null, $autoEtag = false, $autoLastModified = true)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        if (! $file instanceof File) {
            if ($file instanceof SplFileInfo || $file instanceof SplFileObject) {
                $file = new File($file->getPathname());
            } else {
                $file = new File((string) $file);
            }
        }

        if (! $file->isReadable()) {
            throw new FileException('File must be readable.');
        }

        $this->file = $file;

        if ($autoEtag) {
            $this->setAutoEtag();
        }

        if ($autoLastModified) {
            $this->setAutoLastModified();
        }

        if ($contentDisposition) {
            $this->setContentDisposition($contentDisposition);
        }

        return $this;
    }

    /**
     * 取回文件
     *
     * @return \Queryyetsimple\Http\File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * 自动设置最后修改时间
     *
     * @return void
     */
    public function setAutoLastModified()
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->setLastModified(DateTime::createFromFormat('U', $this->file->getMTime()));

        return $this;
    }

    /**
     * 自动设置标记
     *
     * @return void
     */
    public function setAutoEtag()
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $etag = base64_encode(hash_file('sha256', $this->file->getPathname(), true));

        $this->setEtag($etag);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setContent($content)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        if (null !== $content) {
            throw new LogicException('The content cannot be set on a FileResponse instance.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return false;
    }

    /**
     * 设置文件下载模式
     *
     * @param string $disposition
     * @param string $filename
     * @return $this
     */
    public function setContentDisposition($disposition, $filename = '')
    {
        if ($this->checkTControl()) {
            return $this;
        }

        if ('' === $filename) {
            $filename = $this->file->getFilename();
        }

        if (! in_array($disposition, [self::DISPOSITION_ATTACHMENT, self::DISPOSITION_INLINE])) {
            throw new InvalidArgumentException('The disposition type is invalid.');
        }

        $this->headers->set('Content-Disposition', $disposition . ';filename=' . basename($filename));

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function sendContent()
    {
        if ($this->checkTControl()) {
            return $this;
        }

        if (! $this->isSuccessful()) {
            return parent::sendContent();
        }

        $out = fopen('php://output', 'wb');
        $file = fopen($this->file->getPathname(), 'rb');
        stream_copy_to_stream($file, $out);
        fclose($out);
        fclose($file);

        return $this;
    }
}
