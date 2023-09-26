<?php

namespace CraigPaul\Moneris\Tests\Gateway;

use CraigPaul\Moneris\Data\Transactable\CorrectionData;
use CraigPaul\Moneris\Data\Transactable\PurchaseData;
use CraigPaul\Moneris\Interfaces\CardInterface;
use CraigPaul\Moneris\TestSupport\Cases\TestCase;

class GatewayCorrectionTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider basicCardProvider
     */
    public function correcting(CardInterface $testCard): void
    {
        $amount = $this->approvedAmount();
        $orderId = $this->fixtures->orderId();

        $purchaseResponse = $this->gateway()->purchase(new PurchaseData(
            orderId: $orderId,
            creditCard: $testCard,
            amount: $amount,
        ));

        $this->assert->isSuccessful($purchaseResponse);
        $this->assert->isComplete($purchaseResponse);
        $this->assert->isApproved($purchaseResponse);

        $correctionResponse = $this->gateway()->correction(new CorrectionData(
            orderId: $orderId,
            transaction: $purchaseResponse->getTransaction(),
        ));

        $this->assert->isSuccessful($correctionResponse);
        $this->assert->isComplete($correctionResponse);
        $this->assert->isApproved($correctionResponse);
    }
}
