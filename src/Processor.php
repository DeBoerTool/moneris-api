<?php

namespace CraigPaul\Moneris;

use CraigPaul\Moneris\Exceptions\InvalidTransactionException;
use CraigPaul\Moneris\Support\Http\HttpConfigInterface;
use GuzzleHttp\ClientInterface;
use SimpleXMLElement;

class Processor
{
    /**
     * Global error response to maintain consistency.
     */
    protected string $error = '<?xml version="1.0"?><response><receipt><ReceiptId>Global Error Receipt</ReceiptId><ReferenceNum>null</ReferenceNum><ResponseCode>null</ResponseCode><ISO>null</ISO> <AuthCode>null</AuthCode><TransTime>null</TransTime><TransDate>null</TransDate><TransType>null</TransType><Complete>false</Complete><Message>null</Message><TransAmount>null</TransAmount><CardType>null</CardType><TransID>null</TransID><TimedOut>null</TimedOut></receipt></response>';

    public function __construct(
        public readonly HttpConfigInterface $config,
        protected ClientInterface $guzzle,
    ) {
    }

    /**
     * Determine if the transaction is valid. If so, process it via the Moneris
     * API.
     */
    public function process(Transaction $transaction): OldResponse
    {
        if ($transaction->invalid()) {
            throw new InvalidTransactionException($transaction);
        }

        /** @noinspection PhpUnhandledExceptionInspection */
        $xml = $this->submit($transaction);

        return $transaction->validate($xml);
    }

    /**
     * Parse the global error response stub.
     */
    protected function error(): SimpleXMLElement
    {
        return simplexml_load_string($this->error);
    }

    /**
     * Submit the transaction to the Moneris API.
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function submit(Transaction $transaction): SimpleXMLElement
    {
        // Get the XML representation of the Transaction.
        $xml = str_replace(' </', '</', $transaction->toXml());

        // Send the XML to the Moneris API, with headers and timeout from the
        // config.
        $response = $this->guzzle
            ->post($this->config->getFullUrl(), [
                'body' => $xml,
                'headers' => $this->config->getHeaders(),
                'timeout' => $this->config->timeout,
            ])
            ->getBody()
            ->getContents();

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
