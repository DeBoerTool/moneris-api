<?php

namespace CraigPaul\Moneris\TestSupport;

use CraigPaul\Moneris\Cards\Expiry\Expiry;
use CraigPaul\Moneris\Cards\Expiry\Pan;
use CraigPaul\Moneris\Data\AvsData;
use CraigPaul\Moneris\Data\Card\CardCustomerData;
use CraigPaul\Moneris\Enums\AvsCode;
use CraigPaul\Moneris\Enums\CvdCode;
use CraigPaul\Moneris\Support\Cards\CardInterface;
use CraigPaul\Moneris\Response;
use CraigPaul\Moneris\Values\DataKey;
use CraigPaul\Moneris\Values\MaskedCardNumber;
use PHPUnit\Framework\Assert;

class AssertResponse
{
    public function __construct(private readonly Response $response)
    {
    }

    public function dd(): never
    {
        dd([
            'transactable' => $this->response->getTransactable(),
            'receipt' => $this->response->getReader(),
        ]);
    }

    public function isSuccessful(): self
    {
        Assert::assertTrue(
            $this->response->getResponseCode()->isSuccessful(),
            $this->fail('The response code was not successful.'),
        );

        return $this;
    }

    public function isUnsuccessful(): self
    {
        Assert::assertFalse($this->response->getResponseCode()->isSuccessful());

        return $this;
    }

    public function isApproved(string $messageFragment = 'approved'): self
    {
        Assert::assertStringContainsString(
            strtolower($messageFragment),
            strtolower($this->response->getMessage()),
            'The response message does not contain the expected string.',
        );

        return $this;
    }

    public function isDeclined(string $messageFragment = 'declined'): self
    {
        Assert::assertStringContainsString(
            strtolower($messageFragment),
            strtolower($this->response->getMessage()),
            'The response message does not contain the expected string.',
        );

        return $this;
    }

    public function isComplete(): self
    {
        Assert::assertTrue($this->response->isComplete());

        return $this;
    }

    public function hasCvdResult(CvdCode|null $cvdCode = null): self
    {
        Assert::assertNotNull(
            $this->response->getCvdResult(),
            $this->fail('CVD result was null.'),
        );

        if ($cvdCode) {
            Assert::assertSame(
                $cvdCode->value,
                $this->response->getCvdResult()?->cvdCode->value,
                $this->fail('CVD code verification failed.')
            );
        }

        return $this;
    }

    public function hasAvsResult(AvsCode|null $avsCode = null): self
    {
        Assert::assertNotNull(
            $this->response->getAvsResult(),
            $this->fail('AVS result was null.'),
        );

        if ($avsCode) {
            Assert::assertSame(
                $avsCode->value,
                $this->response->getAvsResult()?->avsCode->value,
                $this->fail('AVS code verification failed.'),
            );
        }

        return $this;
    }

    public function hasIssuerId(CardInterface $creditCard): self
    {
        if ($creditCard->supportsCof()) {
            Assert::assertNotNull(
                $this->response->getIssuerId(),
                'Issuer ID was not found.',
            );
        }

        return $this;
    }

    public function hasDataKey(DataKey|null $dataKey = null): self
    {
        Assert::assertNotNull(
            $this->response->getDataKey(),
            'Data key was not found.',
        );

        if ($dataKey) {
            Assert::assertSame(
                (string) $dataKey,
                (string) $this->response->getDataKey(),
            );
        }

        return $this;
    }

    public function hasCardCustomerData(CardCustomerData $data): self
    {
        $resolveData = $this->getResolveData();

        Assert::assertSame($data->customerId, $resolveData['cust_id']);
        Assert::assertSame($data->phone, $resolveData['phone']);
        Assert::assertSame($data->email, $resolveData['email']);
        Assert::assertSame($data->note, $resolveData['note']);

        return $this;
    }

    public function hasAvsData(AvsData $data): self
    {
        $resolveData = $this->getResolveData();

        Assert::assertSame(
            $data->streetNumber,
            $resolveData['avs_street_number'],
        );
        Assert::assertSame($data->streetName, $resolveData['avs_street_name']);
        Assert::assertSame($data->postalCode, $resolveData['avs_zipcode']);

        return $this;
    }

    public function hasNoAvsData(): self
    {
        $resolveData = $this->getResolveData();

        Assert::assertSame('', $resolveData['avs_street_number']);
        Assert::assertSame('', $resolveData['avs_street_name']);
        Assert::assertSame('', $resolveData['avs_zipcode']);

        return $this;
    }

    public function hasNoCardCustomerData(): self
    {
        $resolveData = $this->getResolveData();

        Assert::assertSame('', $resolveData['cust_id']);
        Assert::assertSame('', $resolveData['phone']);
        Assert::assertSame('', $resolveData['email']);
        Assert::assertSame('', $resolveData['note']);

        return $this;
    }

    public function hasExpiry(Expiry $expiry): self
    {
        Assert::assertSame(
            (string) $expiry,
            (string) $this->response->getExpiry()
        );

        return $this;
    }

    public function hasMaskedCardNumber(Pan $creditCardNumber): self
    {
        Assert::assertNotNull($this->response->getMaskedCardNumber());

        Assert::assertSame(
            (string) new MaskedCardNumber((string) $creditCardNumber),
            (string) $this->response->getMaskedCardNumber(),
        );

        return $this;
    }

    // Getters //

    public function hasTransactionId(): self
    {
        Assert::assertNotNull($this->response->getTransactionId());

        return $this;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    // Internal //

    protected function getResolveData(): array
    {
        $resolveData = $this->response->getResolveData();

        Assert::assertNotNull($resolveData, 'Resolve data was null.');

        return $resolveData;
    }

    protected function fail(string $message): string
    {
        $data = print_r($this->response->getReader()->toArray(), return: true);

        return implode(PHP_EOL, [
            $message,
            '>>> Response Data:',
            $data,
        ]);
    }
}
