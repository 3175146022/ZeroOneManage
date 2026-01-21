<?php
declare(strict_types=1);

namespace App\Http\Common\Service;

use App\Helper\Support\RedisCacheSupport;
use App\Helper\Support\UuidSupport;
use App\Http\Common\Enums\Oauth\OauthEnum;
use Hyperf\Stringable\Str;

/**
 * Oauth认证服务
 */
final class OauthService extends IService
{
    public function __construct(
        private readonly UuidSupport       $uuidSupport,
        private readonly RedisCacheSupport $redisCacheSupport,
    ) {}

    /**
     * 获取认证凭证
     *
     * @return array
     */
    public function getCertificate(): array
    {
        // 生成唯一标识 appKey
        $appKey = $this->uuidSupport->generateV4();
        // 生成唯一标识 appSecret
        $appSecret = Str::random(64);
        $data = [
            'app_key' => $appKey,
            'app_secret' => $appSecret,
        ];

        // 缓存
        $this->redisCacheSupport->set(OauthEnum::fromName(OauthEnum::OAUTH), $data, $appKey);

        return $data;
    }
}