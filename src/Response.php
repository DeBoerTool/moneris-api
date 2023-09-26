<?php

namespace CraigPaul\Moneris;

use Adbar\Dot;
use CraigPaul\Moneris\Enums\ResponseErrorEnum;
use CraigPaul\Moneris\Interfaces\GatewayConfigInterface;
use CraigPaul\Moneris\Sources\AvsResultSource;
use CraigPaul\Moneris\Sources\CvdResultSource;
use CraigPaul\Moneris\Support\XmlReader;
use CraigPaul\Moneris\Values\AvsResult;
use CraigPaul\Moneris\Values\CvdResult;

class Response
{
    /**
     * The error, or null if no error has occurred.
     */
    protected ResponseErrorEnum|null $error = null;

    protected AvsResult|null $avsResult = null;

    protected CvdResult|null $cvdResult = null;

    public function __construct(protected readonly Transaction $transaction)
    {
    }

    public function isSuccessful(): bool
    {
        return $this->error === null;
    }

    public function getError(): ResponseErrorEnum|null
    {
        return $this->error;
    }

    public function getReceipt(): Receipt|null
    {
        if (is_null($this->transaction->getXmlResponse())) {
            return null;
        }

        return new Receipt($this->transaction->getXmlResponse()->receipt);
    }

    public function getConfig(): GatewayConfigInterface
    {
        return $this->transaction->getConfig();
    }

    public function getTransaction(): Transaction
    {
        return $this->transaction;
    }

    public function getAvsResult(): AvsResult|null
    {
        return $this->avsResult;
    }

    public function getCvdResult(): CvdResult|null
    {
        return $this->cvdResult;
    }

    public function validate(): self
    {
        $receipt = $this->getReceipt();

        if ($receipt->hasGlobalError()) {
            $this->error = ResponseErrorEnum::GlobalErrorReceipt;

            return $this;
        }

        if (!$receipt->isSuccessful()) {
            $this->error = $this->convertReceiptCodeToError($this->getReceipt());
        }

        if ($this->getConfig()->useAvs() && $receipt->hasAvsCode()) {
            $this->avsResult = AvsResultSource::resolve(
                $receipt->getCardType(),
                $receipt->getAvsCode(),
            );
        }

        if ($this->getConfig()->useCvd() && $receipt->hasCvdCode()) {
            $this->cvdResult = CvdResultSource::resolve(
                $this->getReceipt()->getCvdCode(),
            );
        }

        return $this;
    }

    public function toArray(): array
    {
        $reader = new XmlReader($this->transaction->getXmlResponse());

        return $reader->toArray();
    }

    public function getPath(string $dotNotatedPath): mixed
    {
        return (new Dot($this->toArray()))->get($dotNotatedPath);
    }

    protected function convertReceiptCodeToError(
        Receipt $receipt
    ): ResponseErrorEnum {
        $code = $receipt->read('code');
        $status = $this->convertReceiptMessageToError($receipt);

        if ($code === 'null' && !is_null($status)) {
            return $status;
        }

        return ResponseErrorEnum::fromReceiptCode($code);
    }

    protected function convertReceiptMessageToError(
        Receipt $receipt
    ): ResponseErrorEnum|null {
        $message = (string) $receipt->read('message');
        $status = null;

        if (preg_match('/invalid pan/i', $message)) {
            $status = ResponseErrorEnum::InvalidCard;
        }

        if (preg_match('/invalid expiry date/i', $message)) {
            $status = ResponseErrorEnum::InvalidExpiryDate;
        }

        return $status;
    }
}
