<?php
declare(strict_types=1);

namespace App\Http\Common\Middleware;

use App\Exception\BusinessException;
use App\Helper\Support\RedisCacheSupport;
use App\Helper\Traits\Debugging;
use App\Http\Common\Enums\Oauth\OauthEnum;
use Carbon\Carbon;
use Hyperf\HttpMessage\Upload\UploadedFile;
use Hyperf\HttpServer\Contract\RequestInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use function Hyperf\Translation\__;

/**
 * 签名验证中间件
 */
final class SignatureMiddleware implements MiddlewareInterface
{
    use Debugging;

    public function __construct(
        private readonly RequestInterface  $request,
        private readonly RedisCacheSupport $redisCacheSupport,
    ) {}

    /**
     * 处理请求
     *
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->isDebug()) {
            return $handler->handle($request);
        }

        $sign = $this->request->input('signature');
        $timestamp = $this->request->input('timestamp');
        $appKey = $this->request->header('app_key');

        // 验证必要参数
        if (empty($sign) || empty($timestamp) || empty($appKey)) {
            throw new BusinessException(message: __('system.invalidSignature'));
        }

        // 验证时间戳（防止重放攻击）
        if (abs(Carbon::now()->timestamp - (int)$timestamp) > 300) {
            throw new BusinessException(message: __('system.signatureExpired'));
        }

        // 获取证书
        $certificate = $this->redisCacheSupport->get(OauthEnum::fromName(OauthEnum::OAUTH), $appKey);
        if (!$certificate) {
            throw new BusinessException(message: __('system.certificateInvalidation'));
        }
        $appSecret = $certificate['app_secret'];

        // 验证签名
        if (!$this->verifySignature($sign, $appSecret)) {
            throw new BusinessException(message: __('system.signatureVerificationFailed'));
        }

        return $handler->handle($request);
    }

    /**
     * 验证签名
     *
     * @param string $sign      签名
     * @param string $appSecret 应用密钥
     * @return bool
     */
    private function verifySignature(string $sign, string $appSecret = ''): bool
    {
        // 获取所有参数
        $params = $this->request->all();
        unset($params['signature']);

        $filtered = [];
        foreach ($params as $key => $value) {
            // 排除数组
            if (is_array($value)) {
                continue;
            }

            // 排除文件
            if ($value instanceof UploadedFile) {
                continue;
            }

            // 统一转字符串
            if (is_bool($value)) {
                $value = $value ? '1' : '0';
            } elseif (is_null($value)) {
                $value = '';
            } else {
                $value = (string)$value;
            }

            // 排除大于 100 的长字符
            if (mb_strlen($value) > 100) {
                continue;
            }

            $filtered[$key] = $value;
        }

        // 参数排序
        ksort($filtered);
        // 拼接参数
        $pairs = [];
        foreach ($filtered as $key => $value) {
            $pairs[] = $key . '=' . $value;
        }
        $string = implode('&', $pairs);
        // 生成签名
        $expectedSign = hash_hmac('sha256', $string, $appSecret);
        // 比较签名
        return hash_equals($expectedSign, $sign);
    }
}