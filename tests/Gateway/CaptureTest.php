<?php

namespace CraigPaul\Moneris\Tests\Gateway;

use CraigPaul\Moneris\Data\Transactable\Capture;
use CraigPaul\Moneris\Data\Transactable\Preauth;
use CraigPaul\Moneris\Support\Cards\CardInterface;
use CraigPaul\Moneris\TestSupport\Cases\TestCase;

class CaptureTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider basicCardProvider
     */
    public function capturing(CardInterface $testCard): void
    {
        $amount = $this->fixtures->approvedAmount();
        $orderId = $this->fixtures->orderId();

        $preauthData = new Preauth(
            orderId: $orderId,
            creditCard: $testCard,
            amount: $amount,
        );

        $transactionId = $this->submit($preauthData)
            ->isSuccessful()
            ->isComplete()
            ->isApproved()
            ->getResponse()
            ->getTransactionId();

        $capture = new Capture(
            transactionId: $transactionId,
            orderId: $orderId,
            amount: $amount,
        );

        $this->submit($capture)
            ->isSuccessful()
            ->isComplete()
            ->isApproved();
    }
}
