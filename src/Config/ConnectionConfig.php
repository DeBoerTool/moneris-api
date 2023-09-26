<?php

namespace CraigPaul\Moneris\Config;

use CraigPaul\Moneris\Interfaces\ConnectionConfigInterface;

class ConnectionConfig implements ConnectionConfigInterface
{
    public function __construct(
        public readonly string $protocol = 'https',
        public readonly string $host = 'mpg1.moneris.io',
        public readonly string $port = '443',
        public readonly string $url = '/gateway2/servlet/MpgRequest',
        public readonly string $api_version = 'PHP NA - 1.0.22',
        public readonly int $timeout = 60,
    ) {
    }

    public static function testing(): self
    {
        return new self(
            protocol: 'https',
            host: 'mpg1t.moneris.io',
            port: '443',
            url: '/gateway2/servlet/MpgRequest',
            api_version: 'PHP NA - 1.0.22',
            timeout: 60,
        );
    }

    public function getFullUrl(): string
    {
        return sprintf(
            '%s://%s:%s%s',
            $this->protocol,
            $this->host,
            $this->port,
            $this->url,
        );
    }

    public function getHeaders(): array
    {
        return ['User-Agent' => $this->api_version];
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }
}
