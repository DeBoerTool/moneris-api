<?php

namespace CraigPaul\Moneris\Tests\Gateway;

use CraigPaul\Moneris\Data\Transactable\Purchase;
use CraigPaul\Moneris\Enums\AvsCode;
use CraigPaul\Moneris\Enums\CvdCode;
use CraigPaul\Moneris\Support\Cards\CardInterface;
use CraigPaul\Moneris\TestSupport\Cases\TestCase;
use CraigPaul\Moneris\Values\Amount;

class PurchaseTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider basicCardProvider
     */
    public function purchasing(CardInterface $testCard): void
    {
        $purchase = new Purchase(
            orderId: $this->fixtures->orderId(),
            creditCard: $testCard,
            amount: $this->fixtures->approvedAmount(),
        );

        $this->submit($purchase)
            ->isSuccessful()
            ->isComplete()
            ->isApproved();
    }

    /**
     * @test
     *
     * @dataProvider basicCardProvider
     */
    public function purchasing_with_customer(CardInterface $testCard): void
    {
        $purchase = new Purchase(
            orderId: $this->fixtures->orderId(),
            creditCard: $testCard,
            amount: $this->fixtures->approvedAmount(),
            customerData: $this->fixtures->customerData(),
        );

        /**
         * 2023-10-02 - Verified data is present in the Moneris test account.
         */
        $this->submit($purchase)
            ->isSuccessful()
            ->isComplete()
            ->isApproved();
    }

    /**
     * @test
     *
     * @dataProvider avsAndCvdPurchaseProvider
     */
    public function purchasing_with_cvd_and_avs(
        CardInterface $testCard,
        Amount $amount,
        CvdCode $cvdCode,
        AvsCode $avsCode,
    ): void {
        $purchase = new Purchase(
            orderId: $this->fixtures->orderId(),
            creditCard: $testCard,
            amount: $amount,
            cvdData: $this->fixtures->cvdData(),
            avsData: $this->fixtures->avsData(),
        );

        $this->submit($purchase)
            ->isSuccessful()
            ->isComplete()
            ->isApproved()
            ->hasCvdResult($cvdCode)
            ->hasAvsResult($avsCode);
    }
}
