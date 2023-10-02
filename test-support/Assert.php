<?php

namespace CraigPaul\Moneris\TestSupport;

use CraigPaul\Moneris\Cards\Expiry\Expiry;
use CraigPaul\Moneris\Data\AvsData;
use CraigPaul\Moneris\Data\Card\CardCustomerData;
use CraigPaul\Moneris\Data\Customer\CustomerData;
use CraigPaul\Moneris\Enums\AvsCode;
use CraigPaul\Moneris\Enums\CvdCode;
use CraigPaul\Moneris\Support\Cards\CardInterface;
use CraigPaul\Moneris\OldResponse;
use CraigPaul\Moneris\Response;
use CraigPaul\Moneris\Values\MaskedCardNumber;

class Assert extends \PHPUnit\Framework\Assert
{
    public function response(Response $response): AssertResponse
    {
        return new AssertResponse($response);
    }

    public function hasCardCustomer(
        OldResponse $response,
        CardCustomerData $customer,
    ): void {
        $data = $response->getReceipt()?->read('data');

        self::assertNotNull($data);

        self::assertSame($customer->customerId, $data['customer_id']);
        self::assertSame($customer->phone, $data['phone']);
        self::assertSame($customer->email, $data['email']);
        self::assertSame($customer->note, $data['note']);
    }

    public function isSuccessful(Response $response): void
    {
        self::assertTrue($response->getResponseCode()->isSuccessful());
    }

    public function isNotSuccessful(OldResponse $response): void
    {
        self::assertFalse(
            $response->isSuccessful(),
            'The response was successful.',
        );
    }

    public function hasVaultKey(
        OldResponse $response,
        string|null $expected = null,
    ): void {
        $this->isSuccessful($response);

        self::assertNotNull($response->getReceipt()?->read('key'));

        if ($expected) {
            self::assertSame($expected, $response->getReceipt()?->read('key'));
        }
    }

    public function hasMaskedCardNumber(OldResponse $response, string $actual): void
    {
        self::assertNotNull($response->getReceipt()?->read('data'));

        $data = $response->getReceipt()?->read('data');

        self::assertSame(
            (string) new MaskedCardNumber($actual),
            $data['masked_pan'],
        );
    }

    public function isComplete(OldResponse $response): void
    {
        self::assertTrue($response->getReceipt()->read('complete'));
    }

    public function hasCvdResult(
        Response $response,
        CvdCode $cvdCode,
    ): void {
        $code = sprintf('1%s', $cvdCode->name);

        self::assertSame($code, $response->read('CvdResultCode'));
    }

    public function hasCvdFailure(OldResponse $response): void
    {
        self::assertSame('1N', $response->getReceipt()->read('cvd_result'));
    }

    public function hasAvsResult(OldResponse $response, AvsCode $avsCode): void
    {
        self::assertNotNull($response->getAvsResult());
        self::assertSame(
            $avsCode->value,
            $response->getAvsResult()->avsCode->value,
        );
    }

    public function hasIssuerId(OldResponse $response): void
    {
        self::assertNotNull($response->getReceipt()?->getIssuerId());
    }

    public function hasAvsData(OldResponse $response, AvsData $avsData): void
    {
        $this->checkAvsData($response, $avsData);
    }

    public function hasNoAvsData(OldResponse $beforeUpdatePeekResponse): void
    {
        $this->checkAvsData($beforeUpdatePeekResponse, null);
    }

    public function verificationSuccess(
        CardInterface $card,
        OldResponse $response,
    ): void {
        $this->isSuccessful($response);

        // Cards that don't support CoF (Amex) will fail if we don't do this.
        if ($card->supportsCof()) {
            $this->hasIssuerId($response);
        }
    }

    public function hasNoCardCustomer(OldResponse $response): void
    {
        $data = $response->getTransaction()->getPath('receipt.ResolveData');

        self::assertSame('', $data['cust_id']);
        self::assertSame('', $data['phone']);
        self::assertSame('', $data['email']);
        self::assertSame('', $data['note']);
    }

    public function hasExpiry(
        OldResponse $updateResponse,
        Expiry|string $expiry,
    ): void {
        $transaction = $updateResponse->getTransaction();

        self::assertSame(
            $transaction->getPath('receipt.ResolveData.expdate'),
            (string) $expiry,
        );
    }

    // Internal //

    protected function checkAvsData(
        OldResponse $response,
        AvsData|null $avsData,
    ): void {
        $avsData = $avsData ?? new AvsData('', '', '');

        $data = $response->getTransaction()->getPath('receipt.ResolveData');

        $this->assertSame($data['avs_street_number'], $avsData->streetNumber);
        $this->assertSame($data['avs_street_name'], $avsData->streetName);
        $this->assertSame($data['avs_zipcode'], $avsData->postalCode);
    }

    public function isApproved(Response $response): void
    {
        self::assertStringContainsString(
            'APPROVED',
            $response->getMessage(),
        );
    }

    public function isDeclined(
        OldResponse $response,
        string $expectedSubstring = 'declined',
    ): void {
        self::assertStringContainsString(
            strtolower($expectedSubstring),
            strtolower($response->getTransaction()->getPath('receipt.Message')),
        );
    }

    public function hasCustomerData(
        OldResponse $response,
        CustomerData $customerData,
    ): void {
        dd($response);
    }
}
