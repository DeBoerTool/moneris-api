<?php

namespace CraigPaul\Moneris\Tests\Gateway;

use CraigPaul\Moneris\Data\Transactable\PurchaseData;
use CraigPaul\Moneris\Data\Transactable\RefundData;
use CraigPaul\Moneris\Interfaces\CardInterface;
use CraigPaul\Moneris\TestSupport\Cases\TestCase;

class GatewayRefundTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider basicCardProvider
     */
    public function refunding(CardInterface $testCard): void
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

        $refundResponse = $this->gateway()->refund(new RefundData(
            orderId: $orderId,
            amount: $amount,
            transaction: $purchaseResponse->getTransaction(),
        ));

        $this->assert->isSuccessful($refundResponse);
        $this->assert->isComplete($refundResponse);
        $this->assert->isApproved($refundResponse);
    }
}
