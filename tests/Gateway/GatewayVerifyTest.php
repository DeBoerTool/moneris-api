<?php

namespace CraigPaul\Moneris\Tests\Gateway;

use CraigPaul\Moneris\Cards\Amex;
use CraigPaul\Moneris\Cards\MasterCard;
use CraigPaul\Moneris\Cards\Visa;
use CraigPaul\Moneris\Data\Cof\CofVerificationData;
use CraigPaul\Moneris\Data\Transactable\VerificationData;
use CraigPaul\Moneris\Enums\AvsCode;
use CraigPaul\Moneris\Enums\CvdCode;
use CraigPaul\Moneris\Interfaces\CardInterface;
use CraigPaul\Moneris\TestSupport\Cases\TestCase;
use CraigPaul\Moneris\TestSupport\Verification\AmexSource;
use CraigPaul\Moneris\TestSupport\Verification\MasterCardSource;
use CraigPaul\Moneris\TestSupport\Verification\VisaSource;

class GatewayVerifyTest extends TestCase
{
    /**
     * Some of these cases seem like they shouldn't happen, but since they're
     * included in the Moneris documentation, they're included here.
     *
     * @test
     */
    public function verifying_with_cvd_visa(): void
    {
        // Approved with CVD match
        $response = $this->gateway()->verify(new VerificationData(
            orderId: $this->uid(),
            creditCard: new Visa(
                number: VisaSource::getApprovedMatch(CvdCode::M),
                expiry: '2012'
            ),
            cvdData: $this->fixtures->cvdData(),
        ));

        $this->assert->isSuccessful($response);
        $this->assert->isComplete($response);
        $this->assert->hasCvdSuccess($response);

        // Declined with CVD match
        $response = $this->gateway()->verify(new VerificationData(
            orderId: $this->uid(),
            creditCard: new Visa(
                number: VisaSource::getDeclinedMatch(CvdCode::M),
                expiry: '2012',
            ),
            cvdData: $this->fixtures->cvdData(),
        ));

        // Approved with CVD mismatch
        $this->assert->isNotSuccessful($response);
        $this->assert->isComplete($response);
        $this->assert->hasCvdSuccess($response);

        $response = $this->gateway()->verify(new VerificationData(
            orderId: $this->uid(),
            creditCard: new Visa(
                number: VisaSource::getApprovedMatch(CvdCode::N),
                expiry: '2012',
            ),
            cvdData: $this->fixtures->cvdData(),
        ));

        $this->assert->isSuccessful($response);
        $this->assert->isComplete($response);
        $this->assert->hasCvdFailure($response);

        // Declined with CVD mismatch
        $response = $this->gateway()->verify(new VerificationData(
            orderId: $this->uid(),
            creditCard: new Visa(
                number: VisaSource::getDeclinedMatch(CvdCode::N),
                expiry: '2012',
            ),
            cvdData: $this->fixtures->cvdData(),
        ));

        $this->assert->isNotSuccessful($response);
        $this->assert->isComplete($response);
        $this->assert->hasCvdFailure($response);
    }

    /**
     * The Moneris simulator does not support MasterCard CVD mismatch with
     * success, so there are fewer cases here than with Visa.
     *
     * @test
     */
    public function verifying_with_cvd_mastercard(): void
    {
        // Approved with CVD match
        $response = $this->gateway()->verify(new VerificationData(
            orderId: $this->uid(),
            creditCard: new MasterCard(
                number: MasterCardSource::getApprovedMatch(CvdCode::M),
                expiry: '2012',
            ),
            cvdData: $this->fixtures->cvdData(),
        ));

        $this->assert->isSuccessful($response);
        $this->assert->isComplete($response);
        $this->assert->hasCvdSuccess($response);

        // Declined with CVD mismatch
        $response = $this->gateway()->verify(new VerificationData(
            orderId: $this->uid(),
            creditCard: new MasterCard(
                number: MasterCardSource::getDeclinedMatch(CvdCode::N),
                expiry: '2012',
            ),
            cvdData: $this->fixtures->cvdData(),
        ));

        $this->assert->isNotSuccessful($response);
        $this->assert->isComplete($response);
        $this->assert->hasCvdFailure($response);
    }

