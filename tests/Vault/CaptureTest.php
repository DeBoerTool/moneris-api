<?php

namespace CraigPaul\Moneris\Tests\Vault;

use CraigPaul\Moneris\Data\Transactable\Capture;
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
        $issuerId = $this->verifyCard($testCard);
        $dataKey = $this->addCard($testCard, $issuerId);
        $captureData = $this->vaultPreauth($dataKey, $issuerId, $amount);

        $capture = new Capture(
            transactionId: $captureData->transactionId,
            orderId: $captureData->orderId,
            amount: $amount,
        );

        $this->submit($capture)
            ->isSuccessful()
            ->isComplete()
            ->isApproved();
    }
}
