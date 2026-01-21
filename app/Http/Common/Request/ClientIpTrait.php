<?php

namespace App\Http\Common\Request;

use Symfony\Component\HttpFoundation\IpUtils;

trait ClientIpTrait
{
    protected array $trustedProxies = [];

    /**
     * 获取 IP 地址
     *
     * @return string
     */
    public function getClientIp(): string
    {
        $remoteIp = $this->server('remote_addr', '');

        if (!$this->isFromTrustedProxy($remoteIp)) {
            return $remoteIp;
        }

        $xff = $this->getHeaderLine('x-forwarded-for');
        if (!$xff) {
            return $remoteIp;
        }

        $ips = array_map('trim', explode(',', $xff));

        foreach (array_reverse($ips) as $ip) {
            if (filter_var($ip, FILTER_VALIDATE_IP)
                && !IpUtils::checkIp($ip, $this->trustedProxies)
            ) {
                return $ip;
            }
        }

        return $remoteIp;
    }

    /**
     * Determine if the request is from a trusted proxy.
     *
     * @param string $remote
     * @return bool
     */
    public function isFromTrustedProxy(string $remote): bool
    {
        return $remote && IpUtils::checkIp($remote, $this->trustedProxies);
    }
}