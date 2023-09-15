<?php

namespace CraigPaul\Moneris;

use CraigPaul\Moneris\Enums\ResponseErrorEnum;
use CraigPaul\Moneris\Values\Environment;
use GuzzleHttp\Client;
use SimpleXMLElement;

class Processor
{
    protected Client $client;

    /**
     * API configuration.
     */
    protected array $config = [
        'protocol' => 'https',
        'host' => 'esqa.moneris.com',
        'port' => '443',
        'url' => '/gateway2/servlet/MpgRequest',
        'api_version' => 'PHP - 2.5.6',
        'timeout' => 60,
    ];

    /**
     * Global error response to maintain consistency.
     */
    protected string $error = '<?xml version="1.0"?><response><receipt><ReceiptId>Global Error Receipt</ReceiptId><ReferenceNum>null</ReferenceNum><ResponseCode>null</ResponseCode><ISO>null</ISO> <AuthCode>null</AuthCode><TransTime>null</TransTime><TransDate>null</TransDate><TransType>null</TransType><Complete>false</Complete><Message>null</Message><TransAmount>null</TransAmount><CardType>null</CardType><TransID>null</TransID><TimedOut>null</TimedOut></receipt></response>';

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Retrieve the API configuration.
     */
    public function config(Environment|null $environment = null): array
    {
        /**
         * @codeCoverageIgnore
         */
        if ($environment && $environment->isLive()) {
            $this->config['host'] = 'www3.moneris.com';
        }

        return $this->config;
    }

    /**
     * Determine if the transaction is valid. If so, process it via the Moneris
     * API.
     */
    public function process(Transaction $transaction): Response
    {
        if ($transaction->invalid()) {
            $response = new Response($transaction);
            $response->setError(ResponseErrorEnum::InvalidTransactionData);

            return $response;
        }

        $response = $this->submit($transaction);

        return $transaction->validate($response);
    }

    /**
     * Parse the global error response stub.
     */
    protected function error(): SimpleXMLElement
    {
        return simplexml_load_string($this->error);
    }

    /**
     * Set up and send the request to the Moneris API.
     *
     * @param string $url
     * @param string $xml
     */
    protected function send(array $config, $url = '', $xml = ''): string
    {
        $response = $this->client->post($url, [
            'body' => $xml,
            'headers' => [
                'User-Agent' => $config['api_version'],
            ],
            'timeout' => $config['timeout'],
        ]);

        return $response->getBody()->getContents();
    }

    /**
     * Submit the transaction to the Moneris API.
     *
     *
     * @return \SimpleXMLElement
     */
    protected function submit(Transaction $transaction)
    {
        $config = $this->config($transaction->gateway->environment);

        $url = $config['protocol'] . '://' . $config['host'] . ':' . $config['port'] . $config['url'];

        $xml = str_replace(' </', '</', $transaction->toXml());

        $response = $this->send($config, $url, $xml);

        if (!$response) {
            return $this->error();
        }

        $response = @simplexml_load_string($response);

        if ($response === false) {
            return $this->error();
        }

        return $response;
    }
}