    /** @test */
    public function verifying_with_cvd_amex(): void
    {
        // Approved with CVD match
        $cvdCode = CvdCode::Y;
        $response = $this->gateway()->verify(new VerificationData(
            orderId: $this->uid(),
            creditCard: new Amex(
                number: AmexSource::getApprovedMatch($cvdCode),
                expiry: '2012'
            ),
            cvdData: $this->fixtures->cvdData(),
        ));

        $this->assert->isSuccessful($response);
        $this->assert->isComplete($response);
        $this->assert->hasCvdSuccess($response, $cvdCode);

        // Approved with CVD mismatch
        $response = $this->gateway()->verify(new VerificationData(
            orderId: $this->uid(),
            creditCard: new Amex(
                number: AmexSource::getApprovedMatch(CvdCode::N),
                expiry: '2012'
            ),
            cvdData: $this->fixtures->cvdData(),
        ));

        $this->assert->isSuccessful($response);
        $this->assert->isComplete($response);
        $this->assert->hasCvdFailure($response);

        // Declined with CVD mismatch
        $response = $this->gateway()->verify(new VerificationData(
            orderId: $this->uid(),
            creditCard: new Amex(
                number: AmexSource::getDeclinedMatch(CvdCode::N),
                expiry: '2012'
            ),
            cvdData: $this->fixtures->cvdData(),
        ));

        $this->assert->isNotSuccessful($response);
        $this->assert->isComplete($response);
        $this->assert->hasCvdFailure($response);
    }

    /** @test */
    public function verifying_with_avs_visa(): void
    {
        $avsCode = AvsCode::D;
        $avsData = $this->fixtures->avsData();
        $creditCard = new Visa(
            VisaSource::getApprovedMatch(avs: $avsCode),
            $this->fixtures->expiry(),
        );

        $response = $this->gateway()->verify(new VerificationData(
            orderId: $this->uid(),
            creditCard: $creditCard,
            avsData: $avsData,
        ));

        $this->assert->isSuccessful($response);
        $this->assert->isComplete($response);
        // 2023-09-21 According to the Moneris documentation this test credit
        // card should be returning an AVS code of 'D' but it is returning 'Y'.
        // This may change in the future, so if this test starts failing with
        // a non-matching code, that may be the reason.
        $this->assert->hasAvsResult($response, AvsCode::Y);
    }

    /** @test */
    public function verifying_with_avs_mastercard(): void
    {
        // Approved with AVS match
        $avsCode = AvsCode::X;
        $creditCard = new MasterCard(
            MasterCardSource::getApprovedMatch(avs: $avsCode),
            $this->fixtures->expiry(),
        );

        $response = $this->gateway()->verify(new VerificationData(
            orderId: $this->uid(),
            creditCard: $creditCard,
            avsData: $this->fixtures->avsData(),
        ));

        $this->assert->isSuccessful($response);
        $this->assert->isComplete($response);
        $this->assert->hasAvsResult($response, $avsCode);

        // Declined with AVS mismatch
        $avsCode = AvsCode::N;
        $creditCard = new MasterCard(
            MasterCardSource::getDeclinedMatch(avs: $avsCode),
            $this->fixtures->expiry(),
        );

        $response = $this->gateway()->verify(new VerificationData(
            orderId: $this->uid(),
            creditCard: $creditCard,
            avsData: $this->fixtures->avsData(),
        ));

        $this->assert->isDeclined($response, 'call for');
        $this->assert->isNotSuccessful($response);
        $this->assert->isComplete($response);
        $this->assert->hasAvsResult($response, $avsCode);
    }

    /** @test */
    public function verifying_with_avs_amex(): void
    {
        $avsCode = AvsCode::Y;
        $creditCard = new Amex(
            AmexSource::getApprovedMatch(avs: $avsCode),
            $this->fixtures->expiry(),
        );

        $response = $this->gateway()->verify(new VerificationData(
            orderId: $this->uid(),
            creditCard: $creditCard,
            avsData: $this->fixtures->avsData(),
        ));

        $this->assert->isSuccessful($response);
        $this->assert->isComplete($response);
        $this->assert->hasAvsResult($response, $avsCode);
    }

    /**
     * There is currently no Discover credit card simulator available.
     *
     * @test
     */
    public function verifying_with_avs_discover(): void
    {
        $this->expectNotToPerformAssertions();
    }

    /**
     * @test
     *
     * @dataProvider avsAndCvdVerificationProvider
     */
    public function verification_with_avs_and_cvd_and_cof(
        CardInterface $creditCard,
        CvdCode $cvdCode,
        AvsCode $avsCode,
    ): void {
        $response = $this->gateway()->verify(new VerificationData(
            orderId: $this->fixtures->orderId(),
            creditCard: $creditCard,
            cvdData: $this->fixtures->cvdData(),
            avsData: $this->fixtures->avsData(),
            cofData: new CofVerificationData(),
        ));

        $this->assert->isSuccessful($response);
        $this->assert->isComplete($response);
        $this->assert->hasCvdResult($response, $cvdCode);
        $this->assert->hasAvsResult($response, $avsCode);

        // Amex will fail without this check.
        if ($creditCard->supportsCof()) {
            $this->assert->hasIssuerId($response);
        }
    }
}
