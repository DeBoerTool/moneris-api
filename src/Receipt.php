<?php

namespace CraigPaul\Moneris;

use Closure;
use CraigPaul\Moneris\Enums\AvsCode;
use CraigPaul\Moneris\Enums\CardType;
use CraigPaul\Moneris\Enums\CvdCode;
use CraigPaul\Moneris\Traits\PreparableTrait;
use JsonSerializable;

class Receipt implements JsonSerializable
{
    use PreparableTrait;

    protected array $data;

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

    public function hasGlobalError(): bool
    {
        return $this->read('id') === 'Global Error Receipt';
    }

    public function getCardType(): CardType
    {
        return CardType::fromResponseCode($this->read('card'));
    }

    public function getIssuerId(): string|null
    {
        return $this->whenNotNullish($this->read('issuer_id'));
    }

    public function getDataKey(): string|null
    {
        return $this->whenNotNullish($this->read('key'));
    }

    public function hasAvsCode(): bool
    {
        return !$this->isNullish($this->getRawAvsCode());
    }

    public function getRawAvsCode(): string|null
    {
        return $this->read('avs_result');
    }

    public function getAvsCode(): AvsCode|null
    {
        return $this->whenNotNullish(
            $this->getRawAvsCode(),
            fn () => AvsCode::from($this->getRawAvsCode()),
        );
    }

    public function hasCvdCode(): bool
    {
        return !$this->isNullish($this->getCvdCode());
    }

    public function getCvdCode(): CvdCode|null
    {
        return $this->whenNotNullish(
            $this->read('cvd_result'),
            fn () => CvdCode::from(substr($this->read('cvd_result'), 1, 1)),
        );
    }

    public function getCode(): string
    {
        return $this->read('code');
    }

    public function isComplete(): bool
    {
        return $this->read('complete') === true;
    }

    public function hasValidCode(): bool
    {
        return $this->getCode() !== 'null';
    }

    public function hasSuccessCode(): bool
    {
        $code = (int) $this->getCode();

        return $code >= 0 && $code < 50;
    }

    public function isSuccessful(): bool
    {
        return $this->isComplete()
            && $this->hasValidCode()
            && $this->hasSuccessCode();
    }

    /**
     * Given a key, read a value from the receipt data. Note that these values
     * have casts defined in the constructor.
     */
    public function read(string $key = ''): mixed
    {
        return $this->data[$key] ?? null;
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
            'customer_id' => isset($data['cust_id'])
                ? (is_string($data['cust_id'])
                    ? $data['cust_id']
                    : $data['cust_id']->__toString())
                : null,
            'phone' => isset($data['phone'])
                ? (is_string($data['phone'])
                    ? $data['phone']
                    : $data['phone']->__toString())
                : null,
            'email' => isset($data['email'])
                ? (is_string($data['email'])
                    ? $data['email']
                    : $data['email']->__toString())
                : null,
            'note' => isset($data['note'])
                ? (is_string($data['note'])
                    ? $data['note']
                    : $data['note']->__toString())
                : null,
            'crypt' => isset($data['crypt_type'])
                ? intval($data['crypt_type'])
                : null,
            'masked_pan' => $data['masked_pan'] ?? null,
            'pan' => $data['pan'] ?? null,
            'expiry_date' => [
                'month' => isset($data['expdate'])
                    ? substr($data['expdate'], -2, 2)
                    : null,
                'year' => isset($data['expdate'])
                    ? substr($data['expdate'], 0, 2)
                    : null,
            ],
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->getData();
    }

    protected function isNullish(mixed $value): bool
    {
        return is_null($value) || $value === 'null';
    }

    protected function whenNotNullish(
        mixed $value,
        Closure|null $transform = null
    ): mixed {
        if ($this->isNullish($value)) {
            return null;
        }

        return $transform
            ? $transform($value)
            : $value;
    }
}
