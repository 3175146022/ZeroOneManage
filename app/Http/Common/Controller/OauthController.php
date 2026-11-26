<?php
declare(strict_types=1);

namespace App\Http\Common\Controller;

use App\Http\Common\Result;
use App\Http\Common\Service\OauthService;
use Hyperf\Swagger\Annotation\HyperfServer;
use OpenApi\Attributes as OA;

/**
 * Oauth认证控制器
 */
#[HyperfServer('http')]
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
    #[OA\Get(
        path: '/oauth/certificate',
        operationId: 'getCertificate',
        summary: '签发 API 调用凭证',
        tags: ['认证'],
        responses: [
            new OA\Response(
                response: 200,
                description: '有效期为 2 小时的签名凭证',
                content: new OA\JsonContent(allOf: [
                    new OA\Schema(ref: '#/components/schemas/ApiResponse'),
                    new OA\Schema(properties: [
                        new OA\Property(
                            property: 'data',
                            description: '用于请求签名的应用凭证',
                            allOf: [new OA\Schema(ref: '#/components/schemas/Certificate')],
                        ),
                    ]),
                ]),
            ),
            new OA\Response(response: 500, description: '服务内部错误'),
        ],
    )]
    public function certificate(): Result
    {
        return $this->success($this->oauthService->getCertificate());
    }
}