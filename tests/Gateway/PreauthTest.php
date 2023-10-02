<?php

namespace CraigPaul\Moneris\Tests\Gateway;

use CraigPaul\Moneris\Data\Transactable\Capture;
use CraigPaul\Moneris\Data\Transactable\Preauth;
use CraigPaul\Moneris\Enums\AvsCode;
use CraigPaul\Moneris\Enums\CvdCode;
use CraigPaul\Moneris\Support\Cards\CardInterface;
use CraigPaul\Moneris\TestSupport\Cases\TestCase;
use CraigPaul\Moneris\Values\Amount;

class PreauthTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider basicCardProvider
     */
    public function preauthorizing(CardInterface $testCard): void
    {
        $preauth = new Preauth(
            orderId: $this->fixtures->orderId(),
            creditCard: $testCard,
            amount: $this->fixtures->approvedAmount(),
        );

        $this->submit($preauth)
            ->isSuccessful()
            ->isComplete()
            ->isApproved();
    }

    /**
     * @test
     *
     * @dataProvider basicCardProvider
     */
    public function preauth_with_customer_id(CardInterface $testCard): void
    {
        $preauth = new Preauth(
            orderId: $this->fixtures->orderId(),
            creditCard: $testCard,
            amount: $this->fixtures->approvedAmount(),
            customerData: $this->fixtures->customerData(),
        );

        /**
         * 2023-10-02 - Verified customer information appears on the Moneris
         * test store.
         */
        $this->submit($preauth)
            ->isSuccessful()
            ->isComplete()
            ->isApproved();
    }

    /**
     * @test
     *
     * @dataProvider avsAndCvdPurchaseProvider
     */
    public function preauth_with_cvd_and_avs(
        CardInterface $testCard,
        Amount $amount,
        CvdCode $cvdCode,
        AvsCode $avsCode,
    ): void {
        $preauth = new Preauth(
            orderId: $this->fixtures->orderId(),
            creditCard: $testCard,
            amount: $amount,
            cvdData: $this->fixtures->cvdData(),
            avsData: $this->fixtures->avsData(),
        );

        $this->submit($preauth)
            ->isSuccessful()
            ->isComplete()
            ->isApproved()
            ->hasCvdResult($cvdCode)
            ->hasAvsResult($avsCode);
    }
}
