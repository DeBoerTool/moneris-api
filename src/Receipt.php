<?php

namespace CraigPaul\Moneris;

use CraigPaul\Moneris\Traits\PreparableTrait;
use JsonSerializable;

class Receipt implements JsonSerializable
{
    use PreparableTrait;

    protected array $data;

    /**
     * Create a new Receipt instance.
     */
    public function __construct($data)
    {
        $this->data = $this->prepare($data, [
            ['property' => 'amount', 'key' => 'TransAmount', 'cast' => 'string'],
            ['property' => 'authorization', 'key' => 'AuthCode', 'cast' => 'string'],
            ['property' => 'avs_result', 'key' => 'AvsResultCode', 'cast' => 'string'],
            ['property' => 'card', 'key' => 'CardType', 'cast' => 'string'],
            ['property' => 'code', 'key' => 'ResponseCode', 'cast' => 'string'],
            ['property' => 'complete', 'key' => 'Complete', 'cast' => 'boolean'],
            ['property' => 'cvd_result', 'key' => 'CvdResultCode', 'cast' => 'string'],
            ['property' => 'data', 'key' => 'ResolveData', 'cast' => 'array', 'callback' => 'setData'],
            ['property' => 'date', 'key' => 'TransDate', 'cast' => 'string'],
            ['property' => 'id', 'key' => 'ReceiptId', 'cast' => 'string'],
            ['property' => 'iso', 'key' => 'ISO', 'cast' => 'string'],
            ['property' => 'key', 'key' => 'DataKey', 'cast' => 'string'],
            ['property' => 'message', 'key' => 'Message', 'cast' => 'string'],
            ['property' => 'reference', 'key' => 'ReferenceNum', 'cast' => 'string'],
            ['property' => 'time', 'key' => 'TransTime', 'cast' => 'string'],
            ['property' => 'transaction', 'key' => 'TransID', 'cast' => 'string'],
            ['property' => 'type', 'key' => 'TransType', 'cast' => 'string'],
            ['property' => 'issuer_id', 'key' => 'IssuerId', 'cast' => 'string'],
        ]);
    }

    public function successful(): bool
    {
        $complete = $this->read('complete');
        $valid_code = $this->read('code') !== 'null';
        $code = (int) $this->read('code');

        return $complete && $valid_code && $code >= 0 && $code < 50;
    }

    /**
     * Read an item from the receipt.
     */
    public function read(string $value = ''): mixed
    {
        return $this->data[$value] ?? null;
    }

    /**
     * Get the entire data array.
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Format the resolved data from the Moneris API. This method is called
     * in the PreparableTrait.
     *
     * @noinspection PhpUnusedPrivateMethodInspection
     */
    private function setData(array $data): array
    {
        return [
            'customer_id' => isset($data['cust_id']) ? (is_string($data['cust_id']) ? $data['cust_id'] : $data['cust_id']->__toString()) : null,
            'phone' => isset($data['phone']) ? (is_string($data['phone']) ? $data['phone'] : $data['phone']->__toString()) : null,
            'email' => isset($data['email']) ? (is_string($data['email']) ? $data['email'] : $data['email']->__toString()) : null,
            'note' => isset($data['note']) ? (is_string($data['note']) ? $data['note'] : $data['note']->__toString()) : null,
            'crypt' => isset($data['crypt_type']) ? intval($data['crypt_type']) : null,
            'masked_pan' => isset($data['masked_pan']) ? $data['masked_pan'] : null,
            'pan' => isset($data['pan']) ? $data['pan'] : null,
            'expiry_date' => [
                'month' => isset($data['expdate']) ? substr($data['expdate'], -2, 2) : null,
                'year' => isset($data['expdate']) ? substr($data['expdate'], 0, 2) : null,
            ],
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->getData();
    }
}
