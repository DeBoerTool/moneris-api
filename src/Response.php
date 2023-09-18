<?php

namespace CraigPaul\Moneris;

use CraigPaul\Moneris\Enums\ResponseErrorEnum;
use CraigPaul\Moneris\Interfaces\GatewayInterface;

class Response
{
    /**
     * The error, or null if no error has occurred.
     */
    protected ResponseErrorEnum|null $error = null;

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
        if (is_null($this->transaction->response)) {
            return null;
        }

        return new Receipt($this->transaction->response->receipt);
    }

    public function getGateway(): GatewayInterface
    {
        return $this->transaction->gateway;
    }

    public function getTransaction(): Transaction
    {
        return $this->transaction;
    }

    public function validate(): self
    {
        if ($this->getReceipt()->read('id') === 'Global Error Receipt') {
            $this->error = ResponseErrorEnum::GlobalErrorReceipt;

            return $this;
        }

        if (!$this->getReceipt()->isSuccessful()) {
            $this->error = $this->convertReceiptCodeToError($this->getReceipt());

            return $this;
        }

        if ($this->getGateway()->hasAvsEnabled() && $this->getReceipt()->hasAvsCode()) {
            $avsCode = $this->getReceipt()->getAvsCode();

            if (!$this->getGateway()->isValidAvsCode($avsCode)) {
                $this->error = ResponseErrorEnum::fromAvsCode($avsCode);

                return $this;
            }
        }

        if ($this->getGateway()->hasCvdEnabled() && $this->getReceipt()->hasCvdCode()) {
            $cvdCode = $this->getReceipt()->getCvdCode();

            if (!$this->getGateway()->isValidCvdCode($cvdCode[1])) {
                $this->error = ResponseErrorEnum::CvdGeneric;

                return $this;
            }
        }

        return $this;
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
