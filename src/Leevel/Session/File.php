<?php

declare(strict_types=1);

namespace Leevel\Session;

use Leevel\Cache\File as CacheFile;

/**
 * session.file.
 */
class File extends Session implements ISession
{
    /**
     * 配置.
     */
    protected array $config = [
        'id' => null,
        'name' => null,
    ];

    /**
     * 构造函数.
     */
    public function __construct(CacheFile $cache, array $config = [])
    {
        $this->config = array_merge($this->config, $config);
        parent::__construct($cache);
    }
}
