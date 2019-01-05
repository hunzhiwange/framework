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

use InvalidArgumentException;

/**
 * file bag.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.02.25
 *
 * @version 1.0
 */
class FileBag extends Bag
{
    /**
     * 上传文件 keys.
     *
     * @var array
     */
    protected static $fileKeys = [
        'error',
        'name',
        'size',
        'tmp_name',
        'type',
    ];

    /**
     * 构造函数.
     *
     * @param array $elements
     */
    public function __construct(array $elements = [])
    {
        $elements = $this->normalizeArray($elements);

        $this->add($elements);
    }

    /**
     * {@inheritdoc}
     */
    public function replace(array $elements = []): void
    {
        $this->elements = [];

        $elements = $this->normalizeArray($elements);

        $this->add($elements);
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $key, $value): void
    {
        if (!is_array($value) && !$value instanceof UploadedFile) {
            throw new InvalidArgumentException(
                'An uploaded file must be an array or an instance of UploadedFile.'
            );
        }

        parent::set($key, $this->convertFile($value));
    }

    /**
     * {@inheritdoc}
     */
    public function add(array $files = []): void
    {
        foreach ($files as $key => $file) {
            $this->set($key, $file);
        }
    }

    /**
     * 取回文件数组
     * 数组文件请在末尾加上反斜杆访问.
     *
     * @param string $key
     * @param array  $defaults
     *
     * @return mixed
     */
    public function getArr(string $key, array $defaults = [])
    {
        $files = [];

        foreach ($this->elements as $k => $value) {
            if (0 === strpos($k, $key) && null !== $value) {
                $files[] = $value;
            }
        }

        return $files ?: $defaults;
    }

    /**
     * 转换上传信息到文件实例 UploadedFile.
     *
     * @param array|\Leevel\Http\UploadedFile $file
     *
     * @return null|\Leevel\Http\UploadedFile
     */
    protected function convertFile($file): ?UploadedFile
    {
        if ($file instanceof UploadedFile) {
            return $file;
        }

        $file = $this->normalizeFile($file);

        if (UPLOAD_ERR_NO_FILE === $file['error']) {
            $file = null;
        } else {
            $file = new UploadedFile($file['tmp_name'], $file['name'], $file['type'], $file['error']);
        }

        return $file;
    }

    /**
     * 格式化 $_FILES 数组.
     *
     * @param array $data
     *
     * @return array
     */
    protected function normalizeFile(array $data): array
    {
        $result = [];

        foreach ($data as $key => $value) {
            if (in_array($key, static::$fileKeys, true)) {
                $result[$key] = $value;
            }
        }

        $keys = $this->normalizeKey($result);

        if ($keys !== static::$fileKeys) {
            throw new InvalidArgumentException(
                sprintf('An array uploaded file must be contain keys %s.', implode(',', static::$fileKeys))
            );
        }

        return $result;
    }

    /**
     * 格式化多维数组类文件为一维数组.
     *
     * @param array $elements
     *
     * @return array
     */
    protected function normalizeArray(array $elements): array
    {
        $result = [];

        foreach ($elements as $key => $value) {
            if (is_array($value)) {
                if (false === array_key_exists('name', $value)) {
                    throw new InvalidArgumentException('An uploaded file must be contain key name.');
                }

                if (isset($value['name']) && is_array($value['name'])) {
                    foreach ($value['name'] as $index => $item) {
                        $element = [];

                        foreach (static::$fileKeys as $fileKey) {
                            if (!array_key_exists($fileKey, $value)) {
                                throw new InvalidArgumentException(
                                    sprintf('An uploaded file must be contain key %s.', $fileKey)
                                );
                            }

                            if (!array_key_exists($index, $value[$fileKey])) {
                                throw new InvalidArgumentException(
                                    sprintf('An uploaded file must be contain %s in key %s.', $index, $fileKey)
                                );
                            }

                            $element[$fileKey] = $value[$fileKey][$index] ?? '';
                        }

                        $result[$key.'\\'.$index] = $element;

                        $result = $this->normalizeArray($result);
                    }
                } else {
                    $result[$key] = $value;
                }
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * 格式化 keys.
     *
     * @param array $data
     *
     * @return array
     */
    protected function normalizeKey(array $data): array
    {
        $keys = array_keys($data);
        sort($keys);

        return $keys;
    }
}
