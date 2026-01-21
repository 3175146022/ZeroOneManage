<?php
declare(strict_types=1);

namespace App\Http\Common;

use App\Http\Common\Enums\Code;
use Hyperf\Contract\Arrayable;

/**
 * 统一接口返回格式
 */
class Result implements Arrayable
{
    /**
     * @param Code        $code
     * @param string|null $message
     * @param mixed       $data
     */
    public function __construct(
        public Code    $code = Code::SUCCESS,
        public ?string $message = null,
        public mixed   $data = [])
    {
        if ($this->message === null) {
            $this->message = Code::getMessage($this->code->value);
        }
    }

    /**
     * 转换为数组
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'code' => $this->code->value,
            'message' => $this->message,
            'data' => $this->data,
        ];
    }
}