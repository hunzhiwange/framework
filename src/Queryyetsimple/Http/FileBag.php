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

use InvalidArgumentException;

/**
 * file bag
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2018.02.25
 * @version 1.0
 */
class FileBag extends Bag
{

    /**
     * 上传文件 keys
     * 
     * @var array
     */
    protected static $fileKeys =[
        'error', 
        'name', 
        'size', 
        'tmp_name', 
        'type'
    ];

    /**
     * 构造函数
     * 
     * @param array $elements
     * @return void 
     */
    public function __construct(array $elements = [])
    {
        $elements = $this->normalizeArray($elements);

        $this->add($elements);
    }

    /**
     * {@inheritdoc}
     */
    public function replace(array $elements = [])
    {
        $this->elements = [];

        $this->add($elements);
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        if (! is_array($value) && ! $value instanceof UploadedFile) {
            throw new InvalidArgumentException('An uploaded file must be an array or an instance of UploadedFile.');
        }

        parent::set($key, $this->convertFile($value));
    }

    /**
     * {@inheritdoc}
     */
    public function add(array $files = array())
    {
        foreach ($files as $key => $file) {
            $this->set($key, $file);
        }
    }

    /**
     * 取回文件数组
     * 数组文件请在末尾加上反斜杆访问
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getArr($key, $default = null) {
        $files = [];
        foreach ($this->elements as $k => $value) {
            if (strpos($k, $key) === 0) {
                $files[] = $value;
            }
        }

        return $files ?: $default;
    }

    /**
     * 转换上传信息到文件实例 UploadedFile
     *
     * @param array|\Queryyetsimple\Http\UploadedFile $file
     * @return \Queryyetsimple\Http\UploadedFile|null
     */
    protected function convertFile($file)
    {
        if ($file instanceof UploadedFile) {
            return $file;
        }

        $file = $this->normalizeFile($file);
        
        if (UPLOAD_ERR_NO_FILE == $file['error']) {
            $file = null;
        } else {
            $file = new UploadedFile($file['tmp_name'], $file['name'], $file['type'], $file['error']);
        }

        return $file;
    }

    /**
     * 格式化 $_FILES 数组
     *
     * @param array $data
     * @return array
     */
    protected function normalizeFile(array $data)
    {
        $result = [];

        foreach ($data as $key => $value) {
            if (in_array($key, static::$fileKeys)) {
                $result[$key] = $value;
            }
        }

        $keys = $this->normalizeKey($result);

        if ($keys !== static::$fileKeys) {
            throw new InvalidArgumentException(sprintf('An array uploaded file must be contain keys %s.', implode(',', static::$fileKeys)));
        }

        return $result;
    }

    /**
     * 格式化多维数组类文件为一维数组
     *
     * @param array $elements
     * @return array
     */
    protected function normalizeArray(array $elements)
    {
        $result = [];

        foreach ($elements as $key => $value) {
            if (! isset($value['name'])) {
                throw new InvalidArgumentException('An uploaded file must be contain key name.');
            } elseif (is_array($value['name'])) {
                foreach ($value['name'] as $index => $item) {
                    $element = [];
                    foreach (static::$fileKeys as $fileKey) {
                        if (! isset($value[$fileKey][$index])) {
                            throw new InvalidArgumentException(sprintf('An uploaded file must be contain key %s.', $fileKey));
                        }

                        $element[$fileKey] = $value[$fileKey][$index] ?? '';
                    }

                    $result[$key . '\\' . $index] = $element;

                    $result = $this->normalizeArray($result);
                }
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * 格式化 keys
     *
     * @param array $data
     * @return array
     */
    protected function normalizeKey(array $data) {
        $keys = array_keys($data);
        sort($keys);

        return $keys;
    }
}
