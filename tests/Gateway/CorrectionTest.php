<?php

namespace CraigPaul\Moneris\Tests\Gateway;

use CraigPaul\Moneris\Data\Transactable\Correction;
use CraigPaul\Moneris\Support\Cards\CardInterface;
use CraigPaul\Moneris\TestSupport\Cases\TestCase;

class CorrectionTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider basicCardProvider
     */
    public function correcting(CardInterface $testCard): void
    {
        $amount = $this->fixtures->approvedAmount();

        $ids = $this->purchase($testCard, $amount);

        $correction = new Correction(
            orderId: $ids->orderId,
            transaction: $ids->transactionId,
        );

        $this->submit($correction)
            ->isComplete()
            ->isSuccessful()
            ->isApproved();
    }
}
