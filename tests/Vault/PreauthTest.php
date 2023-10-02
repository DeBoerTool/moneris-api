<?php

namespace CraigPaul\Moneris\Tests\Vault;

use CraigPaul\Moneris\Data\Cof\CofPurchaseData;
use CraigPaul\Moneris\Enums\AvsCode;
use CraigPaul\Moneris\Enums\CvdCode;
use CraigPaul\Moneris\Transactables\VaultPreauth;
use CraigPaul\Moneris\Support\Cards\CardInterface;
use CraigPaul\Moneris\TestSupport\Cases\TestCase;
use CraigPaul\Moneris\Values\Amount;

class PreauthTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider basicCardProvider
     */
    public function vault_preauth(CardInterface $testCard): void
    {
        $issuerId = $this->verifyCard($testCard);
        $dataKey = $this->addCard($testCard, $issuerId);

        $preauth = new VaultPreauth(
            dataKey: $dataKey,
            orderId: $this->fixtures->orderId(),
            amount: $this->fixtures->approvedAmount(),
            cofData: new CofPurchaseData($issuerId),
        );

        $this->submit($preauth)
            ->isComplete()
            ->isSuccessful()
            ->isApproved();
    }

    /**
     * @test
     *
     * @dataProvider basicCardProvider
     */
    public function preauth_with_full_customer_data(CardInterface $testCard): void
    {
        $issuerId = $this->verifyCard($testCard);
        $dataKey = $this->addCard($testCard, $issuerId);
        $customerData = $this->fixtures->customerData();

        $preauth = new VaultPreauth(
            dataKey: $dataKey,
            orderId: $this->fixtures->orderId(),
            amount: $this->fixtures->approvedAmount(),
            cofData: new CofPurchaseData($issuerId),
            customerData: $customerData,
        );

        // 2023-09-20 - Verified data is present in the Moneris test account.
        $this->submit($preauth)
            ->isComplete()
            ->isSuccessful()
            ->isApproved();
    }

    /**
     * @test
     *
     * @dataProvider basicCardProvider
     */
    public function preauth_with_empty_customer_data(CardInterface $testCard): void
    {
        $issuerId = $this->verifyCard($testCard);
        $dataKey = $this->addCard($testCard, $issuerId);
        $customerData = $this->fixtures->customerDataEmpty();

        $preauth = new VaultPreauth(
            dataKey: $dataKey,
            orderId: $this->fixtures->orderId(),
            amount: $this->fixtures->approvedAmount(),
            cofData: new CofPurchaseData($issuerId),
            customerData: $customerData,
        );

        // 2023-09-20 - Verified data is present in the Moneris test account
        // with a fully empty data set.
        $this->submit($preauth)
            ->isComplete()
            ->isSuccessful()
            ->isApproved();
    }

    /**
     * @test
     *
     * @dataProvider basicCardProvider
     */
    public function preauth_with_minimal_customer_data(CardInterface $testCard): void
    {
        $issuerId = $this->verifyCard($testCard);
        $dataKey = $this->addCard($testCard, $issuerId);
        $customerData = $this->fixtures->customerDataMin();

        $preauth = new VaultPreauth(
            dataKey: $dataKey,
            orderId: $this->fixtures->orderId(),
            amount: $this->fixtures->approvedAmount(),
            cofData: new CofPurchaseData($issuerId),
            customerData: $customerData,
        );

        // 2023-09-20 - Verified data is present in the Moneris test account
        // with a fully empty data set.
        $this->submit($preauth)
            ->isComplete()
            ->isSuccessful()
            ->isApproved();
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
        $customerData = $this->fixtures->customerData();
        $issuerId = $this->verifyCard($testCard, $avsData);
        $dataKey = $this->addCard($testCard, $issuerId, $avsData);

        $preauth = new VaultPreauth(
            dataKey: $dataKey,
            orderId: $this->fixtures->orderId(),
            amount: $amount,
            cofData: new CofPurchaseData($issuerId),
            customerData: $customerData,
            cvdData: $this->fixtures->cvdData(),
            avsData: $avsData,
        );

        $this->submit($preauth, avs: true)
            ->isSuccessful()
            ->isComplete()
            ->isApproved()
            ->hasCvdResult($cvdCode)
            ->hasAvsResult($avsCode);
    }
}
