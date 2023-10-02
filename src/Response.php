<?php

namespace CraigPaul\Moneris;

use Closure;
use CraigPaul\Moneris\Cards\Expiry\Expiry;
use CraigPaul\Moneris\Enums\AvsCode;
use CraigPaul\Moneris\Enums\CardType;
use CraigPaul\Moneris\Enums\CvdCode;
use CraigPaul\Moneris\Sources\AvsResultSource;
use CraigPaul\Moneris\Sources\CvdResultSource;
use CraigPaul\Moneris\Values\TransactionId;
use CraigPaul\Moneris\Xml\Reader;
use CraigPaul\Moneris\Support\Transactables\TransactableInterface;
use CraigPaul\Moneris\Values\AvsResult;
use CraigPaul\Moneris\Values\CvdResult;
use CraigPaul\Moneris\Values\DataKey;
use CraigPaul\Moneris\Values\IssuerId;
use CraigPaul\Moneris\Values\MaskedCardNumber;
use CraigPaul\Moneris\Values\ResponseCode;
use Psr\Http\Message\ResponseInterface;
use SimpleXMLElement;

class Response
{
    private Reader|null $reader;

    public function __construct(
        public readonly TransactableInterface $transactable,
        public readonly ResponseInterface $psrResponse,
    )
    {
        $this->reader = null;
    }

    public function getTransactable(): TransactableInterface
    {
        return $this->transactable;
    }

    public function getReader(): Reader
    {
        if ($this->reader === null) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $xml = new SimpleXMLElement(
                $this->psrResponse->getBody()->getContents(),
            );

            $this->reader = new Reader($xml->xpath('//receipt')[0]);
        }

        return $this->reader;
    }

    public function read(string $path): mixed
    {
        return $this->getReader()->getPath($path);
    }

    public function isComplete(): bool
    {
        return $this->read('Complete');
    }

    public function getTransactionId(): TransactionId|null
    {
        return $this->whenNotNull(
            $this->read('TransID'),
            fn (string $value) => TransactionId::of($value),
        );
    }

    public function getResponseCode(): ResponseCode
    {
        return new ResponseCode($this->read('ResponseCode'));
    }

    public function getMessage(): string|null
    {
        return $this->read('Message');
    }

    public function getIssuerId(): IssuerId|null
    {
        return $this->whenNotNull(
            $this->read('IssuerId'),
            fn (string $value) => new IssuerId($value),
        );
    }

    public function getDataKey(): DataKey|null
    {
        return $this->whenNotNull(
            $this->read('DataKey'),
            fn (string $value) => new DataKey($value),
        );
    }

    public function getCardType(): CardType
    {
        return CardType::fromResponseCode($this->read('CardType'));
    }

    public function getExpiry(): Expiry|null
    {
        return $this->whenNotNull(
            $this->read('ResolveData.expdate'),
            fn (string $value) => new Expiry($value),
        );
    }

    public function getMaskedCardNumber(): MaskedCardNumber|null
    {
        return $this->whenNotNull(
            $this->read('ResolveData.masked_pan'),
            fn (string $value) => new MaskedCardNumber($value),
        );
    }

    public function getResolveData(): array|null
    {
        return $this->read('ResolveData');
    }

    public function getCvdResult(): CvdResult|null
    {
        return $this->whenNotNull(
            $this->read('CvdResultCode'),
            fn (string $value) => CvdResultSource::resolve(
                CvdCode::from(substr($value, 1, 1)),
            ),
        );
    }

    public function getAvsResult(): AvsResult|null
    {
        return $this->whenNotNull(
            $this->read('AvsResultCode'),
            fn (string $value) => AvsResultSource::resolve(
                $this->getCardType(),
                AvsCode::from($value),
            ),
        );
    }

    protected function whenNotNull(mixed $value, Closure $callback): mixed
    {
        return is_null($value) ? null : $callback($value);
    }
}
