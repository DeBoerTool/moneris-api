<?php

namespace CraigPaul\Moneris\Tests\Gateway;

use CraigPaul\Moneris\Data\Transactable\Refund;
use CraigPaul\Moneris\Support\Cards\CardInterface;
use CraigPaul\Moneris\TestSupport\Cases\TestCase;

class RefundTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider basicCardProvider
     */
    public function refunding_a_purchase(CardInterface $testCard): void
    {
        $amount = $this->fixtures->approvedAmount();

        $ids = $this->purchase($testCard, $amount);

        $refund = new Refund(
            orderId: $ids->orderId,
            transactionId: $ids->transactionId,
            amount: $amount,
        );

        $this->submit($refund)
            ->isComplete()
            ->isSuccessful()
            ->isApproved();
    }

    /**
     * @test
     *
     * @dataProvider basicCardProvider
     */
    public function refunding_a_capture(CardInterface $testCard): void
    {
        $amount = $this->fixtures->approvedAmount();

        $ids = $this->preauthAndCapture($testCard, $amount);

        $refund = new Refund(
            orderId: $ids->orderId,
            transactionId: $ids->transactionId,
            amount: $amount,
        );

        $this->submit($refund)
            ->isComplete()
            ->isSuccessful()
            ->isApproved();
    }
}
