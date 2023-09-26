<?php

namespace CraigPaul\Moneris\Tests\Gateway;

use CraigPaul\Moneris\Data\Transactable\PreauthData;
use CraigPaul\Moneris\Enums\AvsCode;
use CraigPaul\Moneris\Enums\CvdCode;
use CraigPaul\Moneris\Interfaces\CardInterface;
use CraigPaul\Moneris\TestSupport\Cases\TestCase;
use CraigPaul\Moneris\Values\Amount;

class GatewayPreauthTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider basicCardProvider
     */
    public function preauthorizing(CardInterface $testCard): void
    {
        $data = new PreauthData(
            orderId: $this->orderId(),
            creditCard: $testCard,
            amount: $this->approvedAmount(),
        );

        $response = $this->gateway()->preauth($data);

        $this->assert->isSuccessful($response);
        $this->assert->isComplete($response);
        $this->assert->isApproved($response);
    }

    /**
     * @test
     *
     * @dataProvider basicCardProvider
     */
    public function preauth_with_customer(CardInterface $testCard): void
    {
        $customerData = $this->fixtures->customerData();

        $data = new PreauthData(
            orderId: $this->orderId(),
            creditCard: $testCard,
            amount: $this->approvedAmount(),
            customerDataData: $customerData,
        );

        $response = $this->gateway()->preauth($data);

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
    public function preauth_with_cvd(
        CardInterface $testCard,
        Amount $amount,
        CvdCode $cvdCode,
    ): void {
        $data = new PreauthData(
            orderId: $this->orderId(),
            creditCard: $testCard,
            amount: $amount,
            cvdData: $this->fixtures->cvdData(),
        );

        $response = $this->gateway()->preauth($data);

        $this->assert->isSuccessful($response);
        $this->assert->isComplete($response);
        $this->assert->isApproved($response);
        $this->assert->hasCvdResult($response, $cvdCode);
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
        $avsData = $this->fixtures->avsData();
        $data = new PreauthData(
            orderId: $this->orderId(),
            creditCard: $testCard,
            amount: $amount,
            cvdData: $this->fixtures->cvdData(),
            avsData: $avsData,
        );

        $response = $this->avsGateway()->preauth($data);

        $this->assert->isSuccessful($response);
        $this->assert->isComplete($response);
        $this->assert->isApproved($response);
        $this->assert->hasCvdResult($response, $cvdCode);
        $this->assert->hasAvsResult($response, $avsCode);
    }
}
