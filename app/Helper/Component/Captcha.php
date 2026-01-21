<?php
declare(strict_types=1);

namespace App\Helper\Component;

use App\Exception\BusinessException;
use App\Helper\Support\RedisCacheSupport;
use App\Http\Common\Enums\Cache;
use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use function Hyperf\Translation\__;

/**
 * 图形验证码组件
 */
final class Captcha
{
    public function __construct(
        private readonly RedisCacheSupport $redisCacheSupport
    ) {}

    /**
     * 生成图形验证码
     *
     * @param Cache      $key       Redis缓存键
     * @param int        $length    验证码长度
     * @param string|int $keyParams 缓存键参数
     * @return array
     */
    public function generate(Cache $key, int $length = 4, string|int $keyParams = ''): array
    {
        // 图文验证码长度
        $phraseBuilder = new PhraseBuilder($length);
        // 生成验证码图片
        $builder = new CaptchaBuilder(builder: $phraseBuilder);
        $builder->build();
        $code = $builder->getPhrase();
        // 验证码缓存
        $this->redisCacheSupport->set($key, $code, $keyParams);
        return [
            'key' => $keyParams,
            'image' => 'data:image/png;base64,' . base64_encode($builder->get())
        ];
    }

    /**
     * 验证图形验证码
     *
     * @param Cache      $key       Redis缓存键
     * @param string     $code      用户输入的验证码
     * @param string|int $keyParams 缓存键参数
     *
     * @return bool
     */
    public function validate(Cache $key, string $code, string|int $keyParams = ''): bool
    {
        $cacheCode = $this->redisCacheSupport->get($key, $keyParams);
        if (!$cacheCode) {
            throw new BusinessException(message: __('system.captchaExpired'));
        }

        if (strtolower($cacheCode) !== strtolower($code)) {
            throw new BusinessException(message: __('system.captchaMismatch'));
        }

        $this->redisCacheSupport->del($key, $keyParams);
        return true;
    }
}