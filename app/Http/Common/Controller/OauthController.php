<?php
declare(strict_types=1);

namespace App\Http\Common\Controller;

use App\Http\Common\Result;
use App\Http\Common\Service\OauthService;

/**
 * Oauth认证控制器
 */
final class OauthController extends AbstractController
{
    public function __construct(
        private readonly OauthService $oauthService,
    ) {}

    /**
     * 获取认证凭证
     *
     * @return Result
     */
    public function certificate(): Result
    {
        return $this->success($this->oauthService->getCertificate());
    }
}