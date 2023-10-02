<?php

namespace CraigPaul\Moneris;

use CraigPaul\Moneris\Support\Http\HttpConfigInterface;
use CraigPaul\Moneris\Support\Http\HttpInterface;
use CraigPaul\Moneris\Support\Xml\ToXmlInterface;
use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;

class Http implements HttpInterface
{
    public function __construct(
        public readonly HttpConfigInterface $config,
        public readonly ClientInterface $guzzle,
    ) {
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \CraigPaul\Moneris\Exceptions\InvalidHttpResponseCode
     */
    public function post(
        string|ToXmlInterface $xml,
    ): ResponseInterface
    {
        $response = $this->guzzle->post($this->config->getFullUrl(), [
            'body' => is_string($xml) ? $xml : $xml->toXml(),
            'headers' => $this->config->getHeaders(),
            'timeout' => $this->config->timeout,
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new Exceptions\InvalidHttpResponseCode(
                $response->getStatusCode(),
            );
        }

        return $response;
    }
}
