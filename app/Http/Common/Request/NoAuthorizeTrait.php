<?php

declare(strict_types=1);

namespace App\Http\Common\Request;

/**
 * @package App\Http\Common\Request
 */
trait NoAuthorizeTrait
{
    use BasicRequestTrait;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }
}
