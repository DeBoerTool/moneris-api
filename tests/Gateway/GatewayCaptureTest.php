<?php

namespace CraigPaul\Moneris\Tests\Gateway;

use CraigPaul\Moneris\Data\Transactable\CaptureData;
use CraigPaul\Moneris\Data\Transactable\PreauthData;
use CraigPaul\Moneris\Interfaces\CardInterface;
use CraigPaul\Moneris\TestSupport\Cases\TestCase;

class GatewayCaptureTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider basicCardProvider
     */
    public function capturing(CardInterface $testCard): void
    {
        $amount = $this->approvedAmount();
        $orderId = $this->orderId();

        $preauthData = new PreauthData(
            orderId: $orderId,
            creditCard: $testCard,
            amount: $amount,
        );

        $preauthResponse = $this->gateway()->preauth($preauthData);

        $this->assert->isSuccessful($preauthResponse);
        $this->assert->isComplete($preauthResponse);
        $this->assert->isApproved($preauthResponse);

        $captureData = new CaptureData(
            transaction: $preauthResponse->getTransaction(),
            orderId: $orderId,
            amount: $amount,
        );

        $captureResponse = $this->gateway()->capture($captureData);

        $this->assert->isSuccessful($captureResponse);
        $this->assert->isComplete($captureResponse);
        $this->assert->isApproved($captureResponse);
    }
}
