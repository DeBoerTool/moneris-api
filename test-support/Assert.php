<?php

namespace CraigPaul\Moneris\TestSupport;

use CraigPaul\Moneris\Cards\Expiry\Expiry;
use CraigPaul\Moneris\Data\AvsData;
use CraigPaul\Moneris\Data\Card\CardCustomerData;
use CraigPaul\Moneris\Data\Customer\CustomerData;
use CraigPaul\Moneris\Enums\AvsCode;
use CraigPaul\Moneris\Enums\CvdCode;
use CraigPaul\Moneris\Interfaces\CardInterface;
use CraigPaul\Moneris\Response;
use CraigPaul\Moneris\Values\MaskedCardNumber;

class Assert extends \PHPUnit\Framework\Assert
{
    public function hasCardCustomer(
        Response $response,
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
        self::assertNull(
            $response->getError(),
            sprintf(
                'The response had an error: "%s".',
                $response->getError()?->value,
            ),
        );

        self::assertTrue(
            $response->isSuccessful(),
            'The response was not successful.',
        );
    }

    public function isNotSuccessful(Response $response): void
    {
        self::assertFalse(
            $response->isSuccessful(),
            'The response was successful.',
        );
    }

    public function hasVaultKey(
        Response $response,
        string|null $expected = null,
    ): void {
        $this->isSuccessful($response);

        self::assertNotNull($response->getReceipt()?->read('key'));

        if ($expected) {
            self::assertSame($expected, $response->getReceipt()?->read('key'));
        }
    }

    public function hasMaskedCardNumber(Response $response, string $actual): void
    {
        self::assertNotNull($response->getReceipt()?->read('data'));

        $data = $response->getReceipt()?->read('data');

        self::assertSame(
            (string) new MaskedCardNumber($actual),
            $data['masked_pan'],
        );
    }

    public function isComplete(Response $response): void
    {
        self::assertTrue($response->getReceipt()->read('complete'));
    }

    public function hasCvdResult(
        Response $response,
        CvdCode $cvdCode = CvdCode::M
    ): void {
        $code = sprintf('1%s', $cvdCode->name);

        self::assertSame($code, $response->getReceipt()->read('cvd_result'));
    }

    public function hasCvdSuccess(
        Response $response,
        CvdCode $cvdCode = CvdCode::M,
    ): void {
        $code = sprintf('1%s', $cvdCode->name);

        self::assertSame($code, $response->getReceipt()->read('cvd_result'));
    }

    public function hasCvdFailure(Response $response): void
    {
        self::assertSame('1N', $response->getReceipt()->read('cvd_result'));
    }

    public function hasAvsResult(Response $response, AvsCode $avsCode): void
    {
        self::assertNotNull($response->getAvsResult());
        self::assertSame(
            $avsCode->value,
            $response->getAvsResult()->avsCode->value,
        );
    }

    public function hasIssuerId(Response $response): void
    {
        self::assertNotNull($response->getReceipt()?->getIssuerId());
    }

    public function hasAvsData(Response $response, AvsData $avsData): void
    {
        $this->checkAvsData($response, $avsData);
    }

    public function hasNoAvsData(Response $beforeUpdatePeekResponse): void
    {
        $this->checkAvsData($beforeUpdatePeekResponse, null);
    }

    public function verificationSuccess(
        CardInterface $card,
        Response $response,
    ): void {
        $this->isSuccessful($response);

        // Cards that don't support CoF (Amex) will fail if we don't do this.
        if ($card->supportsCof()) {
            $this->hasIssuerId($response);
        }
    }

    public function hasNoCardCustomer(Response $response): void
    {
        $data = $response->getTransaction()->getPath('receipt.ResolveData');

        self::assertSame('', $data['cust_id']);
        self::assertSame('', $data['phone']);
        self::assertSame('', $data['email']);
        self::assertSame('', $data['note']);
    }

    public function hasExpiry(
        Response $updateResponse,
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
        Response $response,
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
            $response->getTransaction()->getPath('receipt.Message'),
        );
    }

    public function isDeclined(
        Response $response,
        string $expectedSubstring = 'declined',
    ): void {
        self::assertStringContainsString(
            strtolower($expectedSubstring),
            strtolower($response->getTransaction()->getPath('receipt.Message')),
        );
    }

    public function hasCustomerData(
        Response $response,
        CustomerData $customerData,
    ): void {
        dd($response);
    }
}
