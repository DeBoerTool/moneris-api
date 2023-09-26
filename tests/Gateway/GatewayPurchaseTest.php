<?php

namespace CraigPaul\Moneris\Tests\Gateway;

use CraigPaul\Moneris\Data\Transactable\PurchaseData;
use CraigPaul\Moneris\Enums\AvsCode;
use CraigPaul\Moneris\Enums\CvdCode;
use CraigPaul\Moneris\Interfaces\CardInterface;
use CraigPaul\Moneris\TestSupport\Cases\TestCase;
use CraigPaul\Moneris\Values\Amount;

class GatewayPurchaseTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider basicCardProvider
     */
    public function purchasing(CardInterface $testCard): void
    {
        $data = new PurchaseData(
            orderId: $this->orderId(),
            creditCard: $testCard,
            amount: $this->approvedAmount(),
        );

        $response = $this->gateway()->purchase($data);

        $this->assert->isSuccessful($response);
        $this->assert->isComplete($response);
        $this->assert->isApproved($response);
    }

    /**
     * @test
     *
     * @dataProvider basicCardProvider
     */
    public function purchasing_with_customer(CardInterface $testCard): void
    {
        $customerData = $this->fixtures->customerData();

        $data = new PurchaseData(
            orderId: $this->orderId(),
            creditCard: $testCard,
            amount: $this->approvedAmount(),
            customerDataData: $customerData,
        );

        $response = $this->gateway()->purchase($data);

        /**
         * 2023-09-25 - Verified customer information appears on the Moneris
         * test store.
         */
        $this->assert->isSuccessful($response);
        $this->assert->isComplete($response);
        $this->assert->isApproved($response);
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
        $avsData = $this->fixtures->avsData();
        $data = new PurchaseData(
            orderId: $this->orderId(),
            creditCard: $testCard,
            amount: $amount,
            cvdData: $this->fixtures->cvdData(),
            avsData: $avsData,
        );

        $response = $this->avsGateway()->purchase($data);

        $this->assert->isSuccessful($response);
        $this->assert->isComplete($response);
        $this->assert->isApproved($response);
        $this->assert->hasCvdResult($response, $cvdCode);
        $this->assert->hasAvsResult($response, $avsCode);
    }
}
