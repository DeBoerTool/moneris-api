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

    public function getTransaction(): Transaction
    {
        return $this->transaction;
    }

    public function setError(ResponseErrorEnum $error): self
    {
        $this->error = $error;

        return $this;
    }

    public function getError(): ResponseErrorEnum|null
    {
        return $this->error;
    }

    public function isSuccessful(): bool
    {
        return $this->error === null;
    }

    /**
     * Get the transaction receipt when available.
     */
    public function receipt(): Receipt|null
    {
        if (is_null($this->transaction->response)) {
            return null;
        }

        return new Receipt($this->transaction->response->receipt);
    }

    public function gateway(): GatewayInterface
    {
        return $this->transaction->gateway;
    }

    public function transaction(): Transaction
    {
        return $this->transaction;
    }

    /**
     * Validate the response.
     */
    public function validate(): self
    {
        if ($this->receipt()->read('id') === 'Global Error Receipt') {
            $this->error = ResponseErrorEnum::GlobalErrorReceipt;

            return $this;
        }

        if (!$this->receipt()->isSuccessful()) {
            $this->error = $this->convertReceiptCodeToStatus($this->receipt());

            return $this;
        }

        if ($this->gateway()->hasAvsEnabled() && $this->receipt()->hasAvsCode()) {
            $avsCode = $this->receipt()->getAvsCode();

            if (!$this->gateway()->isValidAvsCode($avsCode)) {
                $this->error = ResponseErrorEnum::fromAvsCode($avsCode);

                return $this;
            }
        }

        if ($this->gateway()->hasCvdEnabled() && $this->receipt()->hasCvdCode()) {
            $cvdCode = $this->receipt()->getCvdCode();

            if (!$this->gateway()->isValidCvdCode($cvdCode[1])) {
                $this->error = ResponseErrorEnum::CvdGeneric;

                return $this;
            }
        }

        return $this;
    }

    protected function convertReceiptCodeToStatus(
        Receipt $receipt
    ): ResponseErrorEnum {
        $code = $receipt->read('code');
        $status = $this->convertReceiptMessageToStatus($receipt);

        if ($code === 'null' && !is_null($status)) {
            return $status;
        }

        return ResponseErrorEnum::fromReceiptCode($code);
    }

    protected function convertReceiptMessageToStatus(
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
