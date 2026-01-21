<?php
declare(strict_types=1);

namespace App\Helper\Support;

/**
 * 加密支持类
 */
final class EncryptionSupport
{
    /**
     * 加密密码
     *
     * @param string $password
     * @param string $salt
     * @return string
     */
    public function encryptPassword(string $password, string $salt): string
    {
        return md5(md5($password) . $salt);
    }
}