<?php

namespace CraigPaul\Moneris\Tests\Gateway;

use CraigPaul\Moneris\Cards\Amex;
use CraigPaul\Moneris\Cards\MasterCard;
use CraigPaul\Moneris\Cards\Visa;
use CraigPaul\Moneris\Data\Cof\CofVerificationData;
use CraigPaul\Moneris\Transactables\VerifyCard;
use CraigPaul\Moneris\Enums\AvsCode;
use CraigPaul\Moneris\Enums\CvdCode;
use CraigPaul\Moneris\Support\Cards\CardInterface;
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
        // Approved with CVD match //

        $v1 = new VerifyCard(
            orderId: $this->fixtures->orderId(),
            creditCard: new Visa(
                number: VisaSource::getApprovedMatch(CvdCode::M),
                expiry: '2012',
            ),
            cvdData: $this->fixtures->cvdData(),
        );

        $this->assert
            ->response($v1->submit($this->http(), $this->credentials()))
            ->isApproved()
            ->isSuccessful()
            ->isComplete()
            ->hasCvdResult(CvdCode::M);

        // Declined with CVD match //

        $v2 = new VerifyCard(
            orderId: $this->fixtures->orderId(),
            creditCard: new Visa(
                number: VisaSource::getDeclinedMatch(CvdCode::M),
                expiry: '2012',
            ),
            cvdData: $this->fixtures->cvdData(),
        );

        $this->assert
            ->response($v2->submit($this->http(), $this->credentials()))
            ->isUnsuccessful()
            ->isDeclined()
            ->isComplete()
            ->hasCvdResult(CvdCode::M);

        // Approved with CVD mismatch //

        $v3 = new VerifyCard(
            orderId: $this->fixtures->orderId(),
            creditCard: new Visa(
                number: VisaSource::getApprovedMatch(CvdCode::N),
                expiry: '2012',
            ),
            cvdData: $this->fixtures->cvdData(),
        );

        $this->assert
            ->response($v3->submit($this->http(), $this->credentials()))
            ->isSuccessful()
            ->isComplete()
            ->isApproved()
            ->hasCvdResult(CvdCode::N);

        // Declined with CVD mismatch //

        $v4 = new VerifyCard(
            orderId: $this->fixtures->orderId(),
            creditCard: new Visa(
                number: VisaSource::getDeclinedMatch(CvdCode::N),
                expiry: '2012',
            ),
            cvdData: $this->fixtures->cvdData(),
        );

        $this->assert
            ->response($v4->submit($this->http(), $this->credentials()))
            ->isUnsuccessful()
            ->isDeclined('call for')
            ->isComplete()
            ->hasCvdResult(CvdCode::N);
    }

    /**
     * The Moneris simulator does not support MasterCard CVD mismatch with
     * success, so there are fewer cases here than with Visa.
     *
     * @test
     */
    public function verifying_with_cvd_mastercard(): void
    {
        // Approved with CVD match //

        $v1 = new VerifyCard(
            orderId: $this->fixtures->orderId(),
            creditCard: new MasterCard(
                number: MasterCardSource::getApprovedMatch(CvdCode::M),
                expiry: '2012',
            ),
            cvdData: $this->fixtures->cvdData(),
        );

        $this->assert
            ->response($v1->submit($this->http(), $this->credentials()))
            ->isApproved()
            ->isSuccessful()
            ->isComplete()
            ->hasCvdResult(CvdCode::M);

        // Declined with CVD mismatch //

        $v2 = new VerifyCard(
            orderId: $this->fixtures->orderId(),
            creditCard: new MasterCard(
                number: MasterCardSource::getDeclinedMatch(CvdCode::N),
                expiry: '2012',
            ),
            cvdData: $this->fixtures->cvdData(),
        );

        $this->assert
            ->response($v2->submit($this->http(), $this->credentials()))
            ->isDeclined('call for')
            ->isUnsuccessful()
            ->isComplete()
            ->hasCvdResult(CvdCode::N);
    }

    /** @test */
    public function verifying_with_cvd_amex(): void
    {
        // Approved with CVD match //

        $v1Cvd = CvdCode::Y;
        $v1 = new VerifyCard(
            orderId: $this->fixtures->orderId(),
            creditCard: new Amex(
                number: AmexSource::getApprovedMatch($v1Cvd),
                expiry: '2012'
            ),
            cvdData: $this->fixtures->cvdData(),
        );

        $this->assert
            ->response($v1->submit($this->http(), $this->credentials()))
            ->isApproved()
            ->isSuccessful()
            ->isComplete()
            ->hasCvdResult($v1Cvd);

        // Approved with CVD mismatch
        $v2Cvd = CvdCode::N;
        $v2 = new VerifyCard(
            orderId: $this->fixtures->orderId(),
            creditCard: new Amex(
                number: AmexSource::getApprovedMatch($v2Cvd),
                expiry: '2012'
            ),
            cvdData: $this->fixtures->cvdData(),
        );

        $this->assert
            ->response($v2->submit($this->http(), $this->credentials()))
            ->isApproved()
            ->isSuccessful()
            ->isComplete()
            ->hasCvdResult($v2Cvd);

        // Declined with CVD mismatch //

        $v3Cvd = CvdCode::N;
        $v3 = new VerifyCard(
            orderId: $this->fixtures->orderId(),
            creditCard: new Amex(
                number: AmexSource::getDeclinedMatch($v3Cvd),
                expiry: '2012'
            ),
            cvdData: $this->fixtures->cvdData(),
        );

        $this->assert
            ->response($v3->submit($this->http(), $this->credentials()))
            ->isDeclined()
            ->isUnsuccessful()
            ->isComplete()
            ->hasCvdResult($v3Cvd);
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

        $verify = new VerifyCard(
            orderId: $this->fixtures->orderId(),
            creditCard: $creditCard,
            avsData: $avsData,
        );

        // 2023-09-21 According to the Moneris documentation this test credit
        // card should be returning an AVS code of 'D' but it is returning 'Y'.
        // This may change in the future, so if this test starts failing with
        // a non-matching code, that may be the reason.
        $this->assert
            ->response($verify->submit($this->http(), $this->avsCredentials()))
            ->isApproved()
            ->isSuccessful()
            ->isComplete()
            ->hasAvsResult(AvsCode::Y);
    }

    /** @test */
    public function verifying_with_avs_mastercard(): void
    {
        // Approved with AVS match //

        $avsCode = AvsCode::X;
        $creditCard = new MasterCard(
            MasterCardSource::getApprovedMatch(avs: $avsCode),
            $this->fixtures->expiry(),
        );

        $v1 = new VerifyCard(
            orderId: $this->fixtures->orderId(),
            creditCard: $creditCard,
            avsData: $this->fixtures->avsData(),
        );

        $this->assert
            ->response($v1->submit($this->http(), $this->avsCredentials()))
            ->isApproved()
            ->isSuccessful()
            ->isComplete()
            ->hasAvsResult($avsCode);

        // Declined with AVS mismatch //
        $avsCode = AvsCode::N;
        $creditCard = new MasterCard(
            MasterCardSource::getDeclinedMatch(avs: $avsCode),
            $this->fixtures->expiry(),
        );

        $v2 = new VerifyCard(
            orderId: $this->fixtures->orderId(),
            creditCard: $creditCard,
            avsData: $this->fixtures->avsData(),
        );

        $this->assert
            ->response($v2->submit($this->http(), $this->avsCredentials()))
            ->isDeclined('call for')
            ->isUnsuccessful()
            ->isComplete()
            ->hasAvsResult($avsCode);
    }

    /** @test */
    public function verifying_with_avs_amex(): void
    {
        $avsCode = AvsCode::Y;
        $creditCard = new Amex(
            AmexSource::getApprovedMatch(avs: $avsCode),
            $this->fixtures->expiry(),
        );

        $v1 = new VerifyCard(
            orderId: $this->fixtures->orderId(),
            creditCard: $creditCard,
            avsData: $this->fixtures->avsData(),
        );

        $this->assert
            ->response($v1->submit($this->http(), $this->avsCredentials()))
            ->isApproved()
            ->isSuccessful()
            ->isComplete()
            ->hasAvsResult($avsCode);
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
            $v1 = new VerifyCard(
                orderId: $this->fixtures->orderId(),
                creditCard: $creditCard,
                cvdData: $this->fixtures->cvdData(),
                avsData: $this->fixtures->avsData(),
                cofData: new CofVerificationData(),
            );

        $this->assert
            ->response($v1->submit($this->http(), $this->avsCredentials()))
            ->isApproved()
            ->isSuccessful()
            ->isComplete()
            ->hasCvdResult($cvdCode)
            ->hasAvsResult($avsCode)
            ->hasIssuerId($creditCard);
    }
}
