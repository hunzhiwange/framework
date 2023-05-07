<?php

declare(strict_types=1);

namespace Leevel\Auth;

use Firebase\JWT\JWT as BaseJwt;
use Firebase\JWT\Key;

/**
 * Auth jwt.
 */
class Jwt extends Auth implements IAuth
{
    /**
     * 配置.
     */
    protected array $option = [
        'token' => null,
        'expire' => null,
        'iss' => null, // 签发人
        'aud' => null, // 受众
        'auth_key' => null, // 加密 key
        'alg' => 'HS256', // 签名算法
    ];

    /**
     * 构造函数.
     */
    public function __construct(array $option = [])
    {
        parent::__construct($option);
    }

    /**
     * 数据持久化.
     */
    protected function setPersistence(string $key, string $value, ?int $expire = null): string
    {
        $payload = array_filter([
            'iss' => $this->option['iss'],
            'aud' => $this->option['aud'],
            'iat' => time(),
            'nbf' => time(),
            'init_key' => $key,
            'data' => $value,
        ]);

        if ($expire) {
            $payload['exp'] = time() + $expire;
        }

        return BaseJwt::encode($payload, $this->option['auth_key'], $this->option['alg']);
    }

    /**
     * {@inheritDoc}
     */
    protected function tokenData(): array
    {
        return (array) $this->getPersistence($this->getTokenName());
    }

    /**
     * 获取持久化数据.
     */
    protected function getPersistence(string $key): mixed
    {
        try {
            $decodedData = (array) BaseJwt::decode($key, new Key($this->option['auth_key'], $this->option['alg']));
            $result = json_decode($decodedData['data'], true, 512, JSON_THROW_ON_ERROR);
            unset($decodedData['data']);

            return $result;
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * 删除持久化数据.
     */
    protected function deletePersistence(string $key): void
    {
        // JWT 无法注销
    }
}
